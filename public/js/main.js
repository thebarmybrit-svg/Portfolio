// Dark Mode
const themeToggle = document.getElementById('theme-toggle');
const toggleIcon = themeToggle.querySelector('.toggle-icon');

const savedTheme = localStorage.getItem('portfolio-theme');
const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

const isDark = savedTheme === 'dark' || (!savedTheme && systemPrefersDark);

const setTheme = (toDark) => {
    if (toDark) {
        document.documentElement.setAttribute('data-theme', 'dark');
        toggleIcon.classList.add('icon-sun');
        toggleIcon.classList.remove('icon-moon-stroke');
        localStorage.setItem('portfolio-theme', 'dark');
    } else {
        document.documentElement.removeAttribute('data-theme');
        toggleIcon.classList.add('icon-moon-stroke');
        toggleIcon.classList.remove('icon-sun');
        localStorage.setItem('portfolio-theme', 'light');
        
    }
};
setTheme(isDark);

themeToggle.addEventListener('click', () => {
    const currentTheme = document.documentElement.getAttribute('data-theme');
    setTheme(currentTheme !== 'dark');
});

// Navigation bar
// Mobile view
function initSidebarState() {
    if (window.matchMedia('(max-width: 575.98px)').matches) {
        $('#sidebar-section').removeClass('is-open');
    }
}

$('.btn--toggle').on('click', function(){
    $('#sidebar-section').toggleClass('is-open');
});

// Change between Mobile view to Desktop view
function checkSidebarResolution() {
    if (window.matchMedia('(min-width: 575.98px)').matches) {
        $('#sidebar-section').removeClass('is-open');
    }
}
$(window).on('resize', checkSidebarResolution);
initSidebarState(); 

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

// Consolidated Page Scrolling and Page Transitions
$('.navigation-item a, .initials a').on('click', function(event) {
    var targetUrl = $(this).attr('href');
    if (!targetUrl) return;

    // Check if the current page is the root homepage
    var isHomepage = window.location.pathname === '/' || window.location.pathname === '/index.php';

    // If clicked a pure hash anchor (#section) OR a home-relative anchor (/#section) while ALREADY on home
    if (targetUrl.startsWith('#') || (targetUrl.startsWith('/#') && isHomepage)) {
        event.preventDefault();
        
        // Isolate the hash ID (e.g., "/#projects-section" becomes "#projects-section")
        var targetId = targetUrl.startsWith('/#') ? targetUrl.substring(1) : targetUrl;
        var $targetElement = $(targetId);
        var scrollTopPosition = 0;

        if (targetId !== '#') {
            if ($targetElement.length) {
                scrollTopPosition = $targetElement.offset().top;
            } else {
                return; // Element not found, exit
            }
        }

        // Animate smooth scrolling without reloading the page
        $('html, body').animate({
            scrollTop: scrollTopPosition
        }, 500, function() {
            // Update the URL hash in the browser address bar cleanly
            if (targetId !== '#') {
                history.pushState(null, null, targetId);
            }
        });
    } 
    // If it's a standard cross-page navigation link (e.g., /about or /#section from a subpage)
    else {
        event.preventDefault();

        // Fade out body and redirect smoothly
        $('body').animate({ opacity: 0 }, 'slow', function() {
            window.location.href = targetUrl;
        });
    }
});

// Always fade the body back in smoothly on page entry
$('body').animate({ opacity: 1 }, 'slow');

// Handle incoming cross-page links with hashes (e.g., navigating from /about back to /#projects-section)
$(window).on('load', function() {
    if (window.location.hash) {
        var $targetElement = $(window.location.hash);
        if ($targetElement.length) {
            // Small timeout prevents animation jumping while assets render
            setTimeout(function() {
                $('html, body').animate({
                    scrollTop: $targetElement.offset().top
                }, 500);
            }, 150);
        }
    }
});


// Header
// Typing cycle
$(".typewriter").each(function() {
    new Typed(this, {
        strings: ["Designer", "Developer", "Wizard"],
        typeSpeed: 100,
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

    if (windowWidth < 575.98) {
        if ($slider.hasClass('slick-initialized')) {
            $slider.slick('unslick');
        }
        return;
    }

    // Determine slides based on explicit viewport conditions
    var targetSlides = (windowWidth >= 991.98) ? 3 : 2;

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

// Code Example Carosel
$('.coding-examples-grid').slick({
    slidesToShow: 1,
    slidesToScroll: 1,
    infinite: true,
    dots: true,
    arrows: true,
    adaptiveHeight: true
});

// Bind cleanly to standard browser viewport actions
$(window).on('load resize orientationchange', function() {
    initProjectSlider();
});


// Contact Form
const $requiredFields = $('.required-field');
const $emailField = $('.contact-email');
const $submitBtn = $('.contact-submit');
const $formResponse = $('#form-response');

// Submit button validation toggle
$requiredFields.on('input', function() {
    let allFilled = true;    
    $requiredFields.each(function() {
        if ($(this).val().trim() === '') {
            allFilled = false;
            return false;
        }
    });
    $submitBtn.prop('disabled', !allFilled);
});

// Handle Form Submission with AJAX
$('.contact-form').on('submit', function(e) {
    e.preventDefault(); // Stop standard page redirection

    const emailValue = $emailField.val().trim();
    const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

    // Client-side validation check
    if (!emailRegex.test(emailValue)) {
        $formResponse.html('<div class="response-message response-error">Please enter a valid email address.</div>');
        $emailField.focus().addClass('error-field');
        return false;
    }
    $emailField.removeClass('error-field');

    // Send Form Data to PHP Endpoint asynchronously
    $.ajax({
        url: $(this).attr('action'),
        type: 'POST',
        data: $(this).serialize(),
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Success: Alert user and reset inputs
                $formResponse.html('<div class="response-message response-success">' + response.message + '</div>');
                $('.contact-form')[0].reset();
                $submitBtn.prop('disabled', true);
            } else {
                // Server-side validation failed
                $formResponse.html('<div class="response-message response-error">' + response.message + '</div>');
            }
        },
        error: function() {
            // General Network/Server connection failure
            $formResponse.html('<div class="response-message response-error">An unexpected error occurred. Please try again later.</div>');
        }
        // error: function(xhr, status, error) {
        //     // This catches and prints out the exact PHP compilation error
        //     let customErrorMessage = xhr.responseText ? xhr.responseText : "Internal Server Error (500)";
        //     $formResponse.html('<div class="response-message response-error">Error: ' + customErrorMessage + '</div>');
        // }
    });
});

// Phone Regex
// const phoneRegex = /^(\+\d{1,3}[- ]?)?\(?\d{3}\)?[- ]?\d{3}[- ]?\d{4}$/;
