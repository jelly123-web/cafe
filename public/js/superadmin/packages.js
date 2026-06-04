document.addEventListener('turbo:load', () => {
    const imageInput = document.querySelector('[data-package-image-input]');
    const imagePreview = document.querySelector('[data-package-image-preview]');
    const imageLabel = document.querySelector('[data-package-image-name]');
    const menuCheckboxes = Array.from(document.querySelectorAll('[data-package-menu-checkbox]'));
    const menuCount = document.querySelector('[data-package-menu-count]');

    if (imageInput && imagePreview) {
        imageInput.addEventListener('change', () => {
            const [file] = imageInput.files || [];
            if (imageLabel) {
                imageLabel.textContent = file ? file.name : 'Belum ada file dipilih';
            }

            if (!file) {
                return;
            }

            const reader = new FileReader();
            reader.onload = (event) => {
                imagePreview.src = String(event.target?.result || imagePreview.src);
            };
            reader.readAsDataURL(file);
        });
    }

    const refreshMenuCount = () => {
        if (!menuCount) return;
        const selected = menuCheckboxes.filter((checkbox) => checkbox.checked).length;
        menuCount.textContent = `${selected} menu dipilih`;
    };

    menuCheckboxes.forEach((checkbox) => {
        checkbox.addEventListener('change', refreshMenuCount);
    });

    refreshMenuCount();
});
