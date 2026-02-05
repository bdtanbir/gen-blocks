/**
 * GenBlocks API Service
 *
 * Centralized API service for all REST API calls.
 */

const API_NAMESPACE = 'genblocks/v1';

/**
 * Get the full API URL
 *
 * @param {string} endpoint - API endpoint
 * @returns {string} Full API URL
 */
function getApiUrl(endpoint) {
    const wpData = window.genBlocksAdmin || {};
    const restUrl = wpData.restUrl || '/wp-json/';
    return `${restUrl}${API_NAMESPACE}/${endpoint}`;
}

/**
 * Get request headers with nonce
 *
 * @returns {Object} Headers object
 */
function getHeaders() {
    const wpData = window.genBlocksAdmin || {};
    return {
        'Content-Type': 'application/json',
        'X-WP-Nonce': wpData.nonce || '',
    };
}

/**
 * Handle API response
 *
 * @param {Response} response - Fetch response
 * @returns {Promise<Object>} Parsed response data
 * @throws {Error} If response is not ok
 */
async function handleResponse(response) {
    const data = await response.json();

    if (!response.ok) {
        const message = data.message || data.error || 'An error occurred';
        const error = new Error(message);
        error.code = data.code || 'unknown_error';
        error.status = response.status;
        throw error;
    }

    return data;
}

/**
 * API Service object
 */
const api = {
    /**
     * Get plugin settings
     *
     * @returns {Promise<Object>} Settings data
     */
    async getSettings() {
        const response = await fetch(getApiUrl('settings'), {
            method: 'GET',
            headers: getHeaders(),
        });
        return handleResponse(response);
    },

    /**
     * Update plugin settings
     *
     * @param {Object} settings - Settings to update
     * @returns {Promise<Object>} Updated settings
     */
    async updateSettings(settings) {
        const response = await fetch(getApiUrl('settings'), {
            method: 'POST',
            headers: getHeaders(),
            body: JSON.stringify(settings),
        });
        return handleResponse(response);
    },

    /**
     * Test API connection
     *
     * @returns {Promise<Object>} Connection test result
     */
    async testConnection() {
        const response = await fetch(getApiUrl('test-connection'), {
            method: 'POST',
            headers: getHeaders(),
        });
        return handleResponse(response);
    },

    /**
     * Clear response cache
     *
     * @returns {Promise<Object>} Clear cache result
     */
    async clearCache() {
        const response = await fetch(getApiUrl('cache/clear'), {
            method: 'POST',
            headers: getHeaders(),
        });
        return handleResponse(response);
    },

    /**
     * Get usage statistics
     *
     * @param {string} period - Time period (day, week, month, year, all)
     * @returns {Promise<Object>} Usage data
     */
    async getUsage(period = 'month') {
        const url = new URL(getApiUrl('usage'), window.location.origin);
        url.searchParams.append('period', period);

        const response = await fetch(url.toString(), {
            method: 'GET',
            headers: getHeaders(),
        });
        return handleResponse(response);
    },

    /**
     * Get generation history
     *
     * @param {number} page - Page number
     * @param {number} perPage - Items per page
     * @returns {Promise<Object>} History data with pagination
     */
    async getHistory(page = 1, perPage = 20) {
        const url = new URL(getApiUrl('history'), window.location.origin);
        url.searchParams.append('page', page.toString());
        url.searchParams.append('per_page', perPage.toString());

        const response = await fetch(url.toString(), {
            method: 'GET',
            headers: getHeaders(),
        });
        return handleResponse(response);
    },

    /**
     * Get all templates
     *
     * @param {string|null} category - Optional category filter
     * @returns {Promise<Object>} Templates data
     */
    async getTemplates(category = null) {
        const url = new URL(getApiUrl('templates'), window.location.origin);
        if (category) {
            url.searchParams.append('category', category);
        }

        const response = await fetch(url.toString(), {
            method: 'GET',
            headers: getHeaders(),
        });
        return handleResponse(response);
    },

    /**
     * Get a single template
     *
     * @param {string} id - Template ID
     * @returns {Promise<Object>} Template data
     */
    async getTemplate(id) {
        const response = await fetch(getApiUrl(`templates/${id}`), {
            method: 'GET',
            headers: getHeaders(),
        });
        return handleResponse(response);
    },

    /**
     * Apply a template with variables
     *
     * @param {string} id - Template ID
     * @param {Object} variables - Template variables
     * @returns {Promise<Object>} Applied template result
     */
    async applyTemplate(id, variables = {}) {
        const response = await fetch(getApiUrl(`templates/${id}/apply`), {
            method: 'POST',
            headers: getHeaders(),
            body: JSON.stringify({ variables }),
        });
        return handleResponse(response);
    },

    /**
     * Generate a block from prompt
     *
     * @param {string} prompt - Generation prompt
     * @param {Object} context - Optional context
     * @returns {Promise<Object>} Generated block data
     */
    async generateBlock(prompt, context = {}) {
        const response = await fetch(getApiUrl('generate'), {
            method: 'POST',
            headers: getHeaders(),
            body: JSON.stringify({ prompt, context }),
        });
        return handleResponse(response);
    },

    /**
     * Get example prompts
     *
     * @returns {Promise<Object>} Example prompts
     */
    async getExamplePrompts() {
        const response = await fetch(getApiUrl('prompts/examples'), {
            method: 'GET',
            headers: getHeaders(),
        });
        return handleResponse(response);
    },
};

export default api;
