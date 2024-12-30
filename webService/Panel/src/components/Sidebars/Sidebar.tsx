import React, { createContext, useContext, useState } from "react";
import MenuContainer from "../Menus/Container";
import Header from "./Header";
import { MenuProps } from "../Menus/Menu";
import { isMobile } from "../../app";

interface SidebarStore {
  expand: boolean;
  setExpand: (expand: boolean) => void;
}

const SidebarContext = createContext<SidebarStore>({} as any);
export const useSidebar = () => useContext(SidebarContext);

const Sidebar = () => {
  const [expand, setExpand] = useState(!isMobile());
  window.addEventListener("resize", () => {
    setExpand(!isMobile());
  });

  const toggleExpand = () => {
    setExpand(!expand);
  };

  const payload = {
    expand,
    setExpand,
  };

  const listMenu: MenuProps[] = [
    {
      title: "Dashboard",
      icon: "fa-home",
      path: "/panel/admin/dashboard/",
    },
    {
      title: "Settings",
      icon: "fa-cog",
      path: "/panel/admin/dashboard/settings",
    },
  ];

  return (
    <SidebarContext.Provider value={payload}>
      <div className={`sidebar ${expand ? "expand" : "collapsed"}`}>
        <Header>
          {expand && (
            <>
              <button className="btn" onClick={toggleExpand}>
                <i className="fa fa-xmark"></i>
              </button>
              MerapiPanel
            </>
          )}
          {!expand && (
            <button className="btn" onClick={toggleExpand}>
              <i className="fa fa-bars"></i>
            </button>
          )}
        </Header>
        <MenuContainer listMenu={listMenu} />
      </div>
    </SidebarContext.Provider>
  );
};

export default Sidebar;
