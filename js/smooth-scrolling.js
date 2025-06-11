document.addEventListener('DOMContentLoaded', () => {
    function smoothScrollTo(targetId) {
        const targetElement = document.getElementById(targetId);
        if (targetElement) {
            const header = document.querySelector('header');
            const headerOffset = header ? header.offsetHeight : 0;
            const elementPosition = targetElement.getBoundingClientRect().top;
            const offsetPosition = elementPosition + window.pageYOffset - headerOffset - 20;

            window.scrollTo({
                top: offsetPosition,
                behavior: "smooth"
            });
        }
    }

    document.querySelectorAll('a[href]').forEach(anchor => {
        const href = anchor.getAttribute('href');
        const hashIndex = href.indexOf('#');

        if (hashIndex !== -1) {
            const hrefPath = href.substring(0, hashIndex);
            const targetId = href.substring(hashIndex + 1);

            const currentPath = window.location.pathname;
            const currentFileName = currentPath.substring(currentPath.lastIndexOf('/') + 1);

            const isSamePageLink = (
                hrefPath === '' || 
                hrefPath === currentPath || 
                hrefPath === currentFileName 
            );

            if (isSamePageLink) {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault(); 
                    smoothScrollTo(targetId);

                    if (history.pushState) {
                        history.pushState(null, null, '#' + targetId);
                    } else {
                        window.location.hash = targetId;
                    }
                });
            }
        }
    });

    if (window.location.hash) {
        const targetId = window.location.hash.substring(1);
        setTimeout(() => {
            smoothScrollTo(targetId);
        }, 100);
    }
});
