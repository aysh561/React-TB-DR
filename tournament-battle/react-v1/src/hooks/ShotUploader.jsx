return (
    <div>
        {/* File selector */}
        <input type="file" accept="image/*" onChange={selectFile} />

        {/* Preview image */}
        {preview && (
            <div>
                <img src={preview} alt="Preview" />
            </div>
        )}

        {/* Error message */}
        {error && <ErrorBlock message={error} />}

        {/* Loading state */}
        {loading && <Loader />}

        {/* Actions */}
        <div>
            {file && <button onClick={removeFile}>Remove</button>}
            <button onClick={uploadShot} disabled={loading || !file}>
                Upload
            </button>
        </div>
    </div>
);
