const {
    match,
    loading,
    error,
    getMatch
} = useMatches();

useEffect(() => {
    getMatch(matchId);
}, [matchId]);

if (loading) return <Loader />;
if (error) return <ErrorBlock message={error} />;

return (
    <div>
        {/* Match Details */}
        {match && (
            <div>
                <h2>{match.title}</h2>
                <div>Match ID: {match.id}</div>
                <div>Player 1: {match.player1}</div>
                <div>Player 2: {match.player2}</div>
                <div>Status: {match.status}</div>
            </div>
        )}

        {/* Shot Upload Section */}
        <ShotUploader matchId={matchId} />

        {/* Verification Status (Phase 10) */}
        {match && match.verificationStatus && (
            <div>
                <strong>Verification Status:</strong> {match.verificationStatus}
                {match.verificationReason && (
                    <div>Reason: {match.verificationReason}</div>
                )}
            </div>
        )}

        {/* Phase 7 verification/history block remains here (unchanged) */}
    </div>
);
