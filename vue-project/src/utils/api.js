// API configuration utility
export const API_BASE_URL = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8080/api/endpoints';
console.info(`API Base URL: ${API_BASE_URL}`);
if (import.meta.env.VITE_API_BASE_URL) {
  console.info(`Using custom API Base URL from .env: ${import.meta.env.VITE_API_BASE_URL}`);
} else {
  console.info(`Using default API Base URL: ${API_BASE_URL}`);
}

// Helper function to build API endpoints
export const buildApiUrl = (endpoint) => {
  // Ensure the endpoint does not start with a slash
  if (endpoint.startsWith('/')) {
    endpoint = endpoint.substring(1);
  }
  // sanitize the endpoint to prevent double slashes
  endpoint = endpoint.replace(/\/{2,}/g, '/');
  // Return the full API URL
  console.info(`${API_BASE_URL}/${endpoint}`)
  return `${API_BASE_URL}/${endpoint}`;
};
