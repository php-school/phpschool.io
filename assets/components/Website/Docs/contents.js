import DocIndex from "./Sections/Index.vue";
import TutorialIndex from "./Sections/Tutorial/Index.vue";
import CreatingYourOwnWorkshop from "./Sections/Tutorial/CreatingYourOwnWorkshop.vue";
import ModifyTheme from "./Sections/Tutorial/ModifyTheme.vue";
import CreatingAnExercise from "./Sections/Tutorial/CreatingAnExercise.vue";
import ReferenceIndex from "./Sections/Reference/Index.vue";
import TheContainer from "./Sections/Reference/TheContainer.vue";
import AvailableServices from "./Sections/Reference/AvailableServices.vue";
import ExerciseTypes from "./Sections/Reference/ExerciseTypes.vue";
import ExerciseSolutions from "./Sections/Reference/ExerciseSolutions.vue";
import Results from "./Sections/Reference/Results.vue";
import ExerciseChecks from "./Sections/Reference/ExerciseChecks.vue";
import BundledChecks from "./Sections/Reference/BundledChecks.vue";
import SimpleChecks from "./Sections/Reference/SimpleChecks.vue";
import CreatingCustomResults from "./Sections/Reference/CreatingCustomResults.vue";
import CreatingCustomResultRenderers from "./Sections/Reference/CreatingCustomResultRenderers.vue";
import Events from "./Sections/Reference/Events.vue";
import CreatingListenerChecks from "./Sections/Reference/CreatingListenerChecks.vue";
import SelfCheckingExercises from "./Sections/Reference/SelfCheckingExercises.vue";
import ExerciseEvents from "./Sections/Reference/ExerciseEvents.vue";
import PatchingExerciseSolutions from "./Sections/Reference/PatchingExerciseSolutions.vue";

const docs = [
    {
        "path": "",
        "title": "Documentation Home",
        "sections": [
            {
                'path': '',
                'title': 'Documentation Home',
                'component': DocIndex,
                'file': 'Index.vue'
            }
        ]
    },
    {
        "path": "tutorial",
        'title': 'Tutorial',
        'sections': [
            {
                'path': '',
                'title': 'Workshop Tutorial',
                'component': TutorialIndex,
                'file': 'Tutorial/Index.vue'
            },
            {
                'path': 'creating-your-own-workshop',
                'title': 'Creating your own workshop',
                'component': CreatingYourOwnWorkshop,
                'file': 'Tutorial/CreatingYourOwnWorkshop.vue'
            },
            {
                'path': 'modify-theme',
                'title': 'Modifying the theme of your workshop',
                'component': ModifyTheme,
                'file': 'Tutorial/ModifyTheme.vue'
            },
            {
                'path': 'creating-an-exercise',
                'title': 'Creating an exercise',
                'component': CreatingAnExercise,
                'file': 'Tutorial/CreatingAnExercise.vue'
            },
        ]
    },
    {
        "path": "reference",
        'title': 'Reference Documentation',
        'sections': [
            {
                'path': '',
                'title': 'Reference Documentation',
                'component': ReferenceIndex,
                'file': 'Reference/Index.vue'
            },
            {
                'path': 'container',
                'title': 'The Container',
                'component': TheContainer,
                'file': 'Reference/TheContainer.vue'
            },
            {
                'path': 'available-services',
                'title': 'Available Services',
                'component': AvailableServices,
                'file': 'Reference/AvailableServices.vue'
            },
            {
                'path': 'exercise-types',
                'title': 'Exercise Types',
                'component': ExerciseTypes,
                'file': 'Reference/ExerciseTypes.vue'
            },
            {
                'path': 'exercise-solutions',
                'title': 'Exercise Solutions',
                'component': ExerciseSolutions,
                'file': 'Reference/ExerciseSolutions.vue'
            },
            {
                'path': 'results',
                'title': 'Results & Renderers',
                'component': Results,
                'file': 'Reference/Results.vue'
            },
            {
                'path': 'exercise-checks',
                'title': 'Exercise Checks',
                'component': ExerciseChecks,
                'file': 'Reference/ExerciseChecks.vue'
            },
            {
                'path': 'bundled-checks',
                'title': 'Bundled Checks',
                'component': BundledChecks,
                'file': 'Reference/BundledChecks.vue'
            },
            {
                'path': 'creating-simple-checks',
                'title': 'Creating Simple Checks',
                'component': SimpleChecks,
                'file': 'Reference/SimpleChecks.vue'
            },
            {
                'path': 'creating-custom-results',
                'title': 'Creating Custom Results',
                'component': CreatingCustomResults,
                'file': 'Reference/CreatingCustomResults.vue'
            },
            {
                'path': 'creating-custom-result-renderers',
                'title': 'Creating Custom Result Renderers',
                'component': CreatingCustomResultRenderers,
                'file': 'Reference/CreatingCustomResultRenderers.vue'
            },
            {
                'path': 'events',
                'title': 'Events',
                'component': Events,
                'file': 'Reference/Events.vue'
            },
            {
                'path': 'creating-listener-checks',
                'title': 'Creating Listener Checks',
                'component': CreatingListenerChecks,
                'file': 'Reference/CreatingListenerChecks.vue'
            },
            {
                'path': 'self-checking-exercises',
                'title': 'Self Checking Exercises',
                'component': SelfCheckingExercises,
                'file': 'Reference/SelfCheckingExercises.vue'
            },
            {
                'path': 'exercise-events',
                'title': 'Exercise Events',
                'component': ExerciseEvents,
                'file': 'Reference/ExerciseEvents.vue'
            },
            {
                'path': 'patching-exercise-solutions',
                'title': 'Patching Exercise Submissions',
                'component': PatchingExerciseSolutions,
                'file': 'Reference/PatchingExerciseSolutions.vue'
            },
        ]
    }
];

const sectionRoute = (group, section) => {
    const parts = ['docs', group.path, section.path];

    return '/' + parts.filter(part => part !== '').join('/');
};

const docRoutes = (docs) => {
    return [].concat(...docs.map(doc => {
        return doc.sections.map(section => {
            return {
                path: sectionRoute(doc, section),
                component: section.component,
            };
        });
    }))
}

export {docs as docs, sectionRoute as sectionRoute, docRoutes as docRoutes};