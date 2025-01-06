import React from "react";
import {
  BlockManager,
  LayerManager,
  PageManager,
  Panel,
} from "@merapipanel/core/editor";
import { Tab, TabContainer } from "@merapipanel/core/panels";

interface SidebarProps {
  expand: boolean;
}

const Sidebar: React.FC<SidebarProps> = ({ expand }) => {
  return (
    <>
      <Panel
        id="sidebar"
        className={`editor-sidebar${expand ? " show" : " hide"}`}
        aria-expanded={expand}
        resizable={{
          maxDim: 350,
          minDim: 250,
          tc: false,
          cl: false,
          cr: true,
          bc: false,
          keyWidth: "flex-basis",
        }}
      >
        <TabContainer className="editor-sidebar-container">
          <Tab
            icon={<i className="fa-solid fa-circle-info"></i>}
            label="Essentials"
          >
            <div>
              <div className="d-flex justify-content-between align-items-center mb-2 pt-3">
                <div className="text-primary me-2">
                  <i className="fa-solid fa-file"></i>
                </div>
                <span className="fw-bold">Pages</span>
                <button className="btn ms-auto" aria-label="Add new page">
                  <i className="fa fa-circle-plus fs-5" aria-hidden="true"></i>
                </button>
              </div>
              <PageManager />
            </div>
            <div>
              <div className="d-flex align-items-center mb-2 pt-3">
                <div className="text-primary me-2">
                  <i className="fa-solid fa-layer-group"></i>
                </div>
                <span className="fw-bold">Layers</span>
              </div>
              <LayerManager />
            </div>
          </Tab>
          <Tab
            icon={<i className="fa-solid fa-swatchbook"></i>}
            label="Patterns"
          >
            <BlockManager include={["pattern"]} />
          </Tab>
          <Tab icon={<i className="fa-solid fa-cube"></i>} label="Blocks">
            <BlockManager exclude={["pattern"]} />
          </Tab>
        </TabContainer>
      </Panel>

      {/* <div className="container panel-editor-one-bg panel-editor-two-color">
          
        </div> */}
    </>
  );
};

export default Sidebar;
