var $ = jQuery;

$(function () {

    var $submitForm = $('#ws-submit-form');
    var $formTrigger = $('.button-submit');
    var $formErrors = $('.form__errors');
    var $workshopError = $('.workshop-errors');
    var $workshopSuccess = $('.workshop-success');

    var elementMap = {
        'github-url' : '.gh-errors',
        'email': ".email-errors",
        'workshop-name': '.workshop-name-errors',
        'contact': '.contact-errors',
        'name': '.name-errors'
    };

    /**
     * Loops through the form errors object and a applies the right message under the right input
     * @param dataObject
     */
    function formErrors(errors) {
        for (var field in errors) {
            if (!errors.hasOwnProperty(field)) {
                continue;
            }
            var messages = errors[field];
            var errorContainer = $(elementMap[field]);

            for (var message in messages) {
                if (!messages.hasOwnProperty(message)) {
                    continue;
                }

                errorContainer
                    .addClass('active')
                    .append('<li>' + messages[message] + '</li>');
            }
        }
    }

    /**
     * Loops through the workshop error object and adds the message to the bottom of the form
     * @param dataObject
     */
    function workshopErrors(dataObject) {
        $workshopError.append('<p>We checked out the workshop you submitted and we found a few problems, they are listed below. Feel free to jump on Slack if you need any more help!</p>');
        for (var key in dataObject) {
            if (!dataObject.hasOwnProperty(key)) {
                continue;
            }
            var obj = dataObject[key];

            for (var prop in obj) {
                if (!obj.hasOwnProperty(prop)) continue;
                $workshopError.addClass('active').append('<li>' + obj[prop] + '</li>');
            }
        }
    }

    function formReset() {
        setTimeout(function () {
            $formTrigger.removeClass('button-submit--error')
        }, 5000);
    }

    /**
     * Form submit function and ajax request
     */
    $submitForm.on('submit', function (evt) {
        evt.preventDefault();
        $formTrigger.addClass('button-submit--loading');

        $.ajax({
            url: '/submit',
            method: 'POST',
            data: $submitForm.serialize(),
            dataType: 'json',

            success: function (data) {
                $formTrigger.removeClass('button-submit--loading');
                if (data.success === false) {
                    $formTrigger.addClass('button-submit--error');
                    if (data.form_errors) {
                        formErrors(data.form_errors);
                        formReset();
                    }
                    if (data.workshop_errors) {
                        workshopErrors(data.workshop_errors);
                        formReset();
                    }
                } else {
                    $formErrors.removeClass('active').empty();
                    $formTrigger.addClass('button-submit--success');
                    $workshopSuccess.addClass('active');
                }
            },
            error: function () {
                $formTrigger.removeClass('button-submit--loading').addClass('button-submit--error');
                $workshopError.addClass('active').append('<li>Sorry but something went wrong please try again</li>');
                formReset();
            }
        });
    });

    $submitForm.delegate(':input', 'focus', function() {
        $formTrigger.removeClass('button-submit--success')
        $workshopSuccess.removeClass('active');
    });

    /**
     * Type effect on home page
     */
    if ($('#typer').length) {
        var letters = 'Open Source Learning for PHP';
        var pos = 0;
        var target = document.getElementById('typer');

        var typingAway = window.setInterval(function () {
            if (letters[pos]) {
                target.innerHTML = target.innerHTML + letters[pos];
                pos++;
            } else {
                clearInterval(typingAway);
            }
        }, 100);
    }

    /**
     * Mobile menu js
     * @type {any}
     */
    var $menuTrigger = $('.menu-icon');
    var $mainNav = $('.site-nav__list');

    $menuTrigger.on('click', function () {
        $menuTrigger.toggleClass('active');
        $mainNav.toggleClass('active');
    });

    var $docMenuTrigger = $('.doc__side-trigger');
    var $docMenu = $('.doc__side');

    $docMenuTrigger.on('click', function () {
        var $this = $(this);
        $this.toggleClass('is-open');
        $docMenu.toggleClass('is-open');

        if ($this.hasClass('is-open')) {
            $this.text('Close Menu');
        } else {
            $this.text('Open Menu');
        }
    });

    // header typeing
    $(".header__typed").typed({
        strings: [
            "Open Source Learning for PHP ^500",
            "Fresh Teaching Experience for PHP ^500",
            "Self-paced and Interactive Learning ^500",
            "No Prior Experience Required...^500",
            "Any Level Developer Welcome! ^500",
            "This is... ^500",
            "The New Way to Learn PHP!"
        ],
        typeSpeed: 50,
        cursorChar: '<span class="curser"><svg class="svg-icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/img/icons/icons.svg#svg-cursor"></use></svg></span>',
    });

    $(".install-header__typed").typed({
        strings: [
            "TRoubl^300",
            "Troublesgoo^200",
            "*@!#",
            "Troubleshooting"
        ],
        typeSpeed: 50,
        cursorChar: '<span class="curser"><svg class="svg-icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/img/icons/icons.svg#svg-cursor"></use></svg></span>',
    });

    //tab switcher
    $('.tabs-menu > li > a').click(function (event) {
        event.preventDefault();
        $(this).parent().addClass("active");

        $(this).parent().siblings().removeClass("active");
        var tab = $(this).attr("href");
        $('.tab-content').not(tab).hide();
        $(tab).fadeIn();
        return false;
    });

    var hash = document.location.hash;
    if ($(".tabs-container > .tab").length && hash.length) {
        //if there are some tabs on this page and we have a hash
        processTabHash(hash);
    }

    //Smooth Scroll
    $('a[href*="#"]:not([href="#"]):not([href^="#tab"])').click(function () {
        if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) {
            var target = $(this.hash);
            var hash = this.hash;
            target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
            if (target.length) {
                $('html, body').animate({
                    scrollTop: target.offset().top - 22
                }, 1000, function () {
                    window.location.hash = hash;
                });
                return false;
            }
        }
    });

    if ($('#search-input').length) {
        docsearch({
            apiKey: '839c0aa3f3df6404158b249b3f84774f',
            indexName: 'phpschool',
            inputSelector: '#search-input'
        });
    }

    /**
     * Try to either open a tab (match id to hash) and scroll to it,
     * or if there is an element inside a tab with a id matching the has, open the tab
     * and scroll to the element.
     *
     * @param string hash
     */
    function processTabHash(hash) {
        if (hash.indexOf("#tab-") === 0) {
            //we want to visit specific tab
            var tab = $('a[href="' + hash + '"]');

            if (!tab.length) {
                return;
            }

            var target = tab.closest(".tabs-container").prev();
            $('html, body').animate({
                scrollTop: target.offset().top
            }, 1000, function () {
                window.location.hash = hash;
            });

            tab.click();
        }

        var target = $(".tabs-container > .tab").find(hash);
        if (!target.length) {
            //cant find elem with this id in any tabs
            return;
        }

        //we want to visit content inside a tab
        var tab = target.closest(".tab-content");
        var tabId = tab.attr("id");
        var tabMenuItem = tab.parent().prev().find('a[href="#' + tabId + '"]').parent();

        tab.show().siblings().hide();
        tabMenuItem.addClass('active').siblings().removeClass('active');

        $('html, body').animate({
            scrollTop: target.offset().top
        }, 1000, function () {
            window.location.hash = hash;
        });
    }
});

hljs.initHighlightingOnLoad();


