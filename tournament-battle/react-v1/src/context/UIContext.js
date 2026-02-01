import React, { createContext, useState, useCallback } from "react";

export const UIContext = createContext();

export function UIProvider({ children }) {
    const [theme, setTheme] = useState("light");
    const [loading, setLoading] = useState(false);

    const toggleTheme = useCallback(() => {
        setTheme((prev) => (prev === "light" ? "dark" : "light"));
    }, []);

    const showLoader = useCallback(() => setLoading(true), []);
    const hideLoader = useCallback(() => setLoading(false), []);

    return (
        <UIContext.Provider
            value={{
                theme,
                toggleTheme,
                loading,
                showLoader,
                hideLoader
            }}
        >
            {children}
        </UIContext.Provider>
    );
}
