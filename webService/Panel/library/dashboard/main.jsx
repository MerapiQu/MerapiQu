import { useState } from "react";

const Dashboard = () => {
  const [count, setCount] = useState(0);
  return (
    <div>
      <h1>Dashboard</h1>
      <button onClick={() => setCount(count + 1)} className="btn btn-primary">
        Click
      </button>
      <p>Count: {count}</p>
      <div>
        <ul>
          <li>List 1</li>
          <li>List 2</li>
        </ul>
      </div>
    </div>
  );
};

export default Dashboard;
