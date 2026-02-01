import React from "react";
import { formatTime } from "../../utils/formatTime";

function PaymentCard({ payment }) {
    return (
        <div className="card payment-card">
            <h3 className="card-title">Payment</h3>

            <div className="card-row">
                <span className="card-label">Amount:</span>
                <span>{payment.amount}</span>
            </div>

            <div className="card-row">
                <span className="card-label">Method:</span>
                <span>{payment.method}</span>
            </div>

            <div className="card-row">
                <span className="card-label">Status:</span>
                <span>{payment.status}</span>
            </div>

            <div className="card-row">
                <span className="card-label">Date:</span>
                <span>{formatTime(payment.createdAt)}</span>
            </div>
        </div>
    );
}

export default PaymentCard;
