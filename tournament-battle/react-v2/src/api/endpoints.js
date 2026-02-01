tournaments: {
    list: "/api/tournaments",
    single: (id) => `/api/tournaments/${id}`,
    join: (id) => `/api/tournaments/${id}/join`,
    leave: (id) => `/api/tournaments/${id}/leave`,
},

payments: {
    uploadProof: (tournamentId) => `/api/payments/${tournamentId}/upload`,
    checkStatus: (tournamentId) => `/api/payments/${tournamentId}/status`,
    history: "/api/payments/history"
},

matches: {
    report: (matchId) => `/api/matches/${matchId}/report`,
    details: (matchId) => `/api/matches/${matchId}`,
},

bracket: {
    view: (tournamentId) => `/api/bracket/${tournamentId}`,
    next: (tournamentId) => `/api/bracket/${tournamentId}/next`,
},

shots: {
    upload: (matchId) => `/api/shots/${matchId}/upload`,
    list: (matchId) => `/api/shots/${matchId}`,
}
