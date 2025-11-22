@extends('layouts.app')

@section('title', 'Scanner de Boletos')

@section('content')
<div class="max-w-3xl mx-auto mt-10 p-8 bg-white shadow-lg rounded-2xl text-center border border-gray-200">
    <div class="flex items-center justify-between mb-6">
        <div class="flex-1 text-left">
            <h1 class="text-3xl font-extrabold text-gray-800 tracking-tight">Escáner de Boletos</h1>
            <p class="mt-2 text-gray-600">Apunta la cámara al código QR del boleto para validar acceso.</p>
        </div>
        <div class="flex items-center space-x-3">
            <!-- Toggle Button -->
            <button id="toggle-scanner" 
                    class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg font-semibold shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-200 flex items-center space-x-2">
                <svg id="toggle-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                <span id="toggle-text">Detener Escáner</span>
            </button>
            
            <!-- Switch Camera Button -->
            <button id="switch-camera" 
                    class="px-4 py-3 bg-gray-600 text-white rounded-lg font-semibold shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-200 flex items-center space-x-2"
                    title="Cambiar cámara">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                <span class="hidden md:inline">Cambiar</span>
            </button>
        </div>
    </div>

    <div id="reader" class="mx-auto w-full max-w-md rounded-lg overflow-hidden shadow-md border border-gray-300"></div>
    
    <div id="scanner-status" class="mt-4 text-sm font-medium text-green-600 flex items-center justify-center space-x-2">
        <svg class="w-5 h-5 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
        </svg>
        <span>Escáner activo</span>
    </div>

    <div id="result" class="mt-6 text-lg font-medium"></div>
</div>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const resultContainer = document.getElementById('result');
    const toggleButton = document.getElementById('toggle-scanner');
    const toggleIcon = document.getElementById('toggle-icon');
    const toggleText = document.getElementById('toggle-text');
    const switchCameraButton = document.getElementById('switch-camera');
    const scannerStatus = document.getElementById('scanner-status');
    const readerElement = document.getElementById('reader');
    
    let lock = false;
    let html5QrCode = null;
    let isScanning = false;
    let currentCameraId = null;
    let cameras = [];
    let currentCameraIndex = 0;
    const config = { fps: 10, qrbox: 250 };

    function renderMessage(html) {
        resultContainer.innerHTML = `
            <div class="p-5 rounded-xl border shadow-sm animate-fade">
                ${html}
            </div>
        `;
    }

    function updateToggleButton(isActive) {
        if (isActive) {
            toggleButton.classList.remove('from-red-600', 'to-red-700');
            toggleButton.classList.add('from-blue-600', 'to-indigo-600');
            toggleIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>';
            toggleText.textContent = 'Detener Escáner';
            scannerStatus.classList.remove('hidden');
            scannerStatus.classList.add('flex');
            scannerStatus.querySelector('span').textContent = 'Escáner activo';
            switchCameraButton.disabled = false;
            switchCameraButton.classList.remove('opacity-50', 'cursor-not-allowed');
        } else {
            toggleButton.classList.remove('from-blue-600', 'to-indigo-600');
            toggleButton.classList.add('from-red-600', 'to-red-700');
            toggleIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>';
            toggleText.textContent = 'Iniciar Escáner';
            scannerStatus.classList.add('hidden');
            scannerStatus.classList.remove('flex');
            switchCameraButton.disabled = true;
            switchCameraButton.classList.add('opacity-50', 'cursor-not-allowed');
        }
    }

    function onScanSuccess(decodedText, decodedResult) {
        if (lock) return;
        lock = true;

        let parts = decodedText.split('/');
        let ticketId = parts[parts.length - 1];

        renderMessage(`<p class="text-gray-500">Validando boleto <strong>${ticketId}</strong>...</p>`);

        fetch(`/api/v1/validate-ticket/${ticketId}`)
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    renderMessage(`
                        <div class="text-green-700">
                            <div class="text-2xl font-bold mb-2">✅ Acceso Concedido</div>
                            <div class="text-lg">Boleto: <strong>${data.data.ticket.id}</strong></div>
                            <div class="text-lg">Evento: <strong>${data.data.event.name}</strong></div>
                        </div>
                    `);
                } else {
                    renderMessage(`
                        <div class="text-red-700">
                            <div class="text-2xl font-bold mb-2">❌ Acceso Denegado</div>
                            <div class="text-lg">Este boleto ya ha sido escaneado</div>
                            <div class="mt-2 text-lg">
                                Evento: <strong>${data.data?.event?.name ?? 'No disponible'}</strong>
                            </div>
                        </div>
                    `);
                }
            })
            .catch(() => {
                renderMessage(`
                    <div class="text-red-700">
                        <div class="text-xl font-bold">⚠ Error</div>
                        <p>No se pudo validar el boleto. Intente de nuevo.</p>
                    </div>
                `);
            })
            .finally(() => {
                setTimeout(() => { lock = false; }, 2000);
            });
    }

    async function startScanner(cameraId = null) {
        if (!html5QrCode) {
            html5QrCode = new Html5Qrcode("reader");
        }

        try {
            if (cameraId) {
                await html5QrCode.start(cameraId, config, onScanSuccess);
            } else if (cameras.length > 0) {
                await html5QrCode.start(cameras[currentCameraIndex].id, config, onScanSuccess);
                currentCameraId = cameras[currentCameraIndex].id;
            } else {
                throw new Error('No hay cámaras disponibles');
            }
            isScanning = true;
            updateToggleButton(true);
        } catch (err) {
            console.error('Error iniciando escáner:', err);
            renderMessage(`<p class="text-red-500">No se pudo iniciar el escáner: ${err.message}</p>`);
            isScanning = false;
            updateToggleButton(false);
        }
    }

    async function stopScanner() {
        if (html5QrCode && isScanning) {
            try {
                await html5QrCode.stop();
                await html5QrCode.clear();
                isScanning = false;
                updateToggleButton(false);
            } catch (err) {
                console.error('Error deteniendo escáner:', err);
            }
        }
    }

    async function switchCamera() {
        if (!isScanning || cameras.length < 2) return;
        
        await stopScanner();
        currentCameraIndex = (currentCameraIndex + 1) % cameras.length;
        await startScanner(cameras[currentCameraIndex].id);
    }

    // Toggle button click handler
    toggleButton.addEventListener('click', async function() {
        if (isScanning) {
            await stopScanner();
        } else {
            await startScanner();
        }
    });

    // Switch camera button click handler
    switchCameraButton.addEventListener('click', async function() {
        await switchCamera();
    });

    // Initialize cameras and start scanner
    Html5Qrcode.getCameras().then(devices => {
        if (devices && devices.length) {
            cameras = devices;
            // Start with back camera if available, otherwise use first camera
            const backCamera = devices.find(d => d.label.toLowerCase().includes('back') || d.label.toLowerCase().includes('rear'));
            if (backCamera) {
                currentCameraIndex = devices.indexOf(backCamera);
            }
            startScanner();
        } else {
            renderMessage('<p class="text-red-500">No se encontraron cámaras disponibles.</p>');
            updateToggleButton(false);
        }
    }).catch(err => {
        console.error('Error obteniendo cámaras:', err);
        renderMessage(`<p class="text-red-500">No se pudo acceder a la cámara: ${err.message}</p>`);
        updateToggleButton(false);
    });
});
</script>

<style>
@keyframes fade {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade {
    animation: fade 0.3s ease-out;
}
</style>
@endsection
