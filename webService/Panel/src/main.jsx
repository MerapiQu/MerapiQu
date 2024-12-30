import React from "react";
import { createRoot } from "react-dom/client";
import App from "./app";

const root = document.getElementById("root");
if (!root) {
  console.error("root not found!");
} else {
  const rootElement = createRoot(root);
  rootElement.render(<App />);
}
