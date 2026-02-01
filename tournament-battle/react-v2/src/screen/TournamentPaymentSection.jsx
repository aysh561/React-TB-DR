        {tournament.paymentRequired && tournament.paymentState === "paid" && (
            <div>Payment Approved</div>
        )}

        {tournament.paymentRequired && tournament.paymentState === "under_review" && (
            <div>Payment Under Review</div>
        )}

        {tournament.paymentRequired && tournament.paymentState === "pending" && (
            <PaymentProofUploader tournamentId={tournament.id} />
        )}
    </div>
);
