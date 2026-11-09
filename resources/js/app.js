import './bootstrap';

const adminMenuToggle = document.querySelector('.admin-menu-toggle');
const adminNavigation = document.getElementById('admin-navigation');
const adminNavigationBackdrop = document.querySelector('.admin-navigation-backdrop');

if (adminMenuToggle && adminNavigation && adminNavigationBackdrop) {
    const mobileNavigation = window.matchMedia('(max-width: 650px)');

    function setAdminNavigationOpen(isOpen) {
        const shouldOpen = mobileNavigation.matches && isOpen;
        adminMenuToggle.setAttribute('aria-expanded', String(shouldOpen));
        adminMenuToggle.setAttribute(
            'aria-label',
            shouldOpen ? 'Close admin navigation' : 'Open admin navigation'
        );
        adminNavigation.classList.toggle('is-open', shouldOpen);
        adminNavigationBackdrop.hidden = !shouldOpen;
        document.body.classList.toggle('admin-navigation-open', shouldOpen);
        adminNavigation.inert = mobileNavigation.matches && !shouldOpen;

        if (mobileNavigation.matches) {
            adminNavigation.setAttribute('aria-hidden', String(!shouldOpen));
        } else {
            adminNavigation.removeAttribute('aria-hidden');
        }
    }

    adminMenuToggle.addEventListener('click', () => {
        setAdminNavigationOpen(adminMenuToggle.getAttribute('aria-expanded') !== 'true');
    });
    adminNavigationBackdrop.addEventListener('click', () => setAdminNavigationOpen(false));
    adminNavigation.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', () => setAdminNavigationOpen(false));
    });
    document.addEventListener('keydown', event => {
        if (event.key === 'Escape') {
            setAdminNavigationOpen(false);
        }
    });
    window.addEventListener('resize', () => setAdminNavigationOpen(false));

    setAdminNavigationOpen(false);
}
