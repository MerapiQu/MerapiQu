import React from "react";
import "./styles.scss";
import { Menu, MenuContainer, Panel, SidebarHeader, useNav } from "@merapipanel/core/panels";

const App = () => {

  return (
    <Panel>
      <SidebarHeader>
        Merapi Panel
      </SidebarHeader>
      <MenuContainer>
        <Menu link="/panel/admin/dashboard" icon="fa-home" label="Dashboard" />
        <Menu link="/panel/admin/dashboard/pages" icon="fa-table-columns" label="Pages" />
        <Menu link="/panel/admin/dashboard/users" icon="fa-users" label="Users" />
        <Menu link="/panel/admin/dashboard/settings" icon="fa-cog" label="Settings" />
        <Menu link="/panel/admin/dashboard/help" icon="fa-question-circle" label="Help" />
      </MenuContainer>
    </Panel>
  );
};

export default App;
