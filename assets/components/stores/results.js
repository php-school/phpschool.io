import {ref} from "vue";

export const results = ref({
    results: '',
    reset() {
        this.results = '';
    },
    set(results) {
        this.results = results;
    }
})