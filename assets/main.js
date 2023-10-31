var $ = jQuery;

import "../scss/core.scss";

$(function () {

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

    if ($('#search-input').length) {
        var search = docsearch({
            apiKey: '839c0aa3f3df6404158b249b3f84774f',
            indexName: 'phpschool',
            inputSelector: '#search-input'
        });

        search.autocomplete.on('autocomplete:opened', function (e) {
            $('.site-body').addClass('overlay');
        });

        search.autocomplete.on('autocomplete:closed', function (e) {
            $('.site-body').removeClass('overlay');
        });
    }
});

hljs.highlightAll();


