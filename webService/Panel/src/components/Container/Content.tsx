import React, { useEffect, useState } from "react";
import BlockNode, { BlockProps } from "../Blocks/BlockNode";

export interface ContentProps {
  styles: string[];
  scripts: string[];
  content: BlockProps[] | React.JSX.Element;
}

const isReactElement = (element: any): element is React.JSX.Element => {
  return React.isValidElement(element);
};

const Content = (props: ContentProps) => {
  const [content, setContent] = useState<BlockProps[] | React.JSX.Element>(
    props.content
  );

  useEffect(() => {
    const styles = props.styles || [];
    styles.forEach((style) => {
      const link = document.createElement("link");
      link.rel = "stylesheet";
      link.href = style;
      link.type = "text/css";
      link.classList.add("dynamic-style");
      document.head.appendChild(link);
    });
    return () => {
      document.querySelectorAll(".dynamic-style").forEach((style) => {
        style.remove();
      });
    };
  }, [props.styles]);

  useEffect(() => {
    const scripts = props.scripts;
    scripts.forEach((script) => {
      const match = script.match(/(\w+)\.js/) ?? [];
      if (match[1]) {
        const scriptName = match[1];
        import(/* webpackIgnore: true */ script).then(() => {
          if ((window as any).Panel[`library/${scriptName}`]) {
            const lib = (window as any).Panel[`library/${scriptName}`];
            setContent(<lib.default />);
          }
        });
      }
    });
  }, [props.scripts]);

  useEffect(() => {
    setContent(props.content);
  }, [props.content]);

  if (isReactElement(content)) {
    return <>{content}</>;
  }

  return (
    <>
      {content.map((block, i) => (
        <BlockNode {...block} key={i} />
      ))}
    </>
  );
};

export default Content;
