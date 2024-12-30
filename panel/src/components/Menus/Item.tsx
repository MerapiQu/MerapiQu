import React from "react";
import { isMobile, useApp } from "../../app";
import { useSidebar } from "../Sidebars/Sidebar";

export interface MenuItemProps {
  title: string;
  icon: string;
  path: string;
}

const Item = ({ title, icon, path }: MenuItemProps) => {
  const { setExpand } = useSidebar();
  const { currentPath, setCurrentPath } = useApp();
  const isActive =
    currentPath.replace(/^\/|\/$/g, "") == path.replace(/^\/|\/$/g, "");

  const handleClick = () => {
    setExpand(!isMobile());
    setCurrentPath(path);
  };

  return (
    <div
      className={`menu-item${isActive ? " active" : ""}`}
      onClick={handleClick}
      style={{ cursor: "pointer" }}
    >
      <i className={`fa ${icon}`}></i> <span>{title}</span>
    </div>
  );
};

export default Item;
