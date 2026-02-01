import { useState, useCallback } from "react";
import { fetchJSON } from "../utils/fetchJSON";

export function useFetch() {
    const [data, setData] = useState(null);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState(null);

    const execute = useCallback(async (url, options = {}) => {
        setLoading(true);
        setError(null);

        try {
            const result = await fetchJSON(url, options);
            setData(result);
        } catch (err) {
            setError("Request failed");
        } finally {
            setLoading(false);
        }
    }, []);

    return { data, loading, error, execute };
}
