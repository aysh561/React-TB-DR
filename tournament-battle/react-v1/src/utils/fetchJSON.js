export async function fetchJSON(url, options = {}) {
    const res = await fetch(url, {
        headers: { "Content-Type": "application/json" },
        ...options
    });

    if (!res.ok) {
        throw new Error("Network error");
    }

    return res.json();
}
