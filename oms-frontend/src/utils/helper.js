    // Helper functions for role-based access control

export function setLocalStorage(key, data) {
   localStorage.setItem(key, data)
}

export function getLocalStorage(key) {
   return localStorage.getItem(key)
}