const menuToggle = document.querySelector('[data-menu-toggle]');
const navigation = document.querySelector('[data-navigation]');

if (menuToggle && navigation) {
    menuToggle.addEventListener('click', () => {
        const isOpen = navigation.classList.toggle('is-open');
        menuToggle.setAttribute('aria-expanded', String(isOpen));
    });

    navigation.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', () => {
            navigation.classList.remove('is-open');
            menuToggle.setAttribute('aria-expanded', 'false');
        });
    });
}

const sections = [...document.querySelectorAll('main section[id]')];
const navLinks = [...document.querySelectorAll('.primary-nav a')];
if (sections.length && navLinks.length && 'IntersectionObserver' in window) {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                navLinks.forEach((link) => link.classList.toggle('is-active', link.getAttribute('href').includes(`#${entry.target.id}`)));
            }
        });
    }, { rootMargin: '-35% 0px -55% 0px', threshold: 0 });
    sections.forEach((section) => observer.observe(section));
}

const selectionKey = 'vantage_booking_selection';
const saveSelection = (type, value) => {
    try { localStorage.setItem(selectionKey, JSON.stringify({ type, value })); } catch (error) { /* storage is optional */ }
};

document.querySelectorAll('[data-service-select]').forEach((link) => {
    link.addEventListener('click', () => saveSelection('service', link.dataset.serviceSelect));
});
document.querySelectorAll('[data-package-select]').forEach((link) => {
    link.addEventListener('click', () => saveSelection('package', link.dataset.packageSelect));
});

const serviceField = document.querySelector('[data-service-field]');
const packageField = document.querySelector('[data-package-field]');
try {
    const saved = JSON.parse(localStorage.getItem(selectionKey) || 'null');
    if (saved?.type === 'service' && serviceField) serviceField.value = saved.value;
    if (saved?.type === 'package' && packageField) packageField.value = saved.value;
    if (saved) localStorage.removeItem(selectionKey);
} catch (error) { /* storage is optional */ }

const comparison = document.querySelector('[data-comparison]');
if (comparison) {
    const range = comparison.querySelector('.comparison-range');
    const before = comparison.querySelector('[data-before]');
    const handle = comparison.querySelector('[data-handle]');
    const updateComparison = (value) => {
        before.style.width = `${value}%`;
        handle.style.left = `${value}%`;
    };
    range.addEventListener('input', (event) => updateComparison(event.target.value));
    comparison.addEventListener('keydown', (event) => {
        if (event.key === 'ArrowLeft' || event.key === 'ArrowRight') {
            event.preventDefault();
            const next = Math.min(100, Math.max(0, Number(range.value) + (event.key === 'ArrowRight' ? 5 : -5)));
            range.value = next;
            updateComparison(next);
        }
    });
    updateComparison(range.value);
}

const dateField = document.querySelector('input[name="preferred_date"]');
if (dateField && !dateField.value) {
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    dateField.min = new Date().toISOString().slice(0, 10);
}
