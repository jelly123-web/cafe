(function () {
    const clamp = (value, min, max) => Math.max(min, Math.min(max, value));

    document.querySelectorAll('[data-cropper]').forEach((root) => {
        const input = root.querySelector('[data-cropper-input]');
        const output = root.querySelector('[data-cropper-output]');
        const canvas = root.querySelector('[data-cropper-canvas]');
        const zoom = root.querySelector('[data-cropper-zoom]');
        const filename = root.querySelector('[data-cropper-filename]');
        const panel = root.querySelector('[data-cropper-panel]');
        const preview = root.querySelector('[data-cropper-preview]');
        const previewWrap = root.querySelector('[data-cropper-preview-wrap]');
        const clearBtn = root.querySelector('[data-cropper-clear]');
        const closeButtons = root.querySelectorAll('[data-cropper-close]');
        const ctx = canvas?.getContext('2d');
        const size = Number(root.dataset.cropperSize || 520);
        const defaultPreview = preview?.getAttribute('src') || '';
        let img = null;
        let sourceName = '';
        let baseScale = 1;
        let extraZoom = 1;
        let offsetX = 0;
        let offsetY = 0;
        let dragging = false;
        let lastX = 0;
        let lastY = 0;

        if (!input || !output || !canvas || !ctx || !zoom || !panel) return;

        canvas.width = size;
        canvas.height = size;

        const openPanel = () => {
            if (!img) return;
            panel.style.display = '';
            panel.hidden = false;
            panel.classList.add('open');
            draw();
        };

        const closePanel = () => {
            panel.classList.remove('open');
            panel.hidden = true;
            panel.style.display = 'none';
        };

        const bounds = () => {
            if (!img) return { minX: 0, maxX: 0, minY: 0, maxY: 0, scale: 1 };
            const scale = baseScale * extraZoom;
            const drawnW = img.width * scale;
            const drawnH = img.height * scale;
            const minX = Math.min(0, size - drawnW);
            const minY = Math.min(0, size - drawnH);
            return { minX, maxX: 0, minY, maxY: 0, scale };
        };

        const updatePreview = () => {
            if (!preview || !output.value) return;
            preview.src = output.value;
            if (previewWrap) previewWrap.hidden = false;
        };

        function draw() {
            if (!img) return;
            const b = bounds();
            offsetX = clamp(offsetX, b.minX, b.maxX);
            offsetY = clamp(offsetY, b.minY, b.maxY);
            ctx.clearRect(0, 0, size, size);
            ctx.fillStyle = '#fffaf5';
            ctx.fillRect(0, 0, size, size);
            ctx.drawImage(img, offsetX, offsetY, img.width * b.scale, img.height * b.scale);
            output.value = canvas.toDataURL('image/jpeg', 0.7);
            updatePreview();
        }

        const loadImageSrc = (src, name) => {
            const loaded = new Image();
            loaded.onload = () => {
                img = loaded;
                sourceName = name || 'Gambar dipilih';
                if (filename) filename.textContent = sourceName;
                zoom.value = '1';
                baseScale = Math.max(size / img.width, size / img.height);
                extraZoom = Number(zoom.value || 1);
                offsetX = (size - img.width * baseScale * extraZoom) / 2;
                offsetY = (size - img.height * baseScale * extraZoom) / 2;
                openPanel();
            };
            loaded.src = src;
        };

        const loadFile = (file) => {
            if (!file) return;
            const reader = new FileReader();
            reader.onload = (event) => loadImageSrc(event.target.result, file.name);
            reader.readAsDataURL(file);
        };

        const clearSelection = () => {
            input.value = '';
            output.value = '';
            img = null;
            sourceName = '';
            if (filename) filename.textContent = 'Belum ada file dipilih';
            if (preview) preview.src = defaultPreview;
            if (previewWrap) previewWrap.hidden = true;
            closePanel();
        };

        const pointerPosition = (event) => {
            const touch = event.touches?.[0] || event.changedTouches?.[0];
            return {
                x: touch ? touch.clientX : event.clientX,
                y: touch ? touch.clientY : event.clientY,
            };
        };

        const startDrag = (event) => {
            if (!img) return;
            dragging = true;
            const pos = pointerPosition(event);
            lastX = pos.x;
            lastY = pos.y;
            event.preventDefault();
        };

        const moveDrag = (event) => {
            if (!dragging || !img) return;
            const pos = pointerPosition(event);
            offsetX += pos.x - lastX;
            offsetY += pos.y - lastY;
            lastX = pos.x;
            lastY = pos.y;
            draw();
            event.preventDefault();
        };

        const endDrag = () => {
            dragging = false;
        };

        input.addEventListener('change', (event) => loadFile(event.target.files?.[0]));
        zoom.addEventListener('input', () => {
            if (!img) return;
            const oldScale = baseScale * extraZoom;
            const centerX = size / 2 - offsetX;
            const centerY = size / 2 - offsetY;
            extraZoom = Number(zoom.value || 1);
            const newScale = baseScale * extraZoom;
            offsetX = size / 2 - centerX * (newScale / oldScale);
            offsetY = size / 2 - centerY * (newScale / oldScale);
            draw();
        });

        preview?.addEventListener('click', openPanel);
        clearBtn?.addEventListener('click', clearSelection);
        closeButtons.forEach((button) => {
            button.addEventListener('click', (event) => {
                event.preventDefault();
                event.stopPropagation();
                closePanel();
            });
        });
        panel.addEventListener('click', (event) => {
            if (event.target.closest('[data-cropper-close]')) {
                event.preventDefault();
                event.stopPropagation();
                closePanel();
            }
        });
        panel.addEventListener('click', (event) => {
            if (event.target === panel) closePanel();
        });
        window.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && !panel.hidden) {
                closePanel();
            }
        });
        canvas.addEventListener('mousedown', startDrag);
        canvas.addEventListener('mousemove', moveDrag);
        window.addEventListener('mouseup', endDrag);
        canvas.addEventListener('touchstart', startDrag, { passive: false });
        canvas.addEventListener('touchmove', moveDrag, { passive: false });
        window.addEventListener('touchend', endDrag);
    });
})();
