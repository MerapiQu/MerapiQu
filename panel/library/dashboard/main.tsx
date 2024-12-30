import React, { createContext } from "react";
import Header from "./components/Header";
import WidgetContainer from "./components/WidgetContainer";
import "./style.scss";

interface DashboardStore {
  isCustomize: boolean;
  setIsCustomize: (isCustomize: boolean) => void;
}

const DashboardContext = React.createContext<DashboardStore>({} as any);
export const useDashboard = () => React.useContext(DashboardContext);

const Dashboard = () => {
  const [isCustomize, setIsCustomize] = React.useState(false);
  const payload = {
    isCustomize,
    setIsCustomize,
  };

  return (
    <DashboardContext.Provider value={payload}>
      <div>
        <Header />
        <WidgetContainer />
      </div>
    </DashboardContext.Provider>
  );
};

export default Dashboard;
