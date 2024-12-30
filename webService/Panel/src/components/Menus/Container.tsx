import React from "react";
import Menu, { MenuProps } from "./Menu";

const Container = ({ listMenu }: { listMenu: MenuProps[] }) => {

  return <ul className="menu-container">
    { listMenu.map( (menu, i) => <Menu {...menu} key={i} />) }
  </ul>;
};
export default Container;
