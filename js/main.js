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

// Form
const $requiredFields = $('.required-field');
const $emailField = $('.contact-email');
const $submitBtn = $('.contact-submit');

$requiredFields.on('input', function() {
    let allFilled = true;    
    // Check each textarea
    $requiredFields.each(function() {
        if ($(this).val().trim() === '') {
            allFilled = false;
            return false;
        }
    });

    if (allFilled) {
        $submitBtn.prop('disabled', false);
    } else {
        $submitBtn.prop('disabled', true);
    }

});

 $('.contact-form').on('submit', function(e) {
        const emailValue = $('.contact-email').val().trim();
        const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

        if (!emailRegex.test(emailValue)) {
            alert('Please enter a valid email address.');
            $('.contact-email').focus().css('border', '1px solid red');
            e.preventDefault();
            return false;
        }
        $('.contact-email').css('border', '');
    });