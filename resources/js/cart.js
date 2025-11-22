/**
 * Sistema Global de Carrito con localStorage
 * Funciona en dominio base y subdominios
 */

// Configuración global del carrito
window.CartConfig = {
    baseUrl: null,
    cartAddUrl: null,
    cartSyncUrl: null,
    csrfToken: null,
    storageKey: 'cart_items'
};

// Inicializar configuración
(function() {
    // Obtener dominio base desde meta tag
    const baseUrlMeta = document.querySelector('meta[name="base-url"]');
    if (baseUrlMeta) {
        CartConfig.baseUrl = baseUrlMeta.getAttribute('content');
    } else {
        // Fallback: extraer dominio base del host actual
        const host = window.location.host;
        const parts = host.split('.');
        if (parts.length > 2) {
            // Es un subdominio, obtener dominio base
            CartConfig.baseUrl = window.location.protocol + '//' + parts.slice(-2).join('.');
        } else {
            // Es el dominio base
            CartConfig.baseUrl = window.location.origin;
        }
    }
    
    // Obtener CSRF token
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    if (csrfMeta) {
        CartConfig.csrfToken = csrfMeta.getAttribute('content');
    }
    
    // Construir URLs del carrito (siempre dominio base)
    CartConfig.cartAddUrl = CartConfig.baseUrl + '/cart/add';
    CartConfig.cartSyncUrl = CartConfig.baseUrl + '/cart/sync';
    
    // Debug: verificar configuración
    if (typeof console !== 'undefined') {
        console.log('✅ CartConfig initialized:', CartConfig);
    }
})();

/**
 * Obtener carrito desde localStorage
 */
window.getCart = function() {
    try {
        const storageKey = (window.CartConfig && window.CartConfig.storageKey) ? window.CartConfig.storageKey : 'cart_items';
        const cartJson = localStorage.getItem(storageKey);
        const cart = cartJson ? JSON.parse(cartJson) : {};
        return cart;
    } catch (e) {
        console.error('Error reading cart from localStorage:', e);
        return {};
    }
};

/**
 * Guardar carrito en localStorage
 */
window.saveCart = function(cart) {
    try {
        localStorage.setItem(CartConfig.storageKey, JSON.stringify(cart));
        return true;
    } catch (e) {
        console.error('Error saving cart to localStorage:', e);
        return false;
    }
};

/**
 * Obtener conteo del carrito
 */
window.getCartCount = function() {
    const cart = getCart();
    let count = 0;
    for (const key in cart) {
        if (cart.hasOwnProperty(key)) {
            count += cart[key].quantity || 0;
        }
    }
    return count;
};

/**
 * Obtener total del carrito
 */
window.getCartTotal = function() {
    const cart = getCart();
    let total = 0;
    for (const key in cart) {
        if (cart.hasOwnProperty(key)) {
            const item = cart[key];
            total += (item.price || 0) * (item.quantity || 0);
        }
    }
    return total;
};

/**
 * Agregar item al carrito (localStorage)
 */
window.addToCartLocal = function(ticketTypeId, eventId, quantity, itemData) {
    const cart = getCart();
    const cartKey = `${ticketTypeId}_${eventId}`;
    
    if (cart[cartKey]) {
        cart[cartKey].quantity += quantity;
    } else {
        cart[cartKey] = {
            ticket_type_id: ticketTypeId,
            event_id: eventId,
            quantity: quantity,
            price: itemData.price,
            ticket_type_name: itemData.ticket_type_name,
            event_name: itemData.event_name,
            event_date: itemData.event_date,
            event_image: itemData.event_image
        };
    }
    
    saveCart(cart);
    return cart;
};

/**
 * Actualizar cantidad en el carrito
 */
window.updateCartItem = function(ticketTypeId, eventId, quantity) {
    const cart = getCart();
    const cartKey = `${ticketTypeId}_${eventId}`;
    
    if (cart[cartKey]) {
        if (quantity <= 0) {
            delete cart[cartKey];
        } else {
            cart[cartKey].quantity = quantity;
        }
        saveCart(cart);
    }
    
    return cart;
};

/**
 * Eliminar item del carrito
 */
window.removeFromCart = function(ticketTypeId, eventId) {
    const cart = getCart();
    const cartKey = `${ticketTypeId}_${eventId}`;
    
    if (cart[cartKey]) {
        delete cart[cartKey];
        saveCart(cart);
    }
    
    return cart;
};

/**
 * Limpiar carrito
 */
window.clearCart = function() {
    localStorage.removeItem(CartConfig.storageKey);
};

/**
 * Obtener token CSRF del dominio base
 */
