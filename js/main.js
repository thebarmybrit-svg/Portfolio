// Navigation bar

$('.btn--toggle').on('click', function(){
    $('#sidebar-section').toggleClass('is-open');
});
$(window).on('resize', function() {
    if (window.innerWidth >= 576) {
        $('#sidebar-section').removeClass('is-open');
    }
});

// Header
var typed = new Typed(".typewriter", {
    strings: ["Designer", "Developer", "Wizard"],
    typeSpeed: 150,
    backSpeed: 50,
    typeDelay: 1500,
    loop: true
});