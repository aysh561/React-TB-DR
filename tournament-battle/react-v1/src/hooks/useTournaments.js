import { useState, useCallback } from "react";
import { apiClient } from "../api/apiClient";
import { endpoints } from "../api/endpoints";

export function useTournaments() {
    const [tournaments, setTournaments] = useState(null);
    const [singleTournament, setSingleTournament] = useState(null);
    const [userTournaments, setUserTournaments] = useState(null);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState(null);

    const getTournaments = useCallback(async () => {
        setLoading(true);
        setError(null);
        const res = await apiClient(endpoints.tournaments.list);
        if (!res.ok) {
            setError(res.message);
            setLoading(false);
            return;
        }
        setTournaments(res.data);
        setLoading(false);
    }, []);

    const getTournament = useCallback(async (id) => {
        setLoading(true);
        setError(null);
        const res = await apiClient(endpoints.tournaments.single(id));
        if (!res.ok) {
            setError(res.message);
            setLoading(false);
            return;
        }
        setSingleTournament(res.data);
        setLoading(false);
    }, []);

    const joinTournament = useCallback(async (id, payload) => {
        setLoading(true);
        setError(null);
        const res = await apiClient(endpoints.tournaments.join(id), {
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

    const getUserTournaments = useCallback(async () => {
        setLoading(true);
        setError(null);
        const res = await apiClient(endpoints.tournaments.userTournaments);
        if (!res.ok) {
            setError(res.message);
            setLoading(false);
            return;
        }
        setUserTournaments(res.data);
        setLoading(false);
    }, []);

    return {
        tournaments,
        singleTournament,
        userTournaments,
        loading,
        error,
        getTournaments,
        getTournament,
        joinTournament,
        getUserTournaments
    };
}
