@extends('layouts.space-dashboard')

@section('title', 'Scanner de Boletos')

@section('page_title')
    Scanner
@endsection

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="bg-white shadow-xl rounded-2xl overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold">Escáner de Boletos</h1>
                        <p class="text-blue-100 mt-1">Apunta la cámara al código QR del boleto</p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <button id="toggle-scanner"
                            class="px-5 py-2.5 bg-white text-blue-600 rounded-xl font-semibold shadow-md hover:bg-blue-50 transition-all flex items-center space-x-2">
                            <svg id="toggle-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"></path>
                            </svg>
                            <span id="toggle-text">Detener</span>
                        </button>
                        <button id="switch-camera"
                            class="p-2.5 bg-white/20 text-white rounded-xl hover:bg-white/30 transition-all"
                            title="Cambiar cámara">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                </path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Scanner Area -->
            <div class="p-6">
                <div id="reader"
                    class="mx-auto w-full max-w-md rounded-xl overflow-hidden shadow-inner bg-gray-100 border-2 border-dashed border-gray-300 min-h-[300px] flex items-center justify-center">
                    <div id="camera-placeholder" class="text-center text-gray-400 p-6">
                        <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z">
                            </path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <p class="font-medium">Iniciando cámara...</p>
                        <p class="text-sm mt-2">Por favor permite el acceso a la cámara</p>
                    </div>
                </div>

                <!-- Status Indicator -->
                <div id="scanner-status" class="mt-4 flex items-center justify-center space-x-2">
                    <div class="w-3 h-3 rounded-full bg-green-500 animate-pulse"></div>
                    <span class="text-sm font-medium text-green-600">Escáner activo</span>
                </div>

                <!-- Result Area -->
                <div id="result" class="mt-6"></div>
            </div>
        </div>

        <!-- Instructions Card -->
        <div class="mt-6 bg-white rounded-2xl shadow-lg p-6">
            <h3 class="font-semibold text-gray-900 mb-3">Instrucciones</h3>
            <ul class="space-y-2 text-sm text-gray-600">
                <li class="flex items-start">
                    <svg class="w-5 h-5 text-blue-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Asegúrate de tener buena iluminación
                </li>
                <li class="flex items-start">
                    <svg class="w-5 h-5 text-blue-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Centra el código QR en el recuadro
                </li>
                <li class="flex items-start">
                    <svg class="w-5 h-5 text-blue-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Mantén el dispositivo estable mientras escaneas
                </li>
            </ul>
        </div>
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
            const placeholder = document.getElementById('camera-placeholder');

            let lock = false;
            let html5QrCode = null;
            let isScanning = false;
            let cameras = [];
            let currentCameraIndex = 0;
            const config = {
                fps: 10,
                qrbox: { width: 250, height: 250 },
                aspectRatio: 1.0
            };

            function renderMessage(type, title, message, details = '') {
                const colors = {
                    success: 'bg-green-50 border-green-200 text-green-800',
                    error: 'bg-red-50 border-red-200 text-red-800',
                    warning: 'bg-yellow-50 border-yellow-200 text-yellow-800',
                    info: 'bg-blue-50 border-blue-200 text-blue-800'
                };
                const icons = {
                    success: '✅',
                    error: '❌',
                    warning: '⚠️',
                    info: 'ℹ️'
                };

                resultContainer.innerHTML = `
                <div class="p-5 rounded-xl border ${colors[type]} animate-fade">
                    <div class="flex items-start">
                        <span class="text-2xl mr-3">${icons[type]}</span>
                        <div class="flex-1">
                            <div class="text-lg font-bold mb-1">${title}</div>
                            <div>${message}</div>
                            ${details ? `<div class="mt-2 text-sm opacity-75">${details}</div>` : ''}
                        </div>
                    </div>
                </div>
            `;
            }

            function updateToggleButton(isActive) {
                if (isActive) {
                    toggleIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"></path>';
                    toggleText.textContent = 'Detener';
                    scannerStatus.classList.remove('hidden');
                    switchCameraButton.disabled = false;
                    switchCameraButton.classList.remove('opacity-50');
                } else {
                    toggleIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>';
                    toggleText.textContent = 'Iniciar';
                    scannerStatus.classList.add('hidden');
                    switchCameraButton.disabled = true;
                    switchCameraButton.classList.add('opacity-50');
                }
            }

            function onScanSuccess(decodedText) {
                if (lock) return;
                lock = true;

                let parts = decodedText.split('/');
                let ticketId = parts[parts.length - 1];

                renderMessage('info', 'Validando...', `Verificando boleto <strong>${ticketId}</strong>`);

                fetch(`/api/v1/validate-ticket/${ticketId}`, {
                    credentials: 'include',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            const eventName = data.data?.event?.name || 'Evento';
                            const ticketNum = data.data?.ticket?.id || ticketId;
                            renderMessage('success', 'Acceso Concedido',
                                `Boleto <strong>#${ticketNum}</strong> validado correctamente`,
                                `Evento: <strong>${eventName}</strong>`
                            );
                            stopScanner();
                            setTimeout(() => startScanner(), 3000);
                        } else {
                            if (data.message && data.message.includes("NO pertenece a tu espacio")) {
                                const spaceOfTicket = data.data?.space_of_ticket || 'Desconocido';
                                const yourSpace = data.data?.your_space || 'Tu espacio';
                                renderMessage('error', 'Espacio Incorrecto',
                                    'Este boleto pertenece a otro espacio',
                                    `Espacio del boleto: <strong>${spaceOfTicket}</strong><br>Tu espacio: <strong>${yourSpace}</strong>`
                                );
                            } else {
                                const eventName = data.data?.event?.name || '';
                                renderMessage('error', 'Acceso Denegado',
                                    data.message || 'No se pudo validar el boleto',
                                    eventName ? `Evento: <strong>${eventName}</strong>` : ''
                                );
                            }
                        }
                    })
                    .catch(err => {
                        console.error('Error:', err);
                        renderMessage('error', 'Error de Conexión', 'No se pudo validar el boleto. Intente de nuevo.');
                    })
                    .finally(() => {
                        setTimeout(() => { lock = false; }, 2000);
                    });
            }

            async function startScanner(cameraId = null) {
                if (placeholder) placeholder.style.display = 'none';

                if (!html5QrCode) {
                    html5QrCode = new Html5Qrcode("reader");
                }

                try {
                    const cameraToUse = cameraId || (cameras.length > 0 ? cameras[currentCameraIndex].id : null);

                    if (!cameraToUse) {
                        throw new Error('No hay cámaras disponibles');
                    }

                    await html5QrCode.start(cameraToUse, config, onScanSuccess);
                    isScanning = true;
                    updateToggleButton(true);
                } catch (err) {
                    console.error('Error iniciando escáner:', err);
                    if (placeholder) {
                        placeholder.style.display = 'flex';
                        placeholder.innerHTML = `
                        <div class="text-center text-red-500 p-6">
                            <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            <p class="font-medium">No se pudo acceder a la cámara</p>
                            <p class="text-sm mt-2">${err.message || 'Verifica los permisos de cámara'}</p>
                        </div>
                    `;
                    }
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

            toggleButton.addEventListener('click', async function () {
                if (isScanning) {
                    await stopScanner();
                } else {
                    await startScanner();
                }
            });

            switchCameraButton.addEventListener('click', switchCamera);

            // Initialize
            Html5Qrcode.getCameras().then(devices => {
                if (devices && devices.length) {
                    cameras = devices;
                    // Prefer back camera
                    const backCamera = devices.find(d =>
                        /back|rear|environment|trás|externa/i.test(d.label)
                    );
                    if (backCamera) {
                        currentCameraIndex = devices.indexOf(backCamera);
                    }
                    startScanner();
                } else {
                    if (placeholder) {
                        placeholder.innerHTML = `
                        <div class="text-center text-yellow-600 p-6">
                            <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            <p class="font-medium">No se encontraron cámaras</p>
                            <p class="text-sm mt-2">Conecta una cámara e intenta de nuevo</p>
                        </div>
                    `;
                    }
                    updateToggleButton(false);
                }
            }).catch(err => {
                console.error('Error obteniendo cámaras:', err);
                if (placeholder) {
                    placeholder.innerHTML = `
                    <div class="text-center text-red-500 p-6">
                        <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                        </svg>
                        <p class="font-medium">Error de permisos</p>
                        <p class="text-sm mt-2">${err.message || 'Permite el acceso a la cámara en tu navegador'}</p>
                    </div>
                `;
                }
                updateToggleButton(false);
            });
        });
    </script>

    <style>
        @keyframes fade {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade {
            animation: fade 0.3s ease-out;
        }

        #reader video {
            border-radius: 0.75rem;
        }
    </style>
@endsection