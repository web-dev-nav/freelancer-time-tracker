// Tracking continues when browser privacy settings block persistent storage.
export const storage = {
    getItem(key) {
        try {
            return localStorage.getItem(key);
        } catch (_) {
            return null;
        }
    },
    setItem(key, value) {
        try {
            localStorage.setItem(key, value);
        } catch (_) {}
    },
};
