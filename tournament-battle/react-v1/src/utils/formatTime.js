export function formatTime(value) {
    if (!value) return "N/A";
    const date = new Date(value);
    if (isNaN(date.getTime())) return value;
    return date.toLocaleString();
}
