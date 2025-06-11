document.addEventListener('DOMContentLoaded', () => {
    const navToggle = document.getElementById('navToggle');
    const navToggleIcon = navToggle ? navToggle.querySelector('i.fas') : null;

    const navLinks = document.getElementById('navLinks');
    const overlay = document.createElement('div');
    overlay.classList.add('overlay');
    document.body.appendChild(overlay);

    function toggleMenu() {
        navLinks.classList.toggle('nav-open');
        if (navToggleIcon) {
            navToggleIcon.classList.toggle('fa-bars'); 
            navToggleIcon.classList.toggle('fa-times');
        }

        overlay.classList.toggle('is-visible'); 
        document.body.classList.toggle('no-scroll'); 
    }

    if (navToggle) { 
        navToggle.addEventListener('click', toggleMenu);
    }

    if (overlay) {
        overlay.addEventListener('click', toggleMenu);
    }

    document.querySelectorAll('#navLinks a').forEach(link => {
        link.addEventListener('click', () => {
            if (navLinks.classList.contains('nav-open')) { 
                toggleMenu();
            }
        });
    });
});
