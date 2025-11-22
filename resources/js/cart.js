/**
 * Sistema Global de Carrito basado en sesión del servidor
 * Funciona en dominio base y subdominios
 */

// Configuración global del carrito
window.CartConfig = {
    baseUrl: null,
    cartAddUrl: null,
    cartCountUrl: null,
    cartDropdownUrl: null,
    csrfToken: null
};

// Cache del carrito (solo para UI, no como fuente de verdad)
let cartCache = null;
let cartCacheTimestamp = 0;
const CART_CACHE_TTL = 5000; // 5 segundos

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
    CartConfig.cartCountUrl = CartConfig.baseUrl + '/cart/count';
    CartConfig.cartDropdownUrl = CartConfig.baseUrl + '/cart/dropdown';
    
    // Debug: verificar configuración
    if (typeof console !== 'undefined') {
        console.log('✅ CartConfig initialized:', CartConfig);
    }
})();

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
 * Obtener conteo del carrito desde el servidor
 */
window.getCartCount = async function() {
    try {
        let csrfToken = CartConfig.csrfToken;
        if (!csrfToken) {
            csrfToken = await getCsrfToken();
        }
        
        const response = await fetch(CartConfig.cartCountUrl, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken || ''
            },
            credentials: 'include',
            cache: 'no-cache'
        });
        
        if (response.ok) {
            const data = await response.json();
            return data.count || 0;
        }
    } catch (error) {
        console.error('Error getting cart count:', error);
    }
    
    return 0;
};

/**
 * Obtener carrito completo desde el servidor
 */
window.getCartFromServer = async function() {
    try {
        // Usar cache si está disponible y no ha expirado
        const now = Date.now();
        if (cartCache && (now - cartCacheTimestamp) < CART_CACHE_TTL) {
            return cartCache;
        }
        
        let csrfToken = CartConfig.csrfToken;
        if (!csrfToken) {
            csrfToken = await getCsrfToken();
        }
        
        const response = await fetch(CartConfig.cartDropdownUrl, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken || ''
            },
            credentials: 'include',
            cache: 'no-cache'
        });
        
        if (response.ok) {
            const data = await response.json();
            cartCache = data;
            cartCacheTimestamp = now;
            return data;
        }
    } catch (error) {
        console.error('Error getting cart from server:', error);
    }
    
    return null;
};

/**
 * Actualizar contador del carrito en la UI
 */
window.updateCartCount = async function() {
    const count = await getCartCount();
    
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
 * Renderizar dropdown del carrito desde el servidor
 */
window.renderCartDropdown = async function() {
    const cartMenu = document.getElementById('cart-menu');
    
    if (!cartMenu) return;
    
    // Prevenir ciclos infinitos - usar flag
    if (cartMenu.dataset.rendering === 'true') {
        return;
    }
    cartMenu.dataset.rendering = 'true';
    
    // Mostrar loader
    cartMenu.innerHTML = `
        <div class="px-4 py-8 text-center">
            <div class="w-8 h-8 border-4 border-blue-500 border-t-transparent rounded-full animate-spin mx-auto mb-2"></div>
            <p class="text-sm text-gray-500">Cargando carrito...</p>
        </div>
    `;
    
    try {
        const cartData = await getCartFromServer();
        
        if (!cartData) {
            cartMenu.innerHTML = `
                <div class="px-4 py-8 text-center">
                    <p class="text-sm text-gray-500">Error al cargar el carrito</p>
                </div>
            `;
            cartMenu.dataset.rendering = 'false';
            return;
        }
        
        const count = cartData.count || 0;
        const html = cartData.html || '';
        
        cartMenu.innerHTML = html;
        
        // Actualizar contador sin disparar eventos
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
    } catch (error) {
        console.error('Error rendering cart dropdown:', error);
        cartMenu.innerHTML = `
            <div class="px-4 py-8 text-center">
                <p class="text-sm text-red-500">Error al cargar el carrito</p>
            </div>
        `;
    }
    
    cartMenu.dataset.rendering = 'false';
};

/**
 * Actualizar dropdown del carrito
 */
window.updateCartDropdown = function() {
    // Invalidar cache
    cartCache = null;
    cartCacheTimestamp = 0;
    renderCartDropdown();
};

/**
 * Toggle del dropdown del carrito
 */
window.toggleCartDropdown = function() {
    const cartMenu = document.getElementById('cart-menu');
    if (!cartMenu) return;
    
    if (cartMenu.style.display === 'none' || cartMenu.style.display === '') {
        // Abrir dropdown y actualizar contenido desde el servidor
        cartMenu.style.display = 'block';
        updateCartDropdown();
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
    const cartCount = document.querySelector('#cart-count-badge');

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
 * Invalidar cache del carrito
 */
window.invalidateCartCache = function() {
    cartCache = null;
    cartCacheTimestamp = 0;
};

/**
 * Escuchar eventos de carrito actualizado
 */
let cartUpdateInProgress = false;
document.addEventListener('cartUpdated', async function() {
    // Prevenir múltiples ejecuciones simultáneas
    if (cartUpdateInProgress) {
        return;
    }
    cartUpdateInProgress = true;
    
    // Invalidar cache
    invalidateCartCache();
    
    // Mostrar notificación visual
    if (typeof window.showCartNotification === 'function') {
        window.showCartNotification();
    }
    
    // Actualizar contador y dropdown
    if (typeof window.updateCartCount === 'function') {
        await window.updateCartCount();
    }
    if (typeof window.updateCartDropdown === 'function') {
        window.updateCartDropdown();
    }
    
    // Resetear flag después de un breve delay
    setTimeout(() => {
        cartUpdateInProgress = false;
    }, 100);
});

// Verificar que el script se haya cargado
if (typeof console !== 'undefined') {
    console.log('✅ cart.js loaded successfully (server-based)');
    console.log('✅ CartConfig:', window.CartConfig);
}

// Inicializar contador al cargar la página (solo una vez)
let cartInitialized = false;
(function initCart() {
    if (cartInitialized) {
        return;
    }
    
    async function doInit() {
        if (cartInitialized) {
            return;
        }
        
        if (typeof window.updateCartCount === 'function') {
            cartInitialized = true;
            await window.updateCartCount();
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
