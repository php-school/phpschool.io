import { useAdminStore } from "../../stores/admin";

// FormData bodies set their own multipart Content-Type.
const getHeaders = (token, body) => ({
  Authorization: `Bearer ${token}`,
  ...(body instanceof FormData ? {} : { "Content-Type": "application/json" }),
});

const adminFetch = async (endpoint, options) => {
  const admin = useAdminStore();
  const token = admin.getToken();

  if (!token) {
    throw new Error("Token expired");
  }

  const response = await fetch(endpoint, {
    ...options,
    headers: getHeaders(token, options?.body),
  });

  // the server rejected the token, e.g. after the JWT secret changed
  if (response.status === 401) {
    admin.logout();
    throw new Error("Token rejected");
  }

  let data;
  try {
    data = await response.json();
  } catch {
    // the app always answers JSON, so this is e.g. the web server's own error page when PHP is down
    throw new Error(`The server responded with ${response.status}`);
  }

  // Failures come back as { error: "..." }, including crashes and unknown routes.
  // Form validation failures are a 200 with { form_errors: {...} }, which callers read themselves.
  if (!response.ok || data.error) {
    throw new Error(data.error ?? `The server responded with ${response.status}`);
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

// Sent as multipart so the poster file can be uploaded.
const eventForm = (event) => {
  const body = new FormData();
  for (const [key, value] of Object.entries(event)) {
    body.append(key, value ?? "");
  }
  return body;
};

export const createEvent = async (event) => {
  return await adminFetch("/api/admin/event/create", { method: "POST", body: eventForm(event) });
};

export const updateEvent = async (id, event) => {
  return await adminFetch(`/api/admin/event/update/${id}`, { method: "POST", body: eventForm(event) });
};

export const regenerateWorkshopFeed = async () => {
  await adminFetch("/api/admin/workshop/regenerate", { method: "POST" });
};

export const clearCache = async () => {
  await adminFetch("/api/admin/cache/clear", { method: "POST" });
};
