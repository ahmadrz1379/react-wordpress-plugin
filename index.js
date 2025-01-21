import React, { useState } from "react";
import ReactDOM from "react-dom/client";
import Layout from "./frontend/front-end";

console.log("token outside of app", window.reactPluginData);
const App = () => {
  const [count, setCount] = useState(0);
  const [token, setToken] = useState(
    localStorage.getItem("token") || window.reactPluginData.token || ""
  );

  return (
    <div style={{ padding: "20px" }}>
      <Layout></Layout>
    </div>
  );
};

document.addEventListener("DOMContentLoaded", () => {
  const root = document.getElementById("my-react-plugin-root");
  if (root) {
    const reactRoot = ReactDOM.createRoot(root);
    reactRoot.render(<App />);
  }
});
