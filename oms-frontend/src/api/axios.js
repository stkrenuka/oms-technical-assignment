import axios from 'axios';

const api = axios.create({
    baseURL: import.meta.env.VITE_API_BASE_URL,
    withCredentials: true, // REQUIRED
    withXSRFToken: true,   // Recommended for newer axios versions
    xsrfCookieName: 'XSRF-TOKEN', // Default
    xsrfHeaderName: 'X-XSRF-TOKEN'  // Default
});

export default api;
