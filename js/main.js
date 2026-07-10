// Body
// $('body').hide().fadeIn(400);

// Navigation bar
// Mobile view
$('.btn--toggle').on('click', function(){
    $('#sidebar-section').toggleClass('is-open');
});

if (window.matchMedia('(min-width: 576px)').matches) {
    $('#sidebar-section').removeClass('is-open');
}

// $(window).on('resize', function() {
//     if (window.innerWidth >= 576) {
//         $('#sidebar-section').removeClass('is-open');
//     }
// });

// Page Scrolling
 $('a[href^="#"]').on('click', function(e) {
    var targetId = $(this).attr('href');
    var $targetElement = $(targetId);
    var scrollTopPosition;

    if (targetId === '#') {
        e.preventDefault();
        scrollTopPosition = 0;
    } 

    else if ($targetElement.length) {
        e.preventDefault();
        scrollTopPosition = $targetElement.offset().top;
    } 
    else {
        return; 
    }

    $('html, body').animate({
        scrollTop: scrollTopPosition
    }, 500); 
});

// Page Transisions

// $('.navigation a:not([href^="#"])').on('click', function(e) {
//     e.preventDefault();
//     var targetUrl = $(this).attr('href');
    
//     $('body').fadeOut(400, function() {
//         window.location.href = targetUrl;
//     });
// });

// Header
// Typing cycle
$(".typewriter").each(function() {
    new Typed(this, {
        strings: ["Designer", "Developer", "Wizard"],
        typeSpeed: 150,
        backSpeed: 50,
        typeDelay: 1500,
        loop: true
    });
});

$(".typewriter-static").each(function() {
    const $element = $(this);
    const textToType = $element.text();
    $element.text("");

    new Typed(this, {
        strings: ["", textToType],
        showCursor: false,
        backSpeed: 0,
        typeSpeed: 100 
    });
});

// Contact Form
const $requiredFields = $('.required-field');
const $emailField = $('.contact-email');
const $submitBtn = $('.contact-submit');

// Submit button 
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

// Check Email Regex
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