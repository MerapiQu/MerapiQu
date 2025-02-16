import React from "react";
import { createRoot } from "react-dom/client";
import App from "@/components/App"
import "@/scss/main.scss";

const anchor = document.getElementById("root");
if (anchor) {
  createRoot(anchor).render(
    <React.StrictMode>
      <App />
    </React.StrictMode>
  );
} else {
  console.error("root element not found!");
}
