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

hljs.highlightAll();


