export async function apiClient(endpoint, options = {}) {
    const timeoutMs = 10000;

    const attemptRequest = async () => {
        const controller = new AbortController();
        const timeout = setTimeout(() => controller.abort(), timeoutMs);

        const token =
            typeof localStorage !== "undefined"
                ? localStorage.getItem("auth_token")
                : null;

        const headers = options.headers ? { ...options.headers } : {};

        if (!(options.body instanceof FormData)) {
            headers["Content-Type"] = "application/json";
        }

        if (token) {
            headers["Authorization"] = `Bearer ${token}`;
        }

        const config = {
            method: options.method || "GET",
            headers,
            signal: controller.signal
        };

        if (options.body) {
            config.body =
                options.body instanceof FormData
                    ? options.body
                    : JSON.stringify(options.body);
        }

        try {
            const response = await fetch(endpoint, config);
            clearTimeout(timeout);

            const status = response.status;

            let parsed = null;
            try {
                parsed = await response.json();
            } catch (err) {
                parsed = null;
            }

            if (!response.ok) {
                return {
                    ok: false,
                    status,
                    message:
                        parsed && parsed.message
                            ? parsed.message
                            : "Request failed",
                    data: null,
                    headers: Object.fromEntries(
                        response.headers.entries?.() || []
                    )
                };
            }

            return {
                ok: true,
                status,
                data: parsed,
                headers: Object.fromEntries(response.headers.entries())
            };
        } catch (error) {
            return {
                ok: false,
                status: 0,
                message: "Network error",
                data: null,
                headers: {}
            };
        }
    };

    const first = await attemptRequest();

    if (
        !first.ok &&
        (first.status === 500 || first.status === 503)
    ) {
        return await attemptRequest();
    }

    return first;
}
