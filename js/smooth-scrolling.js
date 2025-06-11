// js/smooth-scroll.js

document.addEventListener('DOMContentLoaded', () => {
    // Function to handle smooth scrolling to a target ID
    function smoothScrollTo(targetId) {
        const targetElement = document.getElementById(targetId);
        if (targetElement) {
            // Get header height for offset (use optional chaining for safety)
            const header = document.querySelector('header');
            const headerOffset = header ? header.offsetHeight : 0; // Fallback to 0 if header not found

            // Calculate the position to scroll to
            const elementPosition = targetElement.getBoundingClientRect().top;
            // window.pageYOffset is current scroll position
            // - headerOffset to account for fixed header
            // - 20 for a little extra padding below the header
            const offsetPosition = elementPosition + window.pageYOffset - headerOffset - 20;

            window.scrollTo({
                top: offsetPosition,
                behavior: "smooth"
            });
        }
    }

    // 1. Handle clicks on all links that have a hash in their href attribute
    document.querySelectorAll('a[href]').forEach(anchor => {
        const href = anchor.getAttribute('href');
        const hashIndex = href.indexOf('#');

        // Check if the href contains a hash
        if (hashIndex !== -1) {
            const hrefPath = href.substring(0, hashIndex); // e.g., 'index.php' from 'index.php#contact'
            const targetId = href.substring(hashIndex + 1); // e.g., 'contact' from 'index.php#contact'

            // Determine if it's a link to the current page's path or a pure hash link
            const currentPath = window.location.pathname; // e.g., '/index.php'
            const currentFileName = currentPath.substring(currentPath.lastIndexOf('/') + 1); // e.g., 'index.php'

            const isSamePageLink = (
                hrefPath === '' || // For pure hash links like <a href="#contact">
                hrefPath === currentPath || // For full path matches like <a href="/index.php#contact">
                hrefPath === currentFileName // For filename matches like <a href="index.php#contact">
            );

            // If it's a link pointing to a section on the current page
            if (isSamePageLink) {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault(); // Prevent the browser's default immediate jump
                    smoothScrollTo(targetId);

                    // Optional: Update the URL hash without jumping, good for back button
                    if (history.pushState) {
                        history.pushState(null, null, '#' + targetId);
                    } else {
                        window.location.hash = targetId;
                    }
                });
            }
        }
    });

    // 2. Handle scrolling to a hash in the URL on initial page load (e.g., coming from another page)
    if (window.location.hash) {
        const targetId = window.location.hash.substring(1);
        // Add a small delay to ensure the page's layout is fully rendered
        // before calculating scroll position, especially useful for cross-page links.
        setTimeout(() => {
            smoothScrollTo(targetId);
        }, 100);
    }
});