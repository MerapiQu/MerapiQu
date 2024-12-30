import React from "react";
import Item, { MenuItemProps } from "./Item";

export interface MenuProps extends MenuItemProps {
  children?: MenuProps[];
}

const Menu = ({ title, icon, path, children }: MenuProps) => {
  return (
    <li className="menu">
      <Item title={title} icon={icon} path={path} />
    </li>
  );
};
export default Menu;
