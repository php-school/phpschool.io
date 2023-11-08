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
import { FocusTrap } from 'focus-trap-vue'
import VueClickAway from "vue3-click-away";
import VueDiff from 'vue-diff';
import 'vue-diff/dist/index.css';
import VueShepherdPlugin from './shepherd-plugin';
import results from "./components/results/results.js";
import StudentDropdown from "./components/StudentDropdown.vue";
import ListWorkshops from "./components/ListWorkshops.vue";
import Home from "./components/Website/Pages/Home.vue";
import SiteNav from "./components/Website/SiteNav.vue";
import SiteFooter from "./components/Website/SiteFooter.vue";
import Events from "./components/Website/Pages/Events.vue";
import SubmitWorkshop from "./components/Website/Pages/SubmitWorkshop.vue";
import Post from "./components/Website/Pages/Blog/Post.vue";

import hljs from 'highlight.js/lib/core';
import php from 'highlight.js/lib/languages/php';
import shell from 'highlight.js/lib/languages/shell';
import javascript from 'highlight.js/lib/languages/javascript';
import markdown from 'highlight.js/lib/languages/markdown';
import json from 'highlight.js/lib/languages/json';
import Terminal from "./components/Website/Docs/Terminal.vue";
import ContentHeader from "./components/Website/Docs/ContentHeader.vue";
import DocCode from "./components/Website/Docs/Code.vue";
import Note from "./components/Website/Docs/Note.vue";
import DocList from "./components/Website/Docs/List.vue";
import DocListItem from "./components/Website/Docs/ListItem.vue";
import DocTable from "./components/Website/Docs/Table.vue";
import ExerciseTypes from "./components/Website/Docs/ExerciseTypes.vue";
import ResultRendererMappings from "./components/Website/Docs/ResultRendererMappings.vue";
import BundledChecks from "./components/Website/Docs/BundledChecks.vue";
import EventList from "./components/Website/Docs/EventList.vue";
import Offline from "./components/Website/Pages/Offline.vue";
import Heading from "./components/Website/Pages/Home/Heading.vue";
import DocTitle from "./components/Website/Docs/Title.vue";

import docsearch from '@docsearch/js';

hljs.registerLanguage('php', php);
hljs.registerLanguage('shell', shell);
hljs.registerLanguage('shell', javascript);
hljs.registerLanguage('md', markdown);
hljs.registerLanguage('json', json);


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
    StudentDropdown,
    ListWorkshops,
    Home,
    SubmitWorkshop,
    SiteNav,
    SiteFooter,
    Events,
    Post,
    Terminal,
    DocTitle,
    ContentHeader,
    DocCode,
    Note,
    DocList,
    DocListItem,
    DocTable,
    ExerciseTypes,
    ResultRendererMappings,
    BundledChecks,
    EventList,
    Offline,
    Heading
}

const app = createApp({components});

Object.entries(results).forEach(([name, resultComponent]) => {
    app.component(name, resultComponent);
});

app.component('FocusTrap', FocusTrap)

app.use(VueClickAway);
app.use(VueDiff);
app.use(VueShepherdPlugin);

app.mount('#app');

hljs.highlightAll();

docsearch({
    container: '#docsearch',
    appId: '7LDU5I0DPZ',
    indexName: 'phpschool',
    apiKey: 'ece29c8970bdd054ac5a6ef17ceb491e',
    placeholder: 'Search...',
    translations: {
        button: {buttonText: 'Search...'}
    }
});
