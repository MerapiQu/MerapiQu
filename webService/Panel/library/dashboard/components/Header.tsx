import React from "react";
import { useDashboard } from "../main";

const Header = () => {
  const { isCustomize, setIsCustomize } = useDashboard();

  return (
    <header className="d-flex justify-content-between align-items-center py-3">
      <h1>Dashboard</h1>
      <div>
        <button
          className={`btn ${isCustomize ? "btn-primary" : "btn-secondary"}`}
          onClick={() => setIsCustomize(!isCustomize)}
        >
          {isCustomize ? (
            <>
              <i className="fa-solid fa-floppy-disk me-2"></i>
              Save Changes
            </>
          ) : (
            <>
              <i className="fa-solid fa-brush me-2"></i> Customize
            </>
          )}
        </button>
      </div>
    </header>
  );
};

export default Header;
