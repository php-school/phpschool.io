import { defineStore } from "pinia";

// localStorage does not exist while pre-rendering (vite-ssg runs this in Node)
const readStored = (key) => (typeof localStorage === "undefined" ? null : JSON.parse(localStorage.getItem(key)));

export const useAdminStore = defineStore("admin", {
  state: () => ({
    admin: readStored("admin"),
    adminToken: readStored("adminToken"),
  }),
  actions: {
    async login(email, password) {
      const response = await fetch("/api/admin/login", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          email: email,
          password: password,
        }),
      });

      const data = await response.json().catch(() => ({}));

      if (!response.ok) {
        return {
          success: false,
          errors: data.form_errors ?? { auth: [`Login failed (${response.status}). Please try again later.`] },
        };
      }

      this.admin = data.admin;
      this.adminToken = data.token;

      localStorage.setItem("admin", JSON.stringify(this.admin));
      localStorage.setItem("adminToken", JSON.stringify(this.adminToken));

      this.router.push("/admin/workshops");

      return { success: true };
    },
    logout() {
      this.admin = null;
      this.adminToken = null;
      localStorage.removeItem("admin");
      localStorage.removeItem("adminToken");
      this.router.push("/login");
    },
    getToken() {
      if (!this.adminToken) {
        return null;
      }

      //verify token not expired
      const decodedToken = JSON.parse(atob(this.adminToken.split(".")[1]));
      const expirationTime = decodedToken.exp * 1000;

      if (Date.now() > expirationTime) {
        this.logout();
        return null;
      }

      return this.adminToken;
    },
  },
});
