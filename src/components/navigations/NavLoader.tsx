/* eslint-disable @typescript-eslint/no-explicit-any */
import React, {
  forwardRef,
  useCallback,
  useEffect,
  useMemo,
  useRef,
} from "react";
import NavFragment, { NavFragmentRef } from "./NavFragment";
import { Box, Typography } from "@mui/material";
import { IModule, INavigationContent } from "@/interfaces/INavFragment";
import { useNavController } from "./NavController";
import { NavigationResponse } from "@/interfaces/Navigation";
import BlockContainer from "../blocknode/BlockContainer";

const NavigationHeader = {
  method: "GET",
  headers: {
    "Content-Type": "application/json",
    Accept: "application/json",
  },
};

interface NavLoaderProps {
  children: React.ReactNode;
  onStartLoad: () => void;
  onLoaded: (data: NavigationResponse) => void;
  onError: (error: Error) => void;
}

// const loadFragmentModule = (module: IModule) => {
//   return new Promise<React.ElementType | null>((resolve, reject) => {
//     const name: string = module.name;
//     const src = typeof module === "string" ? module : module.src;

//     import(/* webpackIgnore: true */ src)
//       .then(() => {
//         const component = name.split(".").reduce((prev, curr) => prev?.[curr], window as any);
//         resolve(component?.default || null);
//       })
//       .catch(reject);
//   });
// };

const NavLoader = forwardRef<NavFragmentRef | undefined | null, NavLoaderProps>(
  ({ children, onLoaded, onError, onStartLoad }, forwardedRef) => {
    const isFetching = useRef(false);
    const { path } = useNavController();
    const fragmentRef = useRef<NavFragmentRef>();

    const modifiedChildren = useMemo(() => {
      function mapChildren(children: React.ReactNode): React.ReactNode {
        return React.Children.map(children, (child) => {
          if (!React.isValidElement(child)) return child;

          if (child.type === NavFragment) {
            return React.cloneElement(child, {
              ...child.props,
              ref: (ref: any) => {
                fragmentRef.current = ref;
                if (forwardedRef && typeof forwardedRef === "object") {
                  forwardedRef.current = ref;
                }
              },
            });
          }
          if (child.props?.children) {
            return React.cloneElement(child, {
              children: mapChildren(child.props.children),
            } as any);
          }
          return child;
        });
      }
      return mapChildren(children);
    }, [children]);

    const loadPage = useCallback(
      async (path: string) => {
        if (isFetching.current) return;
        console.clear();
        onStartLoad();
        isFetching.current = true;
        try {
          const response = await fetch(path, NavigationHeader);
          const result: NavigationResponse = await response.json();
          onLoaded(result);

          if (!result.status) throw new Error(result.message);

          if (fragmentRef.current) {
            fragmentRef.current.setContent(<BlockContainer {...result.data} />);
          }

          if (result.data.title) {
            fragmentRef.current?.setTitle(result.data.title);
          }
        } catch (error) {
          onError(error as Error);
          fragmentRef.current?.setContent(
            <Box sx={{ py: 15 }}>
              <Typography variant="h1" color="error" sx={{ mb: 2 }}>
                Caught an Error
              </Typography>
              <Typography variant="body1">
                {(error as Error).message ||
                  "An error occurred while loading the page."}
              </Typography>
            </Box>
          );
        } finally {
          setTimeout(() => (isFetching.current = false), 150);
        }
      },
      [onLoaded, onError, onStartLoad]
    );

    useEffect(() => {
      loadPage(path);
    }, [path, loadPage]);

    return <>{modifiedChildren}</>;
  }
);

NavLoader.displayName = "NavLoader";

export default NavLoader;
