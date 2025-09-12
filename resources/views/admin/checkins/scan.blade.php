@extends('layouts.app')

@section('title', 'Escanear QR - Check-in')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-900">Escanear QR para Check-in</h1>
                    <a href="{{ route('admin.checkins.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 transition-colors">
                        Ver Check-ins
                    </a>
                </div>

                <!-- Scanner QR -->
                <div class="bg-gray-50 rounded-lg p-6 mb-6">
                    <div class="text-center">
                        <h3 class="text-lg font-semibold mb-4">Escanea el código QR del boleto</h3>
                        
                        <!-- Camera Scanner -->
                        <div id="scanner-container" class="relative">
                            <video id="video" width="400" height="300" class="mx-auto border rounded-lg bg-black"></video>
                            <canvas id="canvas" width="400" height="300" class="hidden"></canvas>
                        </div>
                        
                        <!-- Manual Input -->
                        <div class="mt-6">
                            <label for="manual-code" class="block text-sm font-medium text-gray-700 mb-2">
                                O ingresa el código manualmente:
                            </label>
                            <div class="flex gap-2">
                                <input type="text" id="manual-code" placeholder="Código del boleto" 
                                       class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <button onclick="processManualCode()" 
                                        class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                                    Verificar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Results -->
                <div id="result-container" class="hidden">
                    <div class="bg-white border rounded-lg p-4">
                        <h4 class="font-semibold mb-2">Resultado del escaneo:</h4>
                        <div id="result-content"></div>
                    </div>
                </div>

                <!-- Instructions -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h4 class="font-semibold text-blue-900 mb-2">Instrucciones:</h4>
                    <ul class="text-sm text-blue-800 space-y-1">
                        <li>• Permite el acceso a la cámara cuando se solicite</li>
                        <li>• Apunta la cámara al código QR del boleto</li>
                        <li>• El sistema procesará automáticamente el código</li>
                        <li>• También puedes ingresar el código manualmente</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let stream = null;
let scanning = false;

// Initialize camera
async function initCamera() {
    try {
        stream = await navigator.mediaDevices.getUserMedia({ 
            video: { 
                facingMode: 'environment' // Use back camera if available
            } 
        });
        const video = document.getElementById('video');
        video.srcObject = stream;
        video.play();
        scanning = true;
        scanQR();
    } catch (err) {
        console.error('Error accessing camera:', err);
        document.getElementById('scanner-container').innerHTML = 
            '<p class="text-red-600">No se pudo acceder a la cámara. Usa el campo de entrada manual.</p>';
    }
}

// Simple QR code scanning (you might want to use a library like jsQR)
function scanQR() {
    if (!scanning) return;
    
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const context = canvas.getContext('2d');
    
    if (video.readyState === video.HAVE_ENOUGH_DATA) {
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
        
        // Here you would use a QR code library to decode
        // For now, we'll just continue scanning
        setTimeout(scanQR, 100);
    } else {
        setTimeout(scanQR, 100);
    }
}

// Process manual code input
async function processManualCode() {
    const code = document.getElementById('manual-code').value.trim();
    if (!code) {
        alert('Por favor ingresa un código');
        return;
    }
    
    await processTicketCode(code);
}

// Process ticket code (QR or manual)
async function processTicketCode(code) {
    try {
        const response = await fetch(`/checkin/${code}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        const result = await response.json();
        
        if (response.ok) {
            showResult('success', result.message);
            document.getElementById('manual-code').value = '';
        } else {
            showResult('error', result.message || 'Error al procesar el boleto');
        }
    } catch (error) {
        console.error('Error:', error);
        showResult('error', 'Error de conexión');
    }
}

// Show result
function showResult(type, message) {
    const container = document.getElementById('result-container');
    const content = document.getElementById('result-content');
    
    const bgColor = type === 'success' ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200';
    const textColor = type === 'success' ? 'text-green-800' : 'text-red-800';
    
    content.innerHTML = `
        <div class="${bgColor} border rounded p-3">
            <p class="${textColor}">${message}</p>
        </div>
    `;
    
    container.classList.remove('hidden');
    
    // Hide after 5 seconds
    setTimeout(() => {
        container.classList.add('hidden');
    }, 5000);
}

// Stop camera when page unloads
window.addEventListener('beforeunload', () => {
    if (stream) {
        stream.getTracks().forEach(track => track.stop());
    }
});

// Initialize when page loads
document.addEventListener('DOMContentLoaded', () => {
    initCamera();
});
</script>
@endsection