window.getCsrfToken = async function() {
    try {
        const baseUrl = CartConfig.baseUrl;
        if (!baseUrl) {
            return CartConfig.csrfToken || null;
        }
        
        const tokenResponse = await fetch(baseUrl + '/cart/csrf-token', {
            method: 'GET',
            credentials: 'include'
        });
        
        if (tokenResponse.ok) {
            const tokenData = await tokenResponse.json();
            CartConfig.csrfToken = tokenData.token;
            return tokenData.token;
        }
    } catch (error) {
        console.warn('Could not fetch CSRF token from base domain:', error);
    }
    
    // Fallback al token local
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    if (csrfMeta) {
        CartConfig.csrfToken = csrfMeta.getAttribute('content');
        return CartConfig.csrfToken;
    }
    
    return null;
};

/**
 * Sincronizar carrito con el servidor
 */
window.syncCartWithServer = async function() {
    const cart = getCart();
    
    if (!CartConfig.cartSyncUrl) {
        console.warn('Cart config not initialized');
        return;
    }
    
    // Obtener token CSRF si no lo tenemos
    let csrfToken = CartConfig.csrfToken;
    if (!csrfToken) {
        csrfToken = await getCsrfToken();
    }
    
    if (!csrfToken) {
        console.warn('CSRF token not available');
        return;
    }
    
    try {
        const response = await fetch(CartConfig.cartSyncUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ cart: cart }),
            credentials: 'include'
        });
        
        if (!response.ok) {
            console.error('Error syncing cart with server');
        }
    } catch (error) {
        console.error('Error syncing cart:', error);
    }
};

/**
 * Actualizar contador del carrito en la UI
 */
window.updateCartCount = function() {
    const count = getCartCount();
    
    // Buscar badge por ID primero (más confiable)
    let cartCount = document.getElementById('cart-count-badge');
    
    // Si no existe, buscar por clase
    if (!cartCount) {
        const cartButton = document.querySelector('#cart-dropdown button');
        cartCount = cartButton ? cartButton.querySelector('.bg-red-500') : null;
    }
    
    // Si aún no existe, crearlo
    if (!cartCount) {
        const cartButton = document.querySelector('#cart-dropdown button');
        if (cartButton) {
            cartCount = document.createElement('span');
            cartCount.id = 'cart-count-badge';
            cartCount.className = 'absolute -top-0.5 -right-0.5 inline-flex items-center justify-center bg-red-500 text-white text-xs font-bold min-w-[18px] h-[18px] px-1 rounded-full border-2 border-white shadow-lg';
            cartButton.appendChild(cartCount);
        }
    }
    
    if (cartCount) {
        if (count > 0) {
            cartCount.textContent = count;
            cartCount.style.display = 'inline-flex';
            cartCount.classList.add('animate-pulse');
            setTimeout(() => {
                cartCount.classList.remove('animate-pulse');
            }, 500);
        } else {
            cartCount.style.display = 'none';
        }
    }
    
    return count;
};

/**
 * Renderizar dropdown del carrito desde localStorage
 */
window.renderCartDropdown = function() {
    const cart = getCart();
    const cartCount = getCartCount();
    const cartTotal = getCartTotal();
    const cartMenu = document.getElementById('cart-menu');
    
    if (!cartMenu) return;
    
    // Prevenir ciclos infinitos - usar flag
    if (cartMenu.dataset.rendering === 'true') {
        return;
    }
    cartMenu.dataset.rendering = 'true';
    
    let html = `
        <div class="px-4 py-3 border-b border-gray-200 bg-gray-50 rounded-t-lg">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Carrito de Compras</h3>
                    <p class="text-sm text-gray-500">${cartCount} item(s)</p>
                </div>
                <button onclick="closeCartDropdown()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    `;
    
    if (cartCount === 0) {
        html += `
            <div class="px-4 py-8 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l-1 7a2 2 0 01-2 2H8a2 2 0 01-2-2L5 9z"></path>
                    </svg>
                </div>
                <p class="text-gray-500 text-sm">Tu carrito está vacío</p>
            </div>
        `;
    } else {
        html += '<div class="max-h-80 overflow-y-auto">';
        
        for (const key in cart) {
            if (cart.hasOwnProperty(key)) {
                const item = cart[key];
                const itemTotal = (item.price || 0) * (item.quantity || 0);
                let eventDate = '';
                if (item.event_date) {
                    try {
                        const date = new Date(item.event_date);
                        eventDate = date.toLocaleDateString('es-ES', { day: 'numeric', month: 'short', year: 'numeric' });
                    } catch (e) {
                        eventDate = item.event_date;
                    }
                }
                
                html += `
                    <div class="px-4 py-3 border-b border-gray-100 hover:bg-gray-50">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900">${item.ticket_type_name || 'Boleto'}</p>
                                <p class="text-xs text-gray-500">${item.event_name || 'Evento'}</p>
                                ${eventDate ? `<p class="text-xs text-gray-400">${eventDate}</p>` : ''}
                            </div>
                            <div class="text-right flex-shrink-0">
                                <p class="text-sm font-medium text-gray-900">${item.quantity}x</p>
                                <p class="text-sm text-gray-500">$${itemTotal.toFixed(2)}</p>
                            </div>
                        </div>
                    </div>
                `;
            }
        }
        
        html += '</div>';
        
        html += `
            <div class="px-4 py-3 border-t border-gray-200 bg-gray-50 rounded-b-lg">
                <div class="flex justify-between items-center mb-3">
                    <span class="text-sm font-medium text-gray-900">Total:</span>
                    <span class="text-lg font-bold text-gray-900">$${cartTotal.toFixed(2)}</span>
                </div>
                <a href="${CartConfig.baseUrl}/cart" class="block w-full text-center bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors">
                    Ver Carrito
                </a>
            </div>
        `;
    }
    
    cartMenu.innerHTML = html;
    cartMenu.dataset.rendering = 'false';
    
    // Actualizar contador sin disparar eventos
    const count = getCartCount();
    const cartButton = document.querySelector('#cart-dropdown button');
    let cartCountBadge = document.getElementById('cart-count-badge');
    
    if (count > 0) {
        if (!cartCountBadge && cartButton) {
            cartCountBadge = document.createElement('span');
            cartCountBadge.id = 'cart-count-badge';
            cartCountBadge.className = 'absolute -top-0.5 -right-0.5 inline-flex items-center justify-center bg-red-500 text-white text-xs font-bold min-w-[18px] h-[18px] px-1 rounded-full border-2 border-white shadow-lg';
            cartButton.appendChild(cartCountBadge);
        }
        if (cartCountBadge) {
            cartCountBadge.textContent = count;
            cartCountBadge.style.display = 'inline-flex';
        }
    } else {
        if (cartCountBadge) {
            cartCountBadge.style.display = 'none';
        }
    }
};

