import React, { useState, useEffect } from "react";
import axios from "axios";
import Sidebar from "./components/sidebar";
import Main from "./components/main";

const Layout = () => {
  const [data, setData] = useState([]);
  const [selectedPage, setSelectedPage] = useState(null);
  const [message, setMessage] = useState("");

  useEffect(() => {
    const fetchData = async () => {
      try {
        const response = await axios.get(
          `${window.reactPluginData.rest_base}react-wordpress-plugin/v1/settings`,
          {
            headers: {
              "X-WP-Nonce": window.reactPluginData.token,
            },
          }
        );
        if (response.data.length > 0) {
          setData(response.data);
          setSelectedPage(response.data[0]); // Default to the first page
        }
      } catch (error) {
        setMessage("Error fetching data");
      }
    };

    fetchData();
  }, []);

  const handleFieldChange = (fieldId, value) => {
    setData((prevData) =>
      prevData.map((page) =>
        page.id === selectedPage.id
          ? {
              ...page,
              fields: page.fields.map((field) =>
                field.id === fieldId ? { ...field, default: value } : field
              ),
            }
          : page
      )
    );
  };

  const handleSave = async () => {
    try {
      const response = await axios.post(
        `${window.reactPluginData.rest_base}react-wordpress-plugin/v1/settings`,
        { settings: data },
        {
          headers: {
            "X-WP-Nonce": window.reactPluginData.token,
          },
        }
      );
      setMessage("Data saved successfully");
    } catch (error) {
      setMessage("Error saving data");
    }
  };

  const generateJSON = () => {
    const jsonData = JSON.stringify(data, null, 2);
    const blob = new Blob([jsonData], { type: "application/json" });
    const url = URL.createObjectURL(blob);
    const link = document.createElement("a");
    link.href = url;
    link.download = "settings.json";
    link.click();
  };

  return (
    <div style={{ display: "flex" }}>
      <Sidebar
        items={data} // Pass the data to Sidebar
        onItemClick={(item) => setSelectedPage(item)} // Handle clicks from Sidebar
        activeItem={selectedPage?.id} // Highlight the active item
      />
      <div style={{ flex: 1 }}>
        {selectedPage && (
          <>
            <Main content={selectedPage} onFieldChange={handleFieldChange} /> {/* Pass the selected page content to Main */}
            <button onClick={handleSave} className="bg-blue-500 text-white py-2 px-4 rounded mt-4">
              Save
            </button>
            <button onClick={generateJSON} className="bg-green-500 text-white py-2 px-4 rounded mt-4 ml-2">
              Generate JSON
            </button>
            {message && <p className="mt-4">{message}</p>}
          </>
        )}
      </div>
    </div>
  );
};

export default Layout;
