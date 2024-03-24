import { defineStore } from "pinia";

const useEventStore = defineStore("events", {
  state: () => ({
    events: [],
    previousEvents: [],
  }),
  actions: {
    async initialize() {
      if (this.events.length > 0) {
        return;
      }

      try {
        const response = await fetch(import.meta.env.VITE_API_URL + "/api/events");
        const data = await response.json();

        this.events = data.events;
        this.previousEvents = data.previousEvents;
      } catch (error) {
        console.error("Error fetching events:", error);
      }
    },
  },
});

export { useEventStore };
