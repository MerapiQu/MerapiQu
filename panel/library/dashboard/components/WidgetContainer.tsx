import React from "react";
import Widget, { IWidget } from "./Widget";
import { useDashboard } from "../main";

const createDummy = () => {
  const widgets: IWidget[] = [];
  for (let i = 0; i < 10; i++) {
    widgets.push({
      title: `Widget ${i}`,
      name: `Widget${i}`,
      children: [
        () => {
          return (
            <>
              <h1>Widget {i}</h1>
            </>
          );
        },
      ],
    });
  }
  return widgets;
};

const WidgetContainer = () => {
  const { isCustomize } = useDashboard();
  const widgets = createDummy();

  return (
    <div className={`widget-container${isCustomize ? " customize" : ""}`}>
      {widgets.map((widget, index) => (
        <Widget key={index} {...widget} />
      ))}
    </div>
  );
};

export default WidgetContainer;
