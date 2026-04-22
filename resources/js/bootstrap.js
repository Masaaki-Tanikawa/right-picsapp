/**
 * Laravel用 fetch ラッパー
 */
const getCsrfToken = () => document
    .querySelector('meta[name="csrf-token"]')
    ?.getAttribute('content');

async function handleResponse(response) {
    if (response.ok) {
        if (response.status === 204) return {};
        return response.json();
    }
    const error = await response.json().catch(() => ({}));
    const err = new Error(`HTTP ${response.status}`);
    err.status = response.status;
    err.data = error;
    throw err;
}

window.api = {
    get: (url, options = {}) => fetch(url, {
        method: 'GET',
        ...options,
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            ...options.headers,
        },
    }).then(handleResponse),

    post: (url, data = null, options = {}) => fetch(url, {
        method: 'POST',
        ...options,
        headers: {
            'X-CSRF-TOKEN': getCsrfToken(),
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            ...options.headers,
        },
        body: data ? JSON.stringify(data) : null,
    }).then(handleResponse),

    put: (url, data = null, options = {}) => fetch(url, {
        method: 'PUT',
        ...options,
        headers: {
            'X-CSRF-TOKEN': getCsrfToken(),
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            ...options.headers,
        },
        body: data ? JSON.stringify(data) : null,
    }).then(handleResponse),

    patch: (url, data = null, options = {}) => fetch(url, {
        method: 'PATCH',
        ...options,
        headers: {
            'X-CSRF-TOKEN': getCsrfToken(),
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            ...options.headers,
        },
        body: data ? JSON.stringify(data) : null,
    }).then(handleResponse),

    delete: (url, options = {}) => fetch(url, {
        method: 'DELETE',
        ...options,
        headers: {
            'X-CSRF-TOKEN': getCsrfToken(),
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            ...options.headers,
        },
    }).then(handleResponse),
};
