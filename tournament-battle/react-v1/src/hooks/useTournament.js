import { useFetch } from "./useFetch";
import { useEffect } from "react";

export function useTournament(id) {
    const { data, loading, error, execute } = useFetch();

    useEffect(() => {
        if (id) {
            execute(`/api/tournament/${id}`);
        }
    }, [id, execute]);

    return {
        tournament: data,
        loading,
        error
    };
}
