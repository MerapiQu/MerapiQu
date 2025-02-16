import React, {
  forwardRef,
  useEffect,
  useImperativeHandle,
  useRef,
  useState,
} from "react";
import { Fade } from "@mui/material";

interface NavFragmentProps {
  children: React.ReactNode;
}

export interface NavFragmentRef {
  element: HTMLDivElement | null;
  setContent: (component?: React.ReactNode) => void;
  setTitle: (title?: string) => void;
}

const NavFragment = forwardRef<NavFragmentRef, NavFragmentProps>(
  ({ children }, ref) => {
    const divRef = useRef<HTMLDivElement>(null);
    const [title, setTitle] = useState<string | null>(null);
    const [component, setComponent] = useState<React.ReactNode>(children);
    const [showContent, setShowContent] = useState(true);

    useImperativeHandle(ref, () => ({
      element: divRef.current,
      setContent: (newComponent?: React.ReactNode) => {
        setShowContent(false);
        setTimeout(() => {
          setComponent(newComponent);
          setShowContent(true);
        }, 150);
      },
      setTitle: (newTitle?: string) => setTitle(newTitle || ""),
    }));

    useEffect(() => {
      document.title = title || "";
    }, [title]);

    useEffect(() => {
      if (!divRef.current) return;
      divRef.current.scrollTo({ top: 0, left: 0, behavior: "smooth" });
    }, [component]);

    return (
      <div className="nav-fragment" ref={divRef}>
        <Fade in={showContent} timeout={150} mountOnEnter unmountOnExit>
          <div className="nav-content-wrapper">{component}</div>
        </Fade>
      </div>
    );
  }
);

NavFragment.displayName = "NavFragment";
export default NavFragment;
