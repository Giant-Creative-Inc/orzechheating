jQuery( document ).ready(function() {
    jQuery('.home-hero').append('<a href="#about" class="slider-down-arrow-full" style="transform: translateY(0px); opacity: 1;"><i class="icon-salient-down-arrow icon-default-style">  </i></a>');
    jQuery('body').on('click','.full-page-inner a.slider-down-arrow-full',function(){
        jQuery.fn.fullpage.moveSectionDown();
        console.log('clicked');
    });
});

document.addEventListener("DOMContentLoaded", function () {
    // Select all links
    const links = document.querySelectorAll('a[target="_blank"]');

    links.forEach(function (link) {
        // Add aria-label if not already present
        if (!link.hasAttribute('aria-label')) {
            link.setAttribute('aria-label', 'Opens in a new tab');
        }

        // Add a visually hidden span for screen readers
        const screenReaderText = document.createElement('span');
        screenReaderText.classList.add('screen-reader-text');
        screenReaderText.textContent = ' (opens in a new tab)';
        link.appendChild(screenReaderText);
    });

    const backgroundDivs = document.querySelectorAll('.row-bg');

    backgroundDivs.forEach((div) => {
        const style = div.style.backgroundImage;
        const imageUrl = style.match(/url\("(.+)"\)/)?.[1];
        if (imageUrl) {
            div.setAttribute('aria-hidden', 'true');
        }
    });

    setTimeout(() => {
        const prevButtons = document.querySelectorAll('.prev');
        const nextButtons = document.querySelectorAll('.next');
    
        console.log({ nextButtons });
    
        prevButtons.forEach(button => {
            button.setAttribute('aria-label', 'Go to the previous slide');
        });
    
        nextButtons.forEach(button => {
            button.setAttribute('aria-label', 'Go to the next slide');
        });
    }, 500); // adjust delay as needed

    // Ensure the main content has proper attributes
    const mainContent = document.querySelector('.container.main-content');
    if (mainContent) {
        mainContent.setAttribute('role', 'main');
        mainContent.setAttribute('aria-label', 'Main content area');
    }

    // Ensure the footer has proper attributes
    const footer = document.querySelector('.footer');
    if (footer) {
        footer.setAttribute('role', 'contentinfo');
        footer.setAttribute('aria-label', 'Footer area');
    }

    const bgDiv = document.querySelector(".row-bg");
    if (bgDiv) {
        bgDiv.setAttribute("role", "img");
        bgDiv.setAttribute("aria-label", "Call-to-action banner featuring Orzech Heating services.");
    }
});
  