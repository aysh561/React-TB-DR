return (
    <div>
        <input type="file" accept="image/*" onChange={selectFile} />

        {loading && <Loader />}

        {error && <ErrorBlock message={error} />}

        {preview && (
            <img
                src={preview}
                alt="Payment Proof Preview"
            />
        )}

        {file && state !== "under_review" && (
            <button onClick={removeFile}>Remove</button>
        )}

        {state === "under_review" && (
            <div>Payment Under Review</div>
        )}

        {state === "paid" && (
            <div>Payment Approved</div>
        )}
    </div>
);
