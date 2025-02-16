import React, {
  createContext,
  useContext,
  useEffect,
  useRef,
  useState,
} from "react";
import { INavigationContent } from "@/components/models/INavFragment";
import NavLoader from "./NavLoader";
import { IResponse } from "@/components/models/IResponse";
import { NavFragmentRef } from "./NavFragment";

export interface NavHostProps {
  navigate: (path: string) => void;
  path: string;
  error?: Error;
  fragmentRef: React.MutableRefObject<NavFragmentRef | undefined> | null;
  addEventListener: (
    event: "loaded" | "error" | "navigate" | "popstate",
    listener: () => void
  ) => void;
  removeEventListener: (
    event: "loaded" | "error" | "navigate" | "popstate",
    listener: () => void
  ) => void;
}

export interface NavControllerProps {
  children: React.ReactNode;
}

export interface NavEventProps {
  loaded?: ((data: IResponse<INavigationContent>) => void)[];
  error?: ((error: Error) => void)[];
  navigate?: ((url: string) => void)[];
  popstate?: (() => void)[];
}

const NavHost = createContext<NavHostProps>({
  path: "/",
  fragmentRef: null,
  navigate: () => {},
  addEventListener: () => {},
  removeEventListener: () => {},
});

export const useNavController = () => useContext(NavHost);

const NavController = ({ children }: NavControllerProps) => {
  const fragmentRef = useRef<NavFragmentRef>();
  const [path, setPath] = useState(window.location.pathname);
  const [onListeners, setOnListeners] = useState<NavEventProps>({});

  const addEventListener = (
    event: "loaded" | "error" | "navigate" | "popstate",
    listener: () => void
  ) => {
    const listeners = onListeners[event] || [];
    listeners.push(listener);
    setOnListeners({ ...onListeners, [event]: listeners });
  };

  const removeEventListener = (
    event: "loaded" | "error" | "navigate" | "popstate",
    listener: () => void
  ) => {
    const listeners = onListeners[event] || [];
    const newListeners = listeners.filter((l) => l !== listener);
    setOnListeners({ ...onListeners, [event]: newListeners });
  };

  const dispatchEvents = (
    event: "loaded" | "error" | "navigate" | "popstate",
    data?: any
  ) => {
    const listeners = onListeners[event] || [];
    listeners.forEach((listener) => listener(data));
  };

  const handlePopState = () => {
    setPath(window.location.pathname);
    dispatchEvents("popstate");
  };

  useEffect(() => {
    window.addEventListener("popstate", handlePopState);
    return () => {
      window.removeEventListener("popstate", handlePopState);
    };
  }, []);

  useEffect(() => {
    window.history.replaceState({}, "", path);
  }, [path]);

  const stores = {
    path,
    fragmentRef,
    navigate: (newPath: string) => {
      dispatchEvents("navigate", newPath);
      setPath(newPath);
      window.history.pushState({}, "", newPath);
    },
    addEventListener,
    removeEventListener,
  };

  return (
    <NavHost.Provider value={stores}>
      <NavLoader
        ref={fragmentRef}
        onStartLoad={() => {
          dispatchEvents("loaded");
        }}
        onLoaded={(data: IResponse<INavigationContent>) => {
          dispatchEvents("loaded", data);
        }}
        onError={(error: Error) => {
          dispatchEvents("error", error);
        }}
      >
        {children}
      </NavLoader>
    </NavHost.Provider>
  );
};

export default NavController;
