import { useAdminStore } from "../../stores/admin";

const getHeaders = (token) => ({
  Authorization: `Bearer ${token}`,
  "Content-Type": "application/json",
});

const adminFetch = async (endpoint, options) => {
  const admin = useAdminStore();
  const token = admin.getToken();

  if (!token) {
    throw new Error("Token expired");
  }

  const response = await fetch(endpoint, {
    ...options,
    headers: getHeaders(token),
  });

  // the server rejected the token, e.g. after the JWT secret changed
  if (response.status === 401) {
    admin.logout();
    throw new Error("Token rejected");
  }

  const data = await response.json();

  // failures come back as { error: "..." }; form validation failures as { form_errors: {...} }, which callers read themselves
  if (data.error) {
    throw new Error(data.error);
  }

  return data;
};

export const newWorkshops = async () => {
  return await adminFetch("/api/admin/workshop/new");
};

export const allWorkshops = async () => {
  return await adminFetch("/api/admin/workshop/all");
};

export const getWorkshop = async (id) => {
  return await adminFetch(`/api/admin/workshop/view/${id}`);
};

export const approveWorkshop = async (id) => {
  await adminFetch(`/api/admin/workshop/approve/${id}`, { method: "POST" });
};

export const promoteWorkshop = async (id) => {
  await adminFetch(`/api/admin/workshop/promote/${id}`, { method: "POST" });
};

export const deleteWorkshop = async (id) => {
  await adminFetch(`/api/admin/workshop/delete/${id}`, { method: "DELETE" });
};

export const allStudents = async () => {
  return await adminFetch("/api/admin/student/all");
};

export const allEvents = async () => {
  const response = await adminFetch("/api/admin/event/all");
  return response.events;
};

export const deleteEvent = async (id) => {
  await adminFetch(`/api/admin/event/delete/${id}`, { method: "DELETE" });
};

export const createEvent = async (event) => {
  return await adminFetch("/api/admin/event/create", {
    method: "POST",
    body: JSON.stringify(event),
  });
};

export const updateEvent = async (id, event) => {
  return await adminFetch(`/api/admin/event/update/${id}`, {
    method: "POST",
    body: JSON.stringify(event),
  });
};

export const regenerateWorkshopFeed = async () => {
  await adminFetch("/api/admin/workshop/regenerate", { method: "POST" });
};

export const clearCache = async () => {
  return await adminFetch("/api/admin/cache/clear", { method: "POST" });
};
