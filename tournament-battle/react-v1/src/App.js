import React from "react";
import AppRoutes from "./routes";
import Header from "./components/Header";
import Footer from "./components/Footer";
import "./styles/Layout.css";

function App() {
    return (
        <div className="app-container">
            <Header />
            <main className="app-main">
                <AppRoutes />
            </main>
            <Footer />
        </div>
    );
}

export default App;
