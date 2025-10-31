@extends('layouts.app')

@section('title', 'Scanner de Boletos')

@section('content')
<div class="max-w-3xl mx-auto mt-10 p-8 bg-white shadow rounded-xl text-center">
    <h1 class="text-2xl font-bold mb-4">Escáner de Boletos</h1>
    <p class="mb-6 text-gray-600">Apunta la cámara al código QR del boleto para validar.</p>

    <div id="reader" class="mx-auto w-full max-w-md"></div>

    <div id="result" class="mt-6 text-lg font-semibold text-gray-700"></div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const resultContainer = document.getElementById('result');
    const qrRegion = document.getElementById('reader');

    function onScanSuccess(decodedText, decodedResult) {
        // Mostrar temporalmente lo leído
        resultContainer.innerHTML = `<p>Escaneando: <strong>${decodedText}</strong></p>`;

        // Enviar a backend para validar
        fetch(`/api/validate-ticket/${decodedText}`)
            .then(response => response.json())
            .then(data => {
                if (data.valid) {
                    resultContainer.innerHTML = `
                        <div class="text-green-600 font-bold text-xl">
                            ✅ Boleto válido: ${data.ticket_id}
                        </div>
                    `;
                } else {
                    resultContainer.innerHTML = `
                        <div class="text-red-600 font-bold text-xl">
                            ❌ ${data.message}
                        </div>
                    `;
                }
            })
            .catch(() => {
                resultContainer.innerHTML = `
                    <div class="text-red-500 font-bold">
                        Error al validar el boleto.
                    </div>
                `;
            });
    }

    const html5QrCode = new Html5Qrcode("reader");
    const config = { fps: 10, qrbox: 250 };

    Html5Qrcode.getCameras().then(devices => {
        if (devices && devices.length) {
            const cameraId = devices[0].id;
            html5QrCode.start(
                cameraId,
                config,
                onScanSuccess
            );
        }
    }).catch(err => {
        resultContainer.innerHTML = `<p class="text-red-500">No se pudo acceder a la cámara: ${err}</p>`;
    });
});
</script>
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
@endsection
