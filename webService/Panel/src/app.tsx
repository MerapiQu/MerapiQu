import React, { createContext, useContext, useEffect, useState } from "react";
import Wrapper from "./components/Container/Wrapper";
import Sidebar from "./components/Sidebars/Sidebar";
import "./styles.scss";

export const isMobile = () => {
  return window.innerWidth < 768;
};

interface AppStrore {
  currentPath: string;
  setCurrentPath: (path: string) => void;
}

const AppContext = createContext<AppStrore>({} as any);
export const useApp = () => useContext(AppContext);

const App = () => {
  const [currentPath, setCurrentPath] = useState(window.location.pathname);

  const payload = {
    currentPath,
    setCurrentPath,
  };

  useEffect(() => {
    updateSilentPath();
  }, [currentPath]);

  useEffect(() => {
    window.addEventListener("popstate", updateSilentPath);
    return () => {
      window.removeEventListener("popstate", updateSilentPath);
    };
  }, []);

  const updateSilentPath = () => {
    window.history.pushState({}, "", currentPath);
  };

  return (
    <AppContext.Provider value={payload}>
      <Sidebar />
      <Wrapper />
    </AppContext.Provider>
  );
};

export default App;
