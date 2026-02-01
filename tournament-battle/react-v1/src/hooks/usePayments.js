import { useState, useCallback } from "react";
import { apiClient } from "../api/apiClient";
import { endpoints } from "../api/endpoints";

export function usePayments() {
    const [paymentStatus, setPaymentStatus] = useState(null);
    const [paymentHistory, setPaymentHistory] = useState(null);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState(null);

    const uploadProof = useCallback(async (tournamentId, payload) => {
        setLoading(true);
        setError(null);

        const res = await apiClient(endpoints.payments.uploadProof(tournamentId), {
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

    const checkPaymentStatus = useCallback(async (tournamentId) => {
        setLoading(true);
        setError(null);

        const res = await apiClient(endpoints.payments.checkStatus(tournamentId));

        if (!res.ok) {
            setError(res.message);
            setLoading(false);
            return;
        }

        setPaymentStatus(res.data);
        setLoading(false);
    }, []);

    const getPaymentHistory = useCallback(async () => {
        setLoading(true);
        setError(null);

        const res = await apiClient(endpoints.payments.history);

        if (!res.ok) {
            setError(res.message);
           setLoading(false);
            return;
        }

        setPaymentHistory(res.data);
        setLoading(false);
    }, []);

    return {
        paymentStatus,
        paymentHistory,
        loading,
        error,
        uploadProof,
        checkPaymentStatus,
        getPaymentHistory
    };
}
