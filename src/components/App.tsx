import React from "react";
import NavController from "@/components/navigations/NavController";
import NavFragment from "@/components/navigations/NavFragment";

const App = () => {
  return (
    <>
      <NavController>
        <NavFragment>
          <div className="loading-container">
            <div className="loader"></div>
            <div>Please wait...</div>
          </div>
        </NavFragment>
      </NavController>
    </>
  );
};

export default App;
