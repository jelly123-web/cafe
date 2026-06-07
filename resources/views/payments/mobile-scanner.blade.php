<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Scanner HP - {{ $scopeLabel }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --bg: #F9FAFB;
            --white: #FFFFFF;
            --border: #E5E7EB;
            --fg: #111827;
            --fg-secondary: #374151;
            --muted: #6B7280;
            --accent: #D97706;
            --accent-dark: #B45309;
            --accent-light: #FFFBEB;
            --green: #059669;
            --green-light: #D1FAE5;
            --red: #DC2626;
            --red-light: #FEE2E2;
            --radius-lg: 18px;
            --radius-md: 12px;
            --radius-sm: 8px;
            --shadow: 0 10px 30px rgba(17, 24, 39, 0.08);
            --font: 'Inter', system-ui, sans-serif;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            background: var(--bg);
            color: var(--fg);
            font-family: var(--font);
            min-height: 100vh;
            padding: 18px;
        }
        .shell {
            max-width: 560px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        .card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow);
            padding: 18px;
        }
        .title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 22px;
            font-weight: 900;
            margin: 0 0 6px;
        }
        .title i { color: var(--accent); }
        .subtitle {
            margin: 0;
            color: var(--muted);
            font-size: 13px;
            line-height: 1.6;
        }
        .target-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            border-radius: 999px;
            background: var(--accent-light);
            color: var(--accent-dark);
            font-size: 12px;
            font-weight: 800;
            margin-top: 14px;
        }
        .camera-preview {
            position: relative;
            overflow: hidden;
            border-radius: var(--radius-lg);
            background: #111827;
            aspect-ratio: 4 / 3;
        }
        video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        .camera-overlay {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            pointer-events: none;
        }
        .camera-frame {
            width: min(78%, 320px);
            height: min(42%, 180px);
            border: 2px solid rgba(255,255,255,0.95);
            border-radius: 18px;
            box-shadow: 0 0 0 9999px rgba(0,0,0,0.18);
        }
        .field-grid {
            display: grid;
            grid-template-columns: 1fr 92px;
            gap: 10px;
            margin-top: 14px;
        }
        .field label {
            display: block;
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            color: var(--fg-secondary);
            margin-bottom: 6px;
        }
        .field input {
            width: 100%;
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            padding: 12px 14px;
            font-size: 15px;
            outline: none;
        }
        .field input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(217, 119, 6, 0.12);
        }
        .actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 14px;
        }
        .btn {
            border: none;
            border-radius: var(--radius-md);
            padding: 12px 16px;
            font-size: 14px;
            font-weight: 800;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .btn-primary {
            background: var(--accent);
            color: var(--white);
        }
        .btn-primary:disabled { opacity: 0.6; cursor: wait; }
        .btn-secondary {
            background: var(--white);
            color: var(--fg-secondary);
            border: 1.5px solid var(--border);
        }
        .status {
            margin-top: 12px;
            padding: 12px 14px;
            border-radius: var(--radius-md);
            font-size: 13px;
            font-weight: 700;
            line-height: 1.6;
            background: #FAFBFC;
            border: 1px dashed var(--border);
            color: var(--fg-secondary);
        }
        .status.ok {
            background: var(--green-light);
            border-color: #A7F3D0;
            color: var(--green);
        }
        .status.err {
            background: var(--red-light);
            border-color: #FECACA;
            color: var(--red);
        }
        .meta {
            font-size: 12px;
            color: var(--muted);
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="shell">
        <section class="card">
            <h1 class="title"><i class="fas fa-mobile-screen-button"></i> Scanner HP</h1>
            <p class="subtitle">HP ini dipakai seperti alat scan barcode. Setelah barcode terbaca, datanya langsung dikirim ke aplikasi {{ strtolower($scopeLabel) }}.</p>
            <div class="target-badge"><i class="fas fa-link"></i> Tersambung ke: {{ $targetName }} ({{ $scopeLabel }})</div>
            @if ($expiresAt)
                <div class="meta">Link scanner aktif sampai {{ $expiresAt }}</div>
            @endif
        </section>

        <section class="card">
            <div class="camera-preview">
                <video id="cameraVideo" playsinline muted></video>
                <div class="camera-overlay"><div class="camera-frame"></div></div>
            </div>

            <div class="field-grid">
                <div class="field">
                    <label for="barcodeInput">Barcode</label>
                    <input id="barcodeInput" type="text" autocomplete="off" placeholder="Barcode akan terisi otomatis">
                </div>
                <div class="field">
                    <label for="qtyInput">Qty</label>
                    <input id="qtyInput" type="number" min="1" step="1" value="1">
                </div>
            </div>

            <div class="actions">
                <button id="startBtn" class="btn btn-primary" type="button"><i class="fas fa-camera"></i> Mulai Scan</button>
                <button id="sendBtn" class="btn btn-primary" type="button"><i class="fas fa-paper-plane"></i> Kirim ke Aplikasi</button>
                <button id="stopBtn" class="btn btn-secondary" type="button"><i class="fas fa-circle-stop"></i> Stop Kamera</button>
            </div>

            <div id="statusBox" class="status">Tekan <strong>Mulai Scan</strong>, izinkan kamera, lalu arahkan ke barcode.</div>
        </section>
    </div>

    <script>
        (() => {
            const postUrl = @json($postUrl);
            const video = document.getElementById('cameraVideo');
            const barcodeInput = document.getElementById('barcodeInput');
            const qtyInput = document.getElementById('qtyInput');
            const startBtn = document.getElementById('startBtn');
            const sendBtn = document.getElementById('sendBtn');
            const stopBtn = document.getElementById('stopBtn');
            const statusBox = document.getElementById('statusBox');

            let detector = null;
            let stream = null;
            let frameId = null;
            let cameraBusy = false;

            const setStatus = (message, type = '') => {
                statusBox.className = 'status' + (type ? ' ' + type : '');
                statusBox.innerHTML = message;
            };

            const getCsrfToken = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            const ensureDetector = async () => {
                if (detector) return detector;
                if (!window.isSecureContext) throw new Error('Scan kamera butuh HTTPS atau localhost.');
                if (!('mediaDevices' in navigator) || !navigator.mediaDevices.getUserMedia) {
                    throw new Error('Browser ini tidak mendukung akses kamera.');
                }
                if (!('BarcodeDetector' in window)) {
                    throw new Error('Browser ini belum mendukung scan barcode kamera.');
                }

                const preferredFormats = ['ean_13', 'ean_8', 'code_128', 'code_39', 'upc_a', 'upc_e', 'itf', 'codabar', 'qr_code'];
                let formats = preferredFormats;
                if (typeof window.BarcodeDetector.getSupportedFormats === 'function') {
                    const supported = await window.BarcodeDetector.getSupportedFormats();
                    const filtered = preferredFormats.filter((format) => supported.includes(format));
                    if (filtered.length) formats = filtered;
                }

                detector = new window.BarcodeDetector({ formats });
                return detector;
            };

            const stopCamera = () => {
                if (frameId) {
                    cancelAnimationFrame(frameId);
                    frameId = null;
                }
                if (stream) {
                    stream.getTracks().forEach((track) => track.stop());
                    stream = null;
                }
                cameraBusy = false;
                video.pause?.();
                video.srcObject = null;
            };

            const detectLoop = async () => {
                if (!detector || !video || cameraBusy) {
                    frameId = requestAnimationFrame(detectLoop);
                    return;
                }

                cameraBusy = true;
                try {
                    const results = await detector.detect(video);
                    const code = results?.find((result) => String(result.rawValue || '').trim())?.rawValue?.trim();
                    if (code) {
                        barcodeInput.value = code;
                        setStatus(`Barcode terdeteksi: <strong>${code}</strong><br>Tekan <strong>Kirim ke Aplikasi</strong> atau scan ulang barcode lain.`, 'ok');
                    }
                } catch (error) {
                } finally {
                    cameraBusy = false;
                }

                frameId = requestAnimationFrame(detectLoop);
            };

            const startCamera = async () => {
                try {
                    await ensureDetector();
                    stream = await navigator.mediaDevices.getUserMedia({
                        video: {
                            facingMode: { ideal: 'environment' },
                        },
                        audio: false,
                    });
                    video.srcObject = stream;
                    await video.play();
                    setStatus('Kamera aktif. Arahkan barcode ke dalam kotak.');
                    frameId = requestAnimationFrame(detectLoop);
                } catch (error) {
                    stopCamera();
                    setStatus(error.message || 'Tidak bisa membuka kamera.', 'err');
                }
            };

            const sendBarcode = async () => {
                const barcode = barcodeInput.value.trim();
                const qty = Math.max(1, parseInt(qtyInput.value || '1', 10) || 1);
                if (!barcode) {
                    setStatus('Barcode belum ada. Scan dulu atau isi manual.', 'err');
                    return;
                }

                sendBtn.disabled = true;
                sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengirim...';
                try {
                    const response = await fetch(postUrl, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': getCsrfToken(),
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                        },
                        credentials: 'same-origin',
                        body: JSON.stringify({ barcode, qty }),
                    });

                    const data = await response.json().catch(() => ({}));
                    if (!response.ok) {
                        throw new Error(data.message || 'Gagal mengirim barcode ke aplikasi.');
                    }

                    setStatus(`Berhasil dikirim: <strong>${data.item?.name || barcode}</strong><br>${data.message || 'Masuk ke aplikasi kasir.'}`, 'ok');
                    barcodeInput.value = '';
                    qtyInput.value = '1';
                } catch (error) {
                    setStatus(error.message || 'Gagal mengirim barcode ke aplikasi.', 'err');
                } finally {
                    sendBtn.disabled = false;
                    sendBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Kirim ke Aplikasi';
                }
            };

            startBtn?.addEventListener('click', startCamera);
            stopBtn?.addEventListener('click', stopCamera);
            sendBtn?.addEventListener('click', sendBarcode);
            barcodeInput?.addEventListener('keydown', (event) => {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    sendBarcode();
                }
            });
            window.addEventListener('beforeunload', stopCamera);
        })();
    </script>
</body>
</html>
