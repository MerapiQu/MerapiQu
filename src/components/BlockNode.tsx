import { BlockProps } from "@/interfaces/BlockNode";
import React from "react";

// Helper function to recursively create React elements
const createNode = ({ tagName = "div", attribute = {}, classes = [], children }: BlockProps): React.ReactNode => {
  if (attribute.class) {
    classes.push(attribute.class);
    delete attribute.class;
  }

  attribute.className = classes.join(" ");
  if (typeof children === "string") {
    if (tagName != "div") {
      return React.createElement(tagName, attribute, children);
    }
    return children;
  }

  const childElements = (children || []).map(
    (child, index) => createNode({ ...child, key: index.toString() })
  );
  return React.createElement(tagName, attribute, ...childElements);
};

const BlockNode: React.FC<BlockProps> = ({
  tagName,
  type,
  attribute,
  classes,
  children,
}) => {
  if (type === "textnode" || type === "comment") {
    return <>{children}</>;
  }
  return <>{createNode({ tagName, attribute, classes, children })}</>;
};

export default BlockNode;
