@extends('layouts.app')

@section('title', 'Scanner de Boletos')

@section('content')
<div class="max-w-3xl mx-auto mt-10 p-8 bg-white shadow-lg rounded-2xl text-center border border-gray-200">
    <h1 class="text-3xl font-extrabold mb-4 text-gray-800 tracking-tight">Escáner de Boletos</h1>
    <p class="mb-6 text-gray-600">Apunta la cámara al código QR del boleto para validar acceso.</p>

    <div id="reader" class="mx-auto w-full max-w-md rounded-lg overflow-hidden shadow-md border border-gray-300"></div>

    <div id="result" class="mt-6 text-lg font-medium"></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const resultContainer = document.getElementById('result');
    let lock = false;

    function renderMessage(html) {
        resultContainer.innerHTML = `
            <div class="p-5 rounded-xl border shadow-sm animate-fade">
                ${html}
            </div>
        `;
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

    const html5QrCode = new Html5Qrcode("reader");
    const config = { fps: 10, qrbox: 250 };

    Html5Qrcode.getCameras().then(devices => {
        if (devices && devices.length) {
            html5QrCode.start(devices[0].id, config, onScanSuccess);
        }
    }).catch(err => {
        resultContainer.innerHTML = `<p class="text-red-500">No se pudo acceder a la cámara: ${err}</p>`;
    });
});
</script>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
@endsection
