const value = {
    user,
    setUser,
    token,
    setToken
};

return <AppContext.Provider value={value}>{children}</AppContext.Provider>;
