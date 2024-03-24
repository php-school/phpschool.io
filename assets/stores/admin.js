import { defineStore } from "pinia";

export const useAdminStore = defineStore({
  id: "admin",
  state: () => ({
    admin: JSON.parse(localStorage.getItem("admin")),
    adminToken: JSON.parse(localStorage.getItem("adminToken")),
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

      const data = await response.json();

      if (!response.ok) {
        return {
          success: false,
          errors: data.form_errors,
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
