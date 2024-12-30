import React, { useRef, useEffect, useState } from "react";
import { useWidget } from "../Widget";

interface HandleProps {
  children: React.ReactNode;
  className: string;
  onMove?: (x: number, y: number) => void;
  onClick?: () => void;
}

const calculateOffsetTop = (element: HTMLElement) => {
  let offset = 0;
  while (element) {
    offset += element.offsetTop;
    element = element.offsetParent as HTMLElement;
  }
  return offset;
};
const calculateOffsetLeft = (element: HTMLElement) => {
  let offset = 0;
  while (element) {
    offset += element.offsetLeft;
    element = element.offsetParent as HTMLElement;
  }
  return offset;
};

const Handle = ({ children, className, onMove, onClick }: HandleProps) => {
  const [isInsetX, setIsInsetX] = useState(false);
  const [isInsetY, setIsInsetY] = useState(false);

  const { parent, getWidth, getHeight, option } = useWidget();
  const pressing = useRef(false);
  const elementRef = useRef<HTMLDivElement>(null);

  const mousedownHandler = (event: React.MouseEvent<HTMLDivElement>) => {
    if (onMove) {
      pressing.current = true;
      elementRef.current?.classList.add("focus");


      const handleMouseMove = (moveEvent: MouseEvent) => {
        if (pressing.current && parent) {
          const offsetLeft = calculateOffsetLeft(parent);
          const offsetTop = calculateOffsetTop(parent);

          const parentWidth = parent.offsetWidth || 100;
          const parentHeight = parent.offsetHeight || 100;
          const maxOffsetX = offsetLeft + parentWidth;
          const maxOffsetY = offsetTop + parentHeight;

          const [startX, startY] = [event.clientX, event.clientY];
          const [moveX, moveY] = [moveEvent.clientX, moveEvent.clientY];

          setIsInsetX(moveX >= maxOffsetX - 60); // 60 is Handler width
          setIsInsetY(moveY >= maxOffsetY - 60);

          const [offsetX, offsetY] = [moveX - startX, moveY - startY];

          const x = Math.floor((offsetX / parentWidth) * 100);
          const y = Math.floor((offsetY / parentHeight) * 100);

          onMove(x, y);
        }
      };

      const handleMouseUp = () => {
        pressing.current = false;
        elementRef.current?.classList.remove("focus");
        document.removeEventListener("mousemove", handleMouseMove);
        document.removeEventListener("mouseup", handleMouseUp);
      };

      document.addEventListener("mousemove", handleMouseMove);
      document.addEventListener("mouseup", handleMouseUp);
    }
  };

  useEffect(() => {
    return () => {
      pressing.current = false;
      // Cleanup listeners if component unmounts during drag
      document.removeEventListener("mousemove", () => {});
      document.removeEventListener("mouseup", () => {});
    };
  }, []);

  const clickHandler = () => {
    if (onClick) {
      onClick();
    }
  };

  useEffect(() => {
    const element = elementRef.current;
    if (isInsetX) {
      element?.classList.add("inset-x");
    } else {
      element?.classList.remove("inset-x");
    }
    if (isInsetY) {
      element?.classList.add("inset-y");
    } else {
      element?.classList.remove("inset-y");
    }
  }, [isInsetX, isInsetY]);

  useEffect(() => {
    const element = elementRef.current;
    if (element) {
      const parentWidth = parent?.clientWidth || 100;
      const parentHeight = parent?.offsetHeight || 100;

      const parentOffsetWidth = calculateOffsetLeft(parent!);
      const parentOffsetHeight = calculateOffsetTop(parent!);

      const maxOffsetX = parentOffsetWidth + parentWidth;
      const maxOffsetY = parentOffsetHeight + parentHeight;

      const elementOffsetLeft = calculateOffsetLeft(element) + 60;
      const elementOffsetTop = calculateOffsetTop(element) + 60;

      if (elementOffsetLeft >= maxOffsetX) {
        setIsInsetX(true);
      } else {
        setIsInsetX(false);
      }

      if (elementOffsetTop >= maxOffsetY) {
        setIsInsetY(true);
      } else {
        setIsInsetY(false);
      }
    }
  }, [elementRef]);

  return (
    <div
      ref={elementRef}
      className={`tool-handle ${className}`}
      onMouseDown={onMove ? mousedownHandler : undefined}
      onClick={onClick ? clickHandler : undefined}
      role="button"
      aria-label="Handle tool"
      tabIndex={0} // Make it focusable for accessibility
    >
      {children}
    </div>
  );
};

export default Handle;
