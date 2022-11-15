import 'vite/modulepreload-polyfill'
import './styles'
import { createApp } from 'vue'


const components = {
}

const app = createApp({
    components
});

app.config.unwrapInjectedRef = true;

app.directive('click-outside', {
    mounted(el, binding, vnode) {
        el.clickOutsideEvent = function(event) {
            if (!(el === event.target || el.contains(event.target))) {
                binding.value(event, el);
            }
        };
        document.body.addEventListener('click', el.clickOutsideEvent);
    },
    unmounted(el) {
        document.body.removeEventListener('click', el.clickOutsideEvent);
    }
});

app.mount('#app');