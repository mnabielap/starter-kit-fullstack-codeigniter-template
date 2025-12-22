/**
 * API Client & Auth Handler
 * Handles communication between the Web Interface and JSON API
 */
const API = {
    baseUrl: typeof BASE_URL !== 'undefined' ? BASE_URL : window.location.origin,

    apiPrefix: '/api/v1',

    // Helper to make authenticated requests
    async fetch(endpoint, options = {}) {
        const fullUrl = `${this.baseUrl}${this.apiPrefix}${endpoint}`; 
        const token = localStorage.getItem('accessToken');
        
        const headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            ...options.headers
        };

        if (token) {
            headers['Authorization'] = `Bearer ${token}`;
        }

        const config = {
            ...options,
            headers
        };

        try {
            const response = await fetch(fullUrl, config);

            // Handle Token Expiry (401)
            if (response.status === 401) {
                // If we are not already on an auth page, redirect
                const authPaths = ['/login', '/register', '/forgot-password', '/reset-password'];
                const currentPath = window.location.pathname;
                
                if (!authPaths.some(path => currentPath.includes(path))) {
                    this.logout();
                }
            }

            return response;
        } catch (error) {
            console.error('API Error:', error);
            throw error;
        }
    },

    saveTokens(tokens) {
        localStorage.setItem('accessToken', tokens.access.token);
        localStorage.setItem('refreshToken', tokens.refresh.token);
    },

    async logout() {
        const refreshToken = localStorage.getItem('refreshToken');
        if (refreshToken) {
            try {
                // Try to notify server, but don't block if it fails
                await fetch(`${this.baseUrl}${this.apiPrefix}/auth/logout`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ refreshToken })
                });
            } catch (e) {
                console.warn('Logout server sync failed', e);
            }
        }
        
        localStorage.removeItem('accessToken');
        localStorage.removeItem('refreshToken');
        window.location.href = `${this.baseUrl}/login`;
    },

    checkAuth() {
        const token = localStorage.getItem('accessToken');
        const path = window.location.pathname;
        
        // Allowed public paths
        const publicPaths = ['/login', '/register', '/forgot-password', '/reset-password'];
        const isPublic = publicPaths.some(p => path.includes(p));

        if (!token && !isPublic) {
            window.location.href = `${this.baseUrl}/login`;
        }
        
        // If logged in and trying to access login page, go to dashboard
        if (token && isPublic && !path.includes('reset-password')) {
            window.location.href = `${this.baseUrl}/`;
        }
    },
    
    // Helper to extract payload from JWT
    getUser() {
        const token = localStorage.getItem('accessToken');
        if(!token) return null;
        try {
            return JSON.parse(atob(token.split('.')[1]));
        } catch (e) {
            return null;
        }
    }
};

// Run Auth Check on Load
document.addEventListener('DOMContentLoaded', () => {
    API.checkAuth();
});