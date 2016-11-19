let merge = require('deepmerge');

class Instructor {
    constructor(path, container) {
        this.path = path;
        this.container = $(container);

        let firstQuestion = path.next;

        for (let key of Object.keys(firstQuestion.answers)) {
            firstQuestion.answers[key] = {};
        }

        this.currentPath  = {
            "type": "question",
            "next": firstQuestion
        };
    }

    render() {
        let container = this.container;

        container.html('');
        let instructor = this;

        let renderNext = function (node) {
            let type     = node.type.charAt(0).toUpperCase() + node.type.slice(1);
            let nextNode = instructor['render'+type](node);

            if (nextNode.hasOwnProperty('next')) {
                renderNext(nextNode);
            }
        };

        renderNext(this.currentPath);

        return this;
    }

    renderQuestion(node) {
        let next = {};
        let html = `<div id="${node.id}" class="question"><p>${node.next.question}</p>`;

        Object.keys(node.next.answers).forEach(function (answer) {
            html += `<button class="answer" data-value="${answer}">${answer}</button>`;
        });

        html += '</div>';

        this.container.append(html);

        if (node.hasOwnProperty('answers')) {
            next = node.answers[Object.keys(node.answers)[0]];
        }

        return next;
    }

    renderGuide(node) {
        node.next.element.clone().appendTo(this.container).show();

        return {};
    }
}

class Path {
    constructor(initialQuestion) {
        this.path = this.createQuestion(initialQuestion);
    }

    createQuestion(question) {
        return {
            "id": window.btoa(question),
            "type": "question",
            "next": {
                "question": question
            }
        };
    }

    /**
     * @returns {Object} Last object of the path
     */
    getLastStep() {
        let tunnel = function (obj) {
            for(let key of Object.keys(obj)) {
                if (key === 'answers') {
                    let answer = '';
                    for (let answerKey of Object.keys(obj[key])) {
                         answer = answerKey;
                    }

                    return tunnel(obj[key][answer])
                }

                if (key === 'next') {
                    return tunnel(obj[key]);
                }
            }

            return obj;
        };

        return tunnel(this.path);
    }

    /**
     * @param {string} answer
     * @param {string} question
     * @returns {Path}
     */
    addQuestion(answer, question) {
        let lastStep = this.getLastStep();

        lastStep['answers'] = {};
        lastStep['answers'][answer] = this.createQuestion(question);

        return this;
    }

    /**
     * @param {string} answer
     * @param {string} element
     * @returns {Path}
     */
    addGuide(answer, element) {
        let lastStep = this.getLastStep();

        lastStep['answers'] = {};
        lastStep['answers'][answer] = {
            "type": "guide",
            "next": {
                "element": $(element)
            }
        };

        return this;
    }

    /**
     * @returns {Object} Constructed path object
     */
    getPath() {
        return this.path;
    }
}

$(function (){
    let osQuestion     = 'What operating system do you use?';
    let dockerQuestion = 'Would you like to use Docker (self contained installation) ?';
    let distroQuestion = 'What Linux distribution are you using ?';

    let windowsPath = new Path(osQuestion)
        .addGuide('Windows', '#windowsGuide')
        .getPath();
    let macDockerPath = new Path(osQuestion)
        .addQuestion('Mac Os', dockerQuestion)
        .addGuide('Yes', '#macDocker')
        .getPath();
    let macNativePath = new Path(osQuestion)
        .addQuestion('Mac Os', dockerQuestion)
        .addGuide('No', '#macNative')
        .getPath();
    let linuxDockerPath = new Path(osQuestion)
        .addQuestion('Linux', dockerQuestion)
        .addGuide('Yes', '#linuxDocker')
        .getPath();
    let linuxDebianPath = new Path(osQuestion)
        .addQuestion('Linux', dockerQuestion)
        .addQuestion('No', distroQuestion)
        .addGuide('Debian', '#linuxDebian')
        .getPath();
    let linuxCentOsPath = new Path(osQuestion)
        .addQuestion('Linux', dockerQuestion)
        .addQuestion('No', distroQuestion)
        .addGuide('CentOS', '#linuxCentOS')
        .getPath();

    console.log(merge.all([
        windowsPath, macDockerPath, macNativePath, linuxDockerPath, linuxDebianPath, linuxCentOsPath
    ]));

    let path = merge.all([
        windowsPath, macDockerPath, macNativePath, linuxDockerPath, linuxDebianPath, linuxCentOsPath
    ]);

    console.log(path);

    let instructor = new Instructor(path, '.installer-questions');

    instructor.render();

    $('button.answer', '.installer-questions').on('click', function (e) {
        e.preventDefault();

        console.log($(this));

        // TODO: Update state...
    });
});