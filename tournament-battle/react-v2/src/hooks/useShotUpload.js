const selectFile = useCallback(async (event) => {
    const selected = event.target.files?.[0] || null;
    if (!selected) return;

    const ext = selected.name.split(".").pop().toLowerCase();
    if (!["jpg", "jpeg", "png"].includes(ext)) {
        setError("invalid_extension");
        return;
    }

    if (selected.size > 5 * 1024 * 1024) {
        setError("file_too_large");
        return;
    }

    if (preview) URL.revokeObjectURL(preview);
    const url = URL.createObjectURL(selected);
    setPreview(url);

    const buffer = await selected.arrayBuffer();
    const hashBuffer = await crypto.subtle.digest("SHA-256", buffer);
    const hashHex = [...new Uint8Array(hashBuffer)]
        .map((b) => b.toString(16).padStart(2, "0"))
        .join("");
    setHash(hashHex);

    setFile(selected);
    setError(null);
}, [preview]);

const removeFile = useCallback(() => {
    if (preview) URL.revokeObjectURL(preview);
    setFile(null);
    setPreview(null);
    setHash(null);
    setError(null);
}, [preview]);

const uploadShot = useCallback(async () => {
    if (!file || !hash) return { ok: false };

    setLoading(true);
    setError(null);

    try {
        const formData = new FormData();
        formData.append("shot", file);
        formData.append("hash", hash);

        const packet = generateVerificationPacket(hash);
        formData.append("packet", JSON.stringify(packet));

        const signals = generateRefereeSignals();
        formData.append("signals", JSON.stringify(signals));

        const evidence = buildEvidence(hash, packet, signals);
        formData.append("evidence", JSON.stringify(evidence));

        const url = endpoints.shots.upload(matchId);

        const res = await apiClient(url, {
            method: "POST",
            body: formData
        });

        if (!res.ok) {
            setError(res.message || "Upload failed");
            setLoading(false);
            return { ok: false };
        }

        setLoading(false);
        return { ok: true, data: res.data };
    } catch (e) {
        setLoading(false);
        setError("Upload failed");
        return { ok: false };
    }
}, [file, hash, matchId]);

return {
    file,
    preview,
    hash,
    loading,
    error,
    selectFile,
    removeFile,
    uploadShot
};
