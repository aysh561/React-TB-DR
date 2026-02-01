import { useState, useCallback } from "react";
import { apiClient } from "../api/apiClient";
import { endpoints } from "../api/endpoints";

export function useUser() {
    const [user, setUser] = useState(null);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState(null);

    const login = useCallback(async (payload) => {
        setLoading(true);
        setError(null);

        const res = await apiClient(endpoints.user.login, {
            method: "POST",
            body: payload
        });

        if (!res.ok) {
            setError(res.message);
            setLoading(false);
            return null;
        }

        if (typeof localStorage !== "undefined" && res.data && res.data.token) {
            localStorage.setItem("auth_token", res.data.token);
        }

        setLoading(false);
        return res.data;
    }, []);

    const register = useCallback(async (payload) => {
        setLoading(true);
        setError(null);

        const res = await apiClient(endpoints.user.register, {
            method: "POST",
            body: payload
        });

        if (!res.ok) {
            setError(res.message);
            setLoading(false);
            return null;
        }

        setLoading(false);
        return res.data;
    }, []);

    const fetchUser = useCallback(async () => {
        setLoading(true);
        setError(null);

        const res = await apiClient(endpoints.user.me);

        if (!res.ok) {
            setError(res.message);
            setLoading(false);
            return;
        }

        setUser(res.data);
        setLoading(false);
    }, []);

    const logout = useCallback(() => {
        if (typeof localStorage !== "undefined") {
            localStorage.removeItem("auth_token");
        }
        setUser(null);
    }, []);

    return {
        user,
        loading,
        error,
        login,
        register,
        fetchUser,
        logout
    };
}
