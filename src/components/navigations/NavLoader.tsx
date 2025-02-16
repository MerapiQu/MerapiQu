import React, {
  forwardRef,
  useCallback,
  useEffect,
  useMemo,
  useRef,
  useState,
} from "react";
import NavFragment, { NavFragmentRef } from "./NavFragment";
import {
  Box,
  CircularProgress,
  Slide,
  Snackbar,
  Typography,
  useColorScheme,
} from "@mui/material";
import { IResponse } from "@/components/models/IResponse";
import { IModule, INavigationContent } from "@/components/models/INavFragment";
import { useNavController } from "./NavController";

interface NavLoaderProps {
  children: React.ReactNode;
  onStartLoad: () => void;
  onLoaded: (data: IResponse<INavigationContent>) => void;
  onError: (error: Error) => void;
}

const loadFragmentModule = (module: IModule) => {
  return new Promise<React.ElementType | null>((resolve, reject) => {
    const name: string = module.name;
    const src = typeof module === "string" ? module : module.src;

    import(/* webpackIgnore: true */ src)
      .then(() => {
        const component = name.split(".").reduce((prev, curr) => {
          return prev[curr];
        }, window as any);
        resolve(component.default || null);
      })
      .catch((error) => {
        reject(error);
      });
  });
};

const NavLoader = forwardRef<NavFragmentRef | undefined | null, NavLoaderProps>(
  ({ children, onLoaded, onError, onStartLoad }, forwardedRef) => {
    const { path } = useNavController();
    const [loading, setLoading] = useState(true);
    const fragmentRef = useRef<NavFragmentRef>();
    const { mode, systemMode } = useColorScheme();
    const isDark = mode === "dark" || systemMode === "dark";
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

    const loadPage = useCallback((path: string) => {
      console.clear();
      onStartLoad();
      setLoading(true);
      fetch(path, {
        method: "GET",
        headers: {
          "Content-Type": "application/json",
          Accept: "application/json",
        },
      })
        .then((response) => {
          return response.json();
        })
        .then(async (data: IResponse<INavigationContent>) => {
          onLoaded(data);
          if (typeof data.data.module == "object") {
            const component = await loadFragmentModule(data.data.module);
            if (component && fragmentRef.current) {
              fragmentRef.current.setContent(
                React.createElement(component, { content: data.data })
              );
            }
            setTimeout(() => setLoading(false), 400);
          }
          if (data.data.title) {
            fragmentRef.current?.setTitle(data.data.title);
          }
        })
        .catch((error) => {
          onError(error);
          console.error("Failed to load page:", error);
          fragmentRef.current?.setContent(
            <Box sx={{ py: 15 }}>
              <Typography variant="h1" color="error" sx={{ mb: 2 }}>
                Caught an Error
              </Typography>
              <Typography variant="body1">
                {error.message || "An error occurred while loading the page."}
              </Typography>
            </Box>
          );
          setLoading(false);
        });
    }, []);

    useEffect(() => loadPage(path), [path, loadPage]);

    return (
      <>
        {modifiedChildren}
        <Snackbar
          open={loading}
          anchorOrigin={{ vertical: "bottom", horizontal: "center" }}
          transitionDuration={100}
          TransitionComponent={(props) => <Slide {...props} direction="up" />}
          sx={{
            "& .MuiSnackbarContent-root": {
              minWidth: "0",
              backdropFilter: "blur(5px)",
              background: `rgba(${
                isDark ? "80, 80, 80" : "255, 255, 255"
              }, 0.3)`,
              borderRadius: "10rem",
              maxWidth: "fit-content",
            },
          }}
          message={
            <Box sx={{ display: "flex", alignItems: "center" }}>
              <CircularProgress size={18} />
              <Typography
                sx={{ ml: 2, color: isDark ? "white" : "black" }}
                variant="body2"
              >
                Loading...
              </Typography>
            </Box>
          }
        />
      </>
    );
  }
);

NavLoader.displayName = "NavLoader";

export default NavLoader;
