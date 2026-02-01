//updated
// Phase 8 update included: shots.upload endpoint added

export const endpoints = {
    tournaments: {
        list: "/api/tournaments",
        single: (id) => `/api/tournaments/${id}`,
        join: (id) => `/api/tournaments/${id}/join`,
        userTournaments: "/api/user/tournaments"
    },

    matches: {
        list: (tournamentId) => `/api/tournaments/${tournamentId}/matches`,
        details: (matchId) => `/api/matches/${matchId}`,
        submitVerification: (matchId) => `/api/matches/${matchId}/verification`,
        updateState: (matchId) => `/api/matches/${matchId}/state`
    },

    bracket: {
        rounds: (tournamentId) => `/api/tournaments/${tournamentId}/bracket/rounds`,
        nodes: (tournamentId) => `/api/tournaments/${tournamentId}/bracket/nodes`,
        progression: (tournamentId) => `/api/tournaments/${tournamentId}/bracket/progression`
    },

    payments: {
        uploadProof: (tournamentId) => `/api/payments/${tournamentId}/upload`,
        checkStatus: (tournamentId) => `/api/payments/${tournamentId}/status`,
        history: "/api/payments/history"
    },

    shots: {
        upload: (matchId) => `/api/matches/${matchId}/shot` // Phase 8 endpoint
    },

    user: {
        login: "/api/user/login",
        register: "/api/user/register",
        me: "/api/user/me"
    }
};
