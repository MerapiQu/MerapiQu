import React from "react";
import "./styles.scss";
import { useNav } from "@merapipanel/core/panels";

const PageManager = () => {
  
  const { currentPath, navigate } = useNav();
  const goToEditor = () => {
    navigate(currentPath + "/editor")
  }

  return (
    <div>
      <div className="py-2 d-flex justify-content-between">
        <h1>Page Manager</h1>
        <div>
          <button className="btn btn-primary" onClick={goToEditor}>
            <i className="fa-solid fa-pen-ruler me-2"></i> Editor
          </button>
        </div>
      </div>
      <div className="page-container">Hallo World</div>
      <p>
        Next you will playing in this page, this page provide service such as
        create a page edit a page and delete a page. Most important tools is
        editing tool wich allow user drag and drop block to design her own page.
      </p>
    </div>
  );
};

export default PageManager;
