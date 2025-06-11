// js/main.js (or hamburger.js)

document.addEventListener('DOMContentLoaded', () => {
    const navToggle = document.getElementById('navToggle');
    // --- FIX: Get the actual <i> icon element inside navToggle ---
    const navToggleIcon = navToggle ? navToggle.querySelector('i.fas') : null;

    const navLinks = document.getElementById('navLinks');
    const overlay = document.createElement('div'); // Create overlay element
    overlay.classList.add('overlay');
    document.body.appendChild(overlay); // Append overlay to body

    // Toggle menu function
    function toggleMenu() {
        navLinks.classList.toggle('nav-open');

        // --- FIX: Toggle icon classes on the actual icon element (navToggleIcon) ---
        if (navToggleIcon) { // Ensure the icon element exists
            navToggleIcon.classList.toggle('fa-bars'); // Toggle the hamburger icon
            navToggleIcon.classList.toggle('fa-times'); // Toggle the 'X' icon
        }

        overlay.classList.toggle('is-visible'); // Show/hide overlay
        document.body.classList.toggle('no-scroll'); // Prevent body scroll
    }

    // Event listener for hamburger icon click
    if (navToggle) { // Check if navToggle exists before adding listener
        navToggle.addEventListener('click', toggleMenu);
    }

    // Event listener for overlay click to close menu
    if (overlay) { // Check if overlay exists before adding listener
        overlay.addEventListener('click', toggleMenu);
    }

    // Close menu when a navigation link is clicked (for smoother UX)
    document.querySelectorAll('#navLinks a').forEach(link => {
        link.addEventListener('click', () => {
            if (navLinks.classList.contains('nav-open')) { // Only close if menu is open
                toggleMenu(); // Close the menu
            }
        });
    });
});