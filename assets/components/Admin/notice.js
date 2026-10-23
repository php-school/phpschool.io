import { reactive } from "vue";

// One alert per page, bound with <alert v-bind="notice" :timeout="4000" @close="notice.show = false" />.
export const useNotice = () => {
  const notice = reactive({ show: false, type: "success", message: "" });

  const show = (type, message) => Object.assign(notice, { show: true, type, message });

  return {
    notice,
    success: (message) => show("success", message),
    error: (error) => show("error", error?.message || "An error occurred. Please try again later."),
  };
};
