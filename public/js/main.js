var $ = jQuery;

$('.menu-icon').on('click', function () {
    console.log('sdc');
    $('.menu-icon').toggleClass('active');
    $('.navigation').toggleClass('active');
});


$(function () {
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


