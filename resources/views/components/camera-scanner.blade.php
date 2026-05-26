{{--
    Camera Barcode Scanner Component
    Props:
      - input-id   : ID of the text input to fill (default: barcodeInput)
      - callback   : JS function name to call after scan (default: doBarcodeLookup)
--}}
@props(['inputId' => 'barcodeInput', 'callback' => 'doBarcodeLookup'])

{{-- Camera trigger button --}}
<button type="button" id="openCamera_{{ $inputId }}"
    title="ສະແກນດ້ວຍກ້ອງ"
    class="p-2 text-purple-600 hover:bg-purple-50 rounded-lg transition">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
    </svg>
</button>

{{-- Camera Modal --}}
<div id="cameraModal_{{ $inputId }}" style="display:none"
    class="fixed inset-0 bg-black/80 items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 3.5V16m0 0v.5M20 16h.5M4 6h.01M4 20h.01M20 4h.01M4 4h.01"/>
                </svg>
                ສະແກນ Barcode
            </h3>
            <button type="button" id="closeCamera_{{ $inputId }}"
                class="text-gray-400 hover:text-gray-600 p-1 rounded-lg hover:bg-gray-100 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="p-4">
            {{-- Video feed --}}
            <div class="relative rounded-xl overflow-hidden bg-black aspect-video">
                <video id="camVideo_{{ $inputId }}" class="w-full h-full object-cover" autoplay playsinline muted></video>
                {{-- Scan guide overlay --}}
                <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                    <div class="relative w-48 h-32">
                        <div class="absolute top-0 left-0 w-6 h-6 border-t-2 border-l-2 border-green-400 rounded-tl"></div>
                        <div class="absolute top-0 right-0 w-6 h-6 border-t-2 border-r-2 border-green-400 rounded-tr"></div>
                        <div class="absolute bottom-0 left-0 w-6 h-6 border-b-2 border-l-2 border-green-400 rounded-bl"></div>
                        <div class="absolute bottom-0 right-0 w-6 h-6 border-b-2 border-r-2 border-green-400 rounded-br"></div>
                        <div id="scanLine_{{ $inputId }}" class="absolute left-0 right-0 h-0.5 bg-green-400/70 animate-bounce top-1/2"></div>
                    </div>
                </div>
            </div>

            {{-- Status --}}
            <div id="camStatus_{{ $inputId }}" class="mt-3 text-center text-sm text-gray-500">
                ກຳລັງເລີ່ມກ້ອງ...
            </div>

            {{-- Camera select (for multi-camera devices) --}}
            <select id="camSelect_{{ $inputId }}" style="display:none"
                class="mt-2 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-400">
            </select>

            {{-- No camera fallback --}}
            <div id="camError_{{ $inputId }}" style="display:none"
                class="mt-3 p-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700 text-center">
                ບໍ່ສາມາດເຂົ້າຫາກ້ອງໄດ້ — ກວດສອບການອະນຸຍາດ
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function() {
    const INPUT_ID   = '{{ $inputId }}';
    const CALLBACK   = '{{ $callback }}';
    const modalEl    = document.getElementById('cameraModal_' + INPUT_ID);
    const videoEl    = document.getElementById('camVideo_' + INPUT_ID);
    const statusEl   = document.getElementById('camStatus_' + INPUT_ID);
    const errorEl    = document.getElementById('camError_' + INPUT_ID);
    const selectEl   = document.getElementById('camSelect_' + INPUT_ID);

    let stream       = null;
    let codeReader   = null;
    let zxingLoaded  = false;

    // Move modal to body so position:fixed works correctly regardless of parent stacking context
    document.body.appendChild(modalEl);

    function openModal()  { modalEl.style.display = 'flex'; startCamera(); }
    function closeModal() { modalEl.style.display = 'none';  stopCamera();  }

    document.getElementById('openCamera_'  + INPUT_ID).addEventListener('click', openModal);
    document.getElementById('closeCamera_' + INPUT_ID).addEventListener('click', closeModal);
    modalEl.addEventListener('click', function(e) { if (e.target === modalEl) closeModal(); });

    function loadZXing(callback) {
        if (window.ZXing) { callback(); return; }
        if (zxingLoaded)  { setTimeout(function() { loadZXing(callback); }, 100); return; }
        zxingLoaded = true;
        var s = document.createElement('script');
        s.src = 'https://cdn.jsdelivr.net/npm/@zxing/library@0.20.0/umd/index.min.js';
        s.onload = callback;
        s.onerror = function() {
            errorEl.style.display = 'block';
            statusEl.textContent = 'ໂຫລດ library ບໍ່ໄດ້ — ກວດສອບ internet';
        };
        document.head.appendChild(s);
    }

    function startCamera() {
        statusEl.textContent = 'ກຳລັງໂຫລດ...';
        errorEl.style.display = 'none';

        loadZXing(function() {
            codeReader = new ZXing.BrowserMultiFormatReader();

            // Get available cameras
            codeReader.listVideoInputDevices().then(function(devices) {
                if (!devices || devices.length === 0) {
                    errorEl.style.display = 'block';
                    statusEl.textContent  = '';
                    return;
                }

                // Populate camera selector for multi-camera devices
                if (devices.length > 1) {
                    selectEl.innerHTML = '';
                    devices.forEach(function(d, i) {
                        var opt = document.createElement('option');
                        opt.value = d.deviceId;
                        opt.text  = d.label || ('ກ້ອງ ' + (i + 1));
                        // Prefer back camera
                        if (d.label && d.label.toLowerCase().includes('back')) opt.selected = true;
                        selectEl.appendChild(opt);
                    });
                    selectEl.style.display = 'block';
                    selectEl.addEventListener('change', function() {
                        stopCamera();
                        startDecoding(this.value);
                    });
                }

                // Use back camera if available, otherwise first
                var preferred = devices.find(function(d) {
                    return d.label && d.label.toLowerCase().includes('back');
                }) || devices[0];

                startDecoding(preferred.deviceId);

            }).catch(function(err) {
                errorEl.style.display = 'block';
                statusEl.textContent  = '';
            });
        });
    }

    function startDecoding(deviceId) {
        statusEl.textContent = 'ວາງ barcode ໃນກ່ອງ...';

        codeReader.decodeFromVideoDevice(deviceId, videoEl, function(result, err) {
            if (result) {
                var text = result.getText();
                var input = document.getElementById(INPUT_ID);
                if (input) {
                    input.value = text;
                    closeModal();
                    // Small delay so modal closes before lookup
                    setTimeout(function() {
                        if (typeof window[CALLBACK] === 'function') {
                            window[CALLBACK]();
                        }
                    }, 200);
                }
            }
            // Silently ignore ZXing NotFoundException (no barcode in frame)
        });
    }

    function stopCamera() {
        if (codeReader) {
            try { codeReader.reset(); } catch(e) {}
            codeReader = null;
        }
        if (stream) {
            stream.getTracks().forEach(function(t) { t.stop(); });
            stream = null;
        }
        statusEl.textContent = 'ກຳລັງເລີ່ມກ້ອງ...';
        selectEl.style.display = 'none';
        selectEl.innerHTML = '';
    }
})();
</script>
@endpush
