var $ = jQuery;

var docSide = $('.doc__side');
var docContent = $('.doc__content');
var stickHeight = docContent.offset();

$(window).scroll(function () {
    var scroll = $(window).scrollTop();
    if (scroll >= stickHeight.top) {
        docSide.addClass('doc__side--fixed');
    } else {
        docSide.removeClass('doc__side--fixed');
    }
});

$(function() {
    $('a[href*="#"]:not([href="#"])').click(function() {
        if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
            var target = $(this.hash);
            var hash   = this.hash;
            target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
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

