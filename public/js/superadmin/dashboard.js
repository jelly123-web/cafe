document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-width]').forEach((element) => {
        element.style.width = `${element.dataset.width}%`;
    });
});
