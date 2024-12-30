import React from "react";
import "./styles.scss";

const PageManager = () => {
  return (
    <div>
      <div className="py-2">
        <h1>Page Manager</h1>
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
