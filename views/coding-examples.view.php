<?php require base_path('views/partials/header.php') ?>
        
        <?php require base_path('views/partials/sidemenu.php') ?>

        
        <div id="main">
            <div id="coding-examples">
                <div class="container">
                    <div class="coding-examples-grid">
                        <div class="coding-examples-slide">
                            <h2>Portfolio - HTML</h2>
                            <div class="code-container">
                                <pre><code class="code-raw">
&lt;div id="sidebar-section"&gt;
    &lt;div class="sidebar-container"&gt;
        &lt;div class="grid"&gt;
            &lt;div class="initials"&gt;
                &lt;a class="h1" href=""&gt;AB&lt;/a&gt;
            &lt;/div&gt;
            &lt;a id="theme-toggle" class="btn btn--dark" aria-label="Toggle dark mode"&gt;
                &lt;em class="toggle-icon icon"&gt;&lt;/em&gt;
            &lt;/a&gt;
            &lt;ul class="navigation"&gt;
                &lt;li class="navigation-item"&gt;
                    &lt;a href="About_Me"&gt;About Me&lt;/a&gt;
                &lt;/li&gt;
                &lt;li class="navigation-item"&gt;
                    &lt;a href="#projects-section"&gt;Projects&lt;/a&gt;
                &lt;/li&gt;
                &lt;li class="navigation-item"&gt;
                    &lt;a href="Coding_Examples/"&gt;Coding Examples&lt;/a&gt;
                &lt;/li&gt;
                &lt;li class="navigation-item"&gt;
                    &lt;a href="SCS_Scheme"&gt;SCS Scheme&lt;/a&gt;
                &lt;/li&gt;
                &lt;li class="navigation-item"&gt;
                    &lt;a href="#contact-form-section"&gt;Contact Me&lt;/a&gt;
                &lt;/li&gt;
            &lt;/ul&gt;
            &lt;div class="sidebar-social-logos"&gt;
                &lt;a class="github" href="https://github.com" target="_blank" rel="noopener"&gt;
                    &lt;span class="icon-github"&gt;&amp;nbsp;&lt;/span&gt;
                &lt;/a&gt;
                &lt;a class="linkedin" href="https://linkedin.com" target="_blank" rel="noopener"&gt;
                    &lt;span class="icon-linkedin"&gt;&amp;nbsp;&lt;/span&gt;
                &lt;/a&gt;
            &lt;/div&gt;
        &lt;/div&gt;
    &lt;/div&gt;
&lt;/div&gt;
                                </code></pre>
                            </div>
                            <p>This creates a navigation sidebar to go onto all of pages in the website. It also defines various linked sections to navigate towards once clicked by the user.</p>
                            <p>I used this to allow users to navigate to all of the pages in the website, as well as eternal social sites. 
                                Additionally, a Dark Mode toggle is used here to allow users to have a darker style if that is their preference. 
                                The fuctionality of which is handled by Java and SCSS.</p>
                        </div>
                        <div class="coding-examples-slide">
                            <h2>Netmatters Homepage - SCSS</h2>
                            <div class="code-container">
                                <pre><code class="code-raw">

        &amp;:not(:first-child):not(:last-child){
            position: relative;
            top: 0; 
            transition: top $trns-duration ease;
            @extend .centered;
            background-color: $white;
            box-shadow: 0px 0px 5px 2px $colour-shadow;
            border-radius: $br--default;
            padding: 0;

            &amp;:hover {
                top: -10px; 
                transition: $trns-duration ease;
                cursor: pointer;
            }
            .item{
                height: 100%;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                max-width: 100%; 
                .article-link{
                    position: absolute;
                    top: 0;
                    right: 0;
                    bottom: 0;
                    left: 0;
                    z-index: 2;
                }
                img {
                    max-width: 100%;
                    height: auto;
                    object-fit: contain;
                }
            }

            .block{
                padding: em(30px);
            }
            .user{
                display: flex;
                justify-content: left;
                align-items: center;
                width: 100%;
                border-top: 1px $black solid;
                padding-top: em(30px);
                margin-top: em(30px);
                .details{
                    padding-left: em(20px);
                }
            }
        }
                                </code></pre>
                            </div>
                            <p>The targets specifically the articles of the latest news section of the website, with declerations for the first and last child elsewhere in the file.</p>
                            <p>This allows for a dynamic styling of the section, 
                                allowing any future articles that are added before the last child to match the styling of the others in the section.</p>
                        </div>
                        <div class="coding-examples-slide">
                            <h2>JavaScript Array Assignment - JavaScript</h2>
                            <div class="code-container">
                                <pre><code class="code-raw">
// EMAIL FORM SUBMISSION
document.getElementById('emailForm').addEventListener('submit', function(event) {
    event.preventDefault();

    const emailInput = document.getElementById('user-email');
    const emailValue = emailInput.value.trim();
    // Email Validation pattern
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    // Validate the email format
    if (!emailPattern.test(emailValue)) {
        // Show error popup if incorrect
        alert(`The email "${emailValue}" you inputted is not an acceptable email address. 
            \nPlease check the formatting and try again.`);
        emailInput.style.borderColor = 'red';
        emailInput.focus();
        return; 
    }

    emailInput.style.borderColor = '';

    // Render directly to UI
    renderEntryToDOM(emailValue, currentImageDataUrl);

    // Save permanently to localStorage
    saveEntryToStorage(emailValue, currentImageDataUrl);

    // Clean up interface for next entry
    emailInput.value = '';
    loadRandomImage();
});
                                </code></pre>
                            </div>
                            <p>This code allows the email form in the website to couple the desired email with the randomally generated image. 
                                It also calls the function to create a new random image without needing to refresh the page to do so.</p>
                            <p>It also has safeguards to check if the email is valid, 
                                and uses functions that handle the implementation of the assigned parameters into the webpage. 
                                The use of functions was nessisary to allow for decoupling of the code reliance on itself and for testing.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php require base_path('views/partials/footer.php') ?>