var $ = jQuery;

$(function () {

    var $menuTrigger = $('.menu-icon');
    var $mainNav = $('.navigation');

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

    //Smooth Scroll
    $('a[href*="#"]:not([href="#"])').click(function () {
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
});

hljs.initHighlightingOnLoad();


