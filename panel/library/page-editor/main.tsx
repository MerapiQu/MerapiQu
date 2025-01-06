import React, { useEffect, useState } from "react";
import "./styles.scss";
import { Loading, Tab, TabContainer, useNav } from "@merapipanel/core/panels";
import {
  Editor,
  Grapes,
  Panel,
  Button,
  StylesManager,
  TraitManager,
  Canvas,
} from "@merapipanel/core/editor";
import Sidebar from "./components/Sidebar";


const devices = [
  {
    id: "mobile",
    name: "Mobile",
    width: "500px",
  },
  {
    id: "tablet",
    name: "Tablet",
    width: "900px",
  },
  {
    id: "desktop",
    name: "Desktop",
    width: "1200px",
  },
];

const PageEditor = () => {
  const [expandSidebar, setExpandSidebar] = useState(true);
  const [loading, setLoading] = useState(true);

  const onEditor = (editor: Grapes) => {
    devices.forEach((device) => {
      editor.DeviceManager.add(device);
    });

    setTimeout(() => {
      setLoading(false);
    }, 1000);
  };

  return (
    <>
      <Editor onEditor={onEditor}>
        <div className="editor-wrapper">
          <Sidebar expand={expandSidebar} />
          <div className="editor-container">
            <div className="editor-header panel-editor-one-bg panel-editor-two-color">
              <Button command={() => setExpandSidebar(!expandSidebar)}>
                {!expandSidebar && (
                  <i className="fa-solid fa-chevron-right"></i>
                )}
                {expandSidebar && <i className="fa-solid fa-chevron-left"></i>}
              </Button>

              <Panel id="devices">
                <Button
                  id="mobile"
                  context="devices"
                  active={true}
                  command={{
                    run(editor) {
                      editor.DeviceManager.select("mobile");
                    },
                    stop() {},
                  }}
                >
                  <i className="fa-solid fa-mobile"></i>
                </Button>
                <Button
                  id="tablet"
                  context="devices"
                  command={{
                    run(editor) {
                      editor.DeviceManager.select("tablet");
                    },
                    stop() {},
                  }}
                >
                  <i className="fa-solid fa-tablet"></i>
                </Button>
                <Button
                  id="desktop"
                  context="devices"
                  command={{
                    run(editor) {
                      editor.DeviceManager.select("desktop");
                    },
                    stop() {},
                  }}
                >
                  <i className="fa-solid fa-desktop"></i>
                </Button>
              </Panel>

              <Panel id="tools">
                <Button id="expand">
                  <i className="fa-solid fa-expand"></i>
                </Button>
                <Button id="preview">
                  <i className="fa-solid fa-eye"></i>
                </Button>
                <Button>
                  <i className="fa-solid fa-pen-to-square"></i>
                </Button>
                <Button id="code">
                  <i className="fa-solid fa-code"></i>
                </Button>
                <Button id="undo">
                  <i className="fa-solid fa-rotate-left"></i>
                </Button>
                <Button id="redo">
                  <i className="fa-solid fa-rotate-right"></i>
                </Button>
                <Button id="save">
                  <i className="fa-solid fa-floppy-disk"></i>
                </Button>
                <Button id="setting">
                  <i className="fa-solid fa-cog"></i>
                </Button>
              </Panel>
            </div>
            <div className="editor-body">
              <Canvas />
            </div>
          </div>
          <Panel
            id="tab-container"
            className="editor-tab-container"
            resizable={{
              maxDim: 400,
              minDim: 250,
              tc: false, // Top handler
              cl: true, // Left handler
              cr: false, // Right handler
              bc: false, // Bottom handler
              // Being a flex child we need to change `flex-basis` property
              // instead of the `width` (default)
              keyWidth: "flex-basis",
            }}
          >
            <TabContainer>
              <Tab label="Styles">
                <StylesManager />
              </Tab>
              <Tab label="Properties">
                <TraitManager />
              </Tab>
            </TabContainer>
          </Panel>
        </div>
      </Editor>
      <Loading show={loading} />
    </>
  );
};

export default PageEditor;
