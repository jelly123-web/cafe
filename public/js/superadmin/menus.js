document.addEventListener('DOMContentLoaded', () => {
    const imageInput = document.querySelector('[data-menu-image-input]');
    const imagePreview = document.querySelector('[data-menu-image-preview]');

    if (!imageInput || !imagePreview) {
        return;
    }

    imageInput.addEventListener('change', () => {
        const [file] = imageInput.files || [];
        if (!file) {
            return;
        }

        const reader = new FileReader();
        reader.onload = (event) => {
            imagePreview.src = String(event.target?.result || imagePreview.src);
        };
        reader.readAsDataURL(file);
    });
});
