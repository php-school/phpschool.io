import {useAdminStore} from "../../stores/admin";

const getHeaders = (token) => ({
    Authorization: `Bearer ${token}`,
    'Content-Type': 'application/json',
});

const adminFetch = async (endpoint, options) => {
    const admin = useAdminStore();
    const token = admin.getToken();

    if (!token) {
        throw new Error('Token expired');
    }

    const response = await fetch(endpoint, {
        ...options,
        headers: getHeaders(token),
    });

    return await response.json();
};

export const newWorkshops = async () => {
    return await adminFetch('/api/admin/workshop/new');
};

export const allWorkshops = async () => {
    return await adminFetch('/api/admin/workshop/all');
};

export const getWorkshop = async (id) => {
    const response = await adminFetch(`/api/admin/workshop/view/${id}`);

    if (response.error) {
        throw new Error(response.error);
    }

    return response;
};

export const approveWorkshop = async (id) => {
    const response = await adminFetch(`/api/admin/workshop/approve/${id}`, { method: 'POST' });

    if (response.error) {
        throw new Error(response.error);
    }
}

export const promoteWorkshop = async (id) => {
    const response = await adminFetch(`/api/admin/workshop/promote/${id}`, { method: 'POST' });

    if (response.error) {
        throw new Error(response.error);
    }
}

export const deleteWorkshop = async (id) => {
    const response = await adminFetch(`/api/admin/workshop/delete/${id}`, { method: 'DELETE' });

    if (response.error) {
        throw new Error(response.error);
    }
}

export const allStudents = async () => {
    return await adminFetch('/api/admin/student/all');
}

export const allEvents = async () => {
    const response = await adminFetch('/api/admin/event/all');
    return response.events
}

export const deleteEvent = async (id) => {
    const response = await adminFetch(`/api/admin/event/delete/${id}`, { method: 'DELETE' });

    if (response.error) {
        throw new Error(response.error);
    }
}

export const createEvent = async (event) => {
    return await adminFetch('/api/admin/event/create', {
        method: 'POST',
        body: JSON.stringify(event),
    });
}

export const updateEvent = async (id, event) => {
    return await adminFetch(`/api/admin/event/update/${id}`, {
        method: 'POST',
        body: JSON.stringify(event),
    });
}

export const regenerateWorkshopFeed = async () => {
    const response = await adminFetch('/api/admin/workshop/regenerate', { method: 'POST' });

    if (response.error) {
        throw new Error(response.error);
    }
}

export const clearCache = async () => {
    return await adminFetch('/api/admin/cache/clear', { method: 'POST' });
}