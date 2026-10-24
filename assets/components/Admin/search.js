import { computed } from "vue";

// Items whose given fields contain the search text, ignoring case.
export const useSearch = (items, search, fields) =>
  computed(() => {
    const text = (search() ?? "").toLowerCase();

    return text ? items.value.filter((item) => fields.some((field) => item[field]?.toLowerCase().includes(text))) : items.value;
  });