/**
 * Actualizar dropdown del carrito
 */
window.updateCartDropdown = function() {
    renderCartDropdown();
};

/**
 * Toggle del dropdown del carrito
 */
window.toggleCartDropdown = function() {
    const cartMenu = document.getElementById('cart-menu');
    if (!cartMenu) return;
    
    if (cartMenu.style.display === 'none' || cartMenu.style.display === '') {
        // Abrir dropdown y actualizar contenido
        cartMenu.style.display = 'block';
        renderCartDropdown();
    } else {
        // Cerrar dropdown
        cartMenu.style.display = 'none';
    }
};

/**
 * Cerrar dropdown del carrito
 */
window.closeCartDropdown = function() {
    const cartMenu = document.getElementById('cart-menu');
    if (cartMenu) {
        cartMenu.style.display = 'none';
    }
};

/**
 * Función para mostrar notificación de item agregado al carrito
 */
window.showCartNotification = function() {
    const cartButton = document.querySelector('#cart-dropdown button');
    const cartCount = document.querySelector('#cart-dropdown .bg-red-500');

    if (cartButton) {
        cartButton.classList.add('animate-bounce');
        setTimeout(() => {
            cartButton.classList.remove('animate-bounce');
        }, 1000);
    }

    if (cartCount) {
        cartCount.classList.add('animate-pulse');
        setTimeout(() => {
            cartCount.classList.remove('animate-pulse');
        }, 1000);
    }
};

/**
 * Escuchar eventos de carrito actualizado
 */
let cartUpdateInProgress = false;
document.addEventListener('cartUpdated', function() {
    // Prevenir múltiples ejecuciones simultáneas
    if (cartUpdateInProgress) {
        return;
    }
    cartUpdateInProgress = true;
    
    // Mostrar notificación visual
    if (typeof window.showCartNotification === 'function') {
        window.showCartNotification();
    }
    
    // Actualizar contador y dropdown
    if (typeof window.updateCartCount === 'function') {
        window.updateCartCount();
    }
    if (typeof window.updateCartDropdown === 'function') {
        window.updateCartDropdown();
    }
    
    // Sincronizar con servidor en background
    if (typeof window.syncCartWithServer === 'function') {
        window.syncCartWithServer();
    }
    
    // Resetear flag después de un breve delay
    setTimeout(() => {
        cartUpdateInProgress = false;
    }, 100);
});

// Verificar que el script se haya cargado
if (typeof console !== 'undefined') {
    console.log('✅ cart.js loaded successfully');
    console.log('✅ CartConfig:', window.CartConfig);
    console.log('✅ getCart function available:', typeof window.getCart === 'function');
    console.log('✅ getCartCount function available:', typeof window.getCartCount === 'function');
    console.log('✅ updateCartCount function available:', typeof window.updateCartCount === 'function');
}

// Inicializar contador al cargar la página (solo una vez)
let cartInitialized = false;
(function initCart() {
    if (cartInitialized) {
        return;
    }
    
    function doInit() {
        if (cartInitialized) {
            return;
        }
        
        if (typeof window.updateCartCount === 'function') {
            cartInitialized = true;
            window.updateCartCount();
            if (typeof console !== 'undefined') {
                console.log('✅ Cart counter initialized');
            }
        } else {
            if (typeof console !== 'undefined') {
                console.warn('⚠️ updateCartCount not available yet, retrying...');
            }
            setTimeout(doInit, 100);
        }
    }
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(doInit, 200);
        });
    } else {
        setTimeout(doInit, 200);
    }
})();
