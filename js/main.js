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

// Projects Carosel
function initProjectSlider() {
    var $slider = $('.projects-grid');
    var windowWidth = $(window).width();

    if (windowWidth < 576) {
        if ($slider.hasClass('slick-initialized')) {
            $slider.slick('unslick');
        }
        return;
    }

    // Determine slides based on explicit viewport conditions
    var targetSlides = (windowWidth >= 992) ? 3 : 2;

    if (!$slider.hasClass('slick-initialized')) {
        // First-time load config
        $slider.slick({
            dots: true,
            arrows: false,
            slidesToShow: targetSlides,
            slidesToScroll: targetSlides,
            autoplay: true,
            autoplaySpeed: 5000,
            adaptiveHeight: true
        });
    } else {
        var currentOptions = $slider.slick('getSlick');
        if (currentOptions.options.slidesToShow !== targetSlides) {
            $slider.slick('slickSetOption', 'slidesToShow', targetSlides, false);
            $slider.slick('slickSetOption', 'slidesToScroll', targetSlides, true);
        }
    }
}

// Bind cleanly to standard browser viewport actions
$(window).on('load resize orientationchange', function() {
    initProjectSlider();
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