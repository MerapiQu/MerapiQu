import React, { useState, useRef, useEffect } from "react";
import { useDashboard } from "../main";
import ToolContainer from "./Tools/ToolContainer";

export interface WidgetOption {
  width: string;
  height: string;
  [key: string]: any;
}

export interface IWidget {
  name: string;
  title: string;
  children: React.FC[];
  option?: WidgetOption;
}

interface WidgetStore {
  parent: HTMLElement | null | undefined;
  /**
   * @returns element width in persentage
   */
  getWidth: () => number;
  getHeight: () => number;
  setOption: React.Dispatch<React.SetStateAction<WidgetOption>>;
  option: WidgetOption;
  [key: string]: any;
}

const getRealWidth = (element?: HTMLElement, parent?: HTMLElement) => {
  const width = element?.offsetWidth || 0;
  const parentWidth = parent?.offsetWidth || 100;
  return Math.floor((width / parentWidth) * 100);
};

const getRealHeight = (element: HTMLElement, parent: HTMLElement) => {
  const height = element.offsetHeight;
  const parentHeight = parent.offsetHeight;
  return Math.floor((height / parentHeight) * 100);
};

const WidgetContext = React.createContext<WidgetStore>({} as any);
export const useWidget = () => React.useContext(WidgetContext);

const Widget = (args: IWidget) => {
  const { name, title, children } = args;
  const [option, setOption] = useState<WidgetOption>(
    args.option || { width: "200px", height: "200px" }
  );
  const { isCustomize } = useDashboard();
  const [focus, setFocus] = useState(false);
  const widgetRef = useRef<HTMLDivElement>(null);
  const [parent, setParent] = useState<HTMLElement | null>();

  const handleFocus = () => {
    if (!isCustomize) return;
    setFocus(true);
  };

  const handleClickOutside = (event: MouseEvent) => {
    if (
      widgetRef.current &&
      !widgetRef.current.contains(event.target as Node)
    ) {
      setFocus(false);
    }
  };

  useEffect(() => {
    if (widgetRef.current) {
      setParent(widgetRef.current.parentElement);
    }
  }, [widgetRef]);

  const store: WidgetStore = {
    option,
    focus,
    parent,
    setFocus,
    getWidth: () => getRealWidth(widgetRef.current!, parent!),
    getHeight: () => getRealHeight(widgetRef.current!, parent!),
    setOption,
  };

  useEffect(() => {
    document.addEventListener("mousedown", handleClickOutside);

    return () => {
      document.removeEventListener("mousedown", handleClickOutside);
    };
  }, []);

  return (
    <WidgetContext.Provider value={store}>
      <div
        className={`widget${focus ? " focus" : ""}`}
        ref={widgetRef}
        onClick={handleFocus}
        style={{ width: option.width, height: option.height }}
      >
        <div>{name}</div>
        <div>{title}</div>
        {children.map((Child, index) => <Child key={index} />)}
        {focus && <ToolContainer />}
      </div>
    </WidgetContext.Provider>
  );
};

export default Widget;
