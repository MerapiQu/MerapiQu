import React from "react";

export interface BlockProps {
  tagName?: string; // The HTML tag to use (e.g., 'div', 'span')
  type?: string; // The type of node (e.g., 'textnode', 'comment')
  attribute?: {
    [key: string]: string; // HTML attributes for the element (e.g., { className: "example" })
  };
  classes?: string[]; // Additional CSS classes to apply to the element
  children?: string | string[] | BlockProps[]; // Can be a string or nested array of BlockProps
  [key: string]: any; // Allow for additional properties
}

// Helper function to recursively create React elements
const createNode = ({
  tagName = "div",
  attribute = {},
  classes = [],
  children,
}: BlockProps): React.ReactNode => {
  if (attribute.class) {
    classes.push(attribute.class);
    delete attribute.class;
  }

  attribute.className = classes.join(" ");
  if (typeof children === "string") {
    if (tagName != "div") {
      return React.createElement(tagName, attribute, children);
    }
    // Directly return the string if children is plain text
    return children;
  }

  // Recursively map children to React elements
  const childElements = (children || []).map(
    (child, index) => createNode({ ...child, key: index.toString() }) // Provide a unique `key` for each child
  );
  return React.createElement(tagName, attribute, ...childElements);
};

// Component to render a BlockProps object as a React element
const BlockNode: React.FC<BlockProps> = ({
  tagName,
  type,
  attribute,
  classes,
  children,
}) => {
  if (type === "textnode" || type === "comment") {
    // If it's a text node or comment, return the string directly
    return <>{children}</>;
  }

  // Use `createNode` to render complex structures
  return <>{createNode({ tagName, attribute, classes, children })}</>;
};

export default BlockNode;
