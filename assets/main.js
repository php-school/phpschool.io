import 'vite/modulepreload-polyfill'
import './styles'
import { createApp } from 'vue'
import AceEditor from './components/AceEditor.vue'
import FileTree from './components/FileTree.vue'
import TreeItem from './components/TreeItem.vue'
import Tabs from './components/Tabs.vue'
import Tab from './components/Tab.vue'
import Modal from './components/Modal.vue'
import StudentProgress from './components/StudentProgress.vue'
import WorkshopExerciseSelectionList from './components/WorkshopExerciseSelectionList.vue'
import PassNotification from "./components/PassNotification.vue";
import ExerciseVerify from "./components/ExerciseVerify.vue";
import ExerciseEditor from "./components/ExerciseEditor.vue";

const components = {
    AceEditor,
    FileTree,
    TreeItem,
    Tabs,
    Tab,
    Modal,
    StudentProgress,
    WorkshopExerciseSelectionList,
    PassNotification,
    ExerciseVerify,
    ExerciseEditor
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