// Auto-upload handler
const uploadProof = useCallback(async (id) => {
    if (!file) {
        setError("No file selected.");
        return;
    }

    setLoading(true);
    setError(null);

    try {
        const formData = new FormData();
        formData.append("payment_proof", file);

        const url = endpoints.payments.uploadProof(id);
        const response = await apiClient(url, {
            method: "POST",
            body: formData
        });

        if (response.ok) {
            const newState = response.data.state;
            if (newState === "under_review" || newState === "paid") {
                setState(newState);
            } else {
                setError("Invalid state response.");
            }
        } else {
            setError(response.message || "Upload failed.");
        }
    } catch (e) {
        setError("Upload failed.");
    } finally {
        setLoading(false);
    }
}, [file]);

// Handle file selection and trigger auto-upload
const selectFile = useCallback((event) => {
    const selected = event.target.files[0];
    if (!selected) return;

    const allowedTypes = ["image/jpeg", "image/jpg", "image/png"];
    if (!allowedTypes.includes(selected.type)) {
        setError("Only JPG, JPEG, or PNG files are allowed.");
        return;
    }

    if (selected.size > 5 * 1024 * 1024) {
        setError("File must be below 5MB.");
        return;
    }

    if (preview) URL.revokeObjectURL(preview);

    const url = URL.createObjectURL(selected);
    setPreview(url);
    setFile(selected);
    setError(null);
    setState("idle");

    uploadProof(tournamentId);
}, [preview, uploadProof, tournamentId]);

// Remove file unless under review
const removeFile = useCallback(() => {
    if (state === "under_review") return;

    if (preview) URL.revokeObjectURL(preview);

    setFile(null);
    setPreview(null);
    setError(null);
    setState("idle");
}, [state, preview]);

return {
    file,
    preview,
    loading,
    error,
    state,
    selectFile,
    removeFile,
    uploadProof
};
