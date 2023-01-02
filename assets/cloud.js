import 'vite/modulepreload-polyfill'
import './styles'
import { createApp } from 'vue'
import AceEditor from './components/AceEditor.vue'
import FileTree from './components/FileTree.vue'
import TreeItem from './components/TreeItem.vue'
import Tabs from './components/Tabs.vue'
import Modal from './components/Modal.vue'
import StudentProgress from './components/StudentProgress.vue'
import WorkshopExerciseSelectionList from './components/WorkshopExerciseSelectionList.vue'
import PassNotification from "./components/PassNotification.vue";
import ExerciseVerify from "./components/ExerciseVerify.vue";
import ExerciseEditor from "./components/ExerciseEditor.vue";
import VueClickAway from "vue3-click-away";
import VueDiff from 'vue-diff';
import 'vue-diff/dist/index.css';


const components = {
    AceEditor,
    FileTree,
    TreeItem,
    Tabs,
    Modal,
    StudentProgress,
    WorkshopExerciseSelectionList,
    PassNotification,
    ExerciseVerify,
    ExerciseEditor,
}

const app = createApp({
    components
});

app.config.unwrapInjectedRef = true;

app.use(VueClickAway);
app.use(VueDiff);

app.mount('#app');