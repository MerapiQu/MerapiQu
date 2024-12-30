import React, { useRef } from "react";
import { useWidget, WidgetOption } from "../Widget";
import Handle from "./Handle";

const ToolContainer = () => {
  const { getWidth, getHeight, setOption, parent } = useWidget();
  const width = getWidth();
  const height = getHeight();
  const elementRef = useRef<HTMLDivElement>(null);

  const handleResizeX = (x: number, y: number) => {
    let newWidth = width + x;
    if (newWidth > 100) return;
    if (newWidth < 0) return;
    setOption((curr) => ({ ...curr, width: `${newWidth}%` }));
  };

  const handleResizeY = (x: number, y: number) => {
    const parentHeight = parent?.offsetHeight || 100;
    let newHeight = height + y;
    newHeight = (newHeight / 100) * parentHeight;
    newHeight = Math.floor(newHeight);
    if (newHeight > window.screen.height / 2) return;
    setOption((curr) => ({ ...curr, height: `${newHeight}px` }));
  };

  return (
    <div className="editing-tools" ref={elementRef}>
      {/* Horizontal Resize Handle */}
      <Handle className="resize-x" onMove={handleResizeX}>
        <i className="fa-solid fa-left-right"></i>
      </Handle>

      {/* Vertical Resize Handle */}
      <Handle className="resize-y" onMove={handleResizeY}>
        <i className="fa-solid fa-up-down"></i>
      </Handle>
    </div>
  );
};

export default ToolContainer;
