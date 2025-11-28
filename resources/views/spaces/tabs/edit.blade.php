<!-- Tab: Editar (solo admin) -->
<div>
    <h2 class="text-3xl font-bold text-gray-900 mb-8">Editar Espacio</h2>
    
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-yellow-700">
                    Solo los administradores del espacio pueden editar esta información.
                </p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-xl p-8">
        <form action="{{ route('spaces.update', $space->subdomain) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="space-y-6">
                <!-- Nombre -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nombre del Espacio</label>
                    <input type="text" 
                           name="name" 
                           id="name" 
                           value="{{ old('name', $space->name) }}"
                           required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                </div>

                <!-- Descripción -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                    <textarea name="description" 
                              id="description" 
                              rows="3"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">{{ old('description', $space->description) }}</textarea>
                </div>

                <!-- About -->
                <div>
                    <label for="about" class="block text-sm font-medium text-gray-700 mb-2">Acerca de</label>
                    <textarea name="about" 
                              id="about" 
                              rows="5"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">{{ old('about', $space->about) }}</textarea>
                </div>

                <!-- Keywords -->
                <div>
                    <label for="keywords" class="block text-sm font-medium text-gray-700 mb-2">Palabras Clave (Keywords)</label>
                    <textarea name="keywords" 
                              id="keywords" 
                              rows="3"
                              placeholder="Ej: música, conciertos, entretenimiento, eventos en vivo"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">{{ old('keywords', $space->keywords) }}</textarea>
                    <p class="mt-2 text-sm text-gray-500 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        Separa las palabras clave con comas. Estas ayudan a mejorar la búsqueda y SEO del espacio.
                    </p>
                </div>

                <!-- Colores -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="color_primary" class="block text-sm font-medium text-gray-700 mb-2">Color Primario</label>
                        <div class="flex items-center space-x-3">
                            <input type="color" 
                                   name="color_primary" 
                                   id="color_primary" 
                                   value="{{ old('color_primary', $space->color_primary ?? '#3b82f6') }}"
                                   class="w-16 h-16 rounded-lg border-2 border-gray-300 cursor-pointer">
                            <input type="text" 
                                   value="{{ old('color_primary', $space->color_primary ?? '#3b82f6') }}"
                                   class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   readonly>
                        </div>
                    </div>
                    <div>
                        <label for="color_secondary" class="block text-sm font-medium text-gray-700 mb-2">Color Secundario</label>
                        <div class="flex items-center space-x-3">
                            <input type="color" 
                                   name="color_secondary" 
                                   id="color_secondary" 
                                   value="{{ old('color_secondary', $space->color_secondary ?? '#8b5cf6') }}"
                                   class="w-16 h-16 rounded-lg border-2 border-gray-300 cursor-pointer">
                            <input type="text" 
                                   value="{{ old('color_secondary', $space->color_secondary ?? '#8b5cf6') }}"
                                   class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   readonly>
                        </div>
                    </div>
                </div>

                <!-- Logo y Banner -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="logo" class="block text-sm font-medium text-gray-700 mb-2">Logo</label>
                        @if($space->logo)
                            <img src="{{ \App\Helpers\ImageHelper::getImageUrl($space->logo) }}" alt="Logo actual" class="w-32 h-32 object-cover rounded-lg mb-3">
                        @endif
                        <input type="file" 
                               name="logo" 
                               id="logo" 
                               accept="image/*"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="banner" class="block text-sm font-medium text-gray-700 mb-2">Banner</label>
                        @if($space->banner)
                            <img src="{{ \App\Helpers\ImageHelper::getImageUrl($space->banner) }}" alt="Banner actual" class="w-full h-48 object-cover rounded-lg mb-3">
                        @endif
                        <input type="file" 
                               name="banner" 
                               id="banner" 
                               accept="image/*"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <!-- Información de Contacto -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="location" class="block text-sm font-medium text-gray-700 mb-2">Ubicación</label>
                        <input type="text" 
                               name="location" 
                               id="location" 
                               value="{{ old('location', $space->location) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="website" class="block text-sm font-medium text-gray-700 mb-2">Sitio Web</label>
                        <input type="url" 
                               name="website" 
                               id="website" 
                               value="{{ old('website', $space->website) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="contact_email" class="block text-sm font-medium text-gray-700 mb-2">Email de Contacto</label>
                        <input type="email" 
                               name="contact_email" 
                               id="contact_email" 
                               value="{{ old('contact_email', $space->contact_email) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="contact_phone" class="block text-sm font-medium text-gray-700 mb-2">Teléfono</label>
                        <input type="text" 
                               name="contact_phone" 
                               id="contact_phone" 
                               value="{{ old('contact_phone', $space->contact_phone) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <!-- Redes Sociales -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="social_facebook" class="block text-sm font-medium text-gray-700 mb-2">Facebook</label>
                        <input type="url" 
                               name="social_facebook" 
                               id="social_facebook" 
                               value="{{ old('social_facebook', $space->social_facebook) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="social_instagram" class="block text-sm font-medium text-gray-700 mb-2">Instagram</label>
                        <input type="url" 
                               name="social_instagram" 
                               id="social_instagram" 
                               value="{{ old('social_instagram', $space->social_instagram) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="social_twitter" class="block text-sm font-medium text-gray-700 mb-2">Twitter</label>
                        <input type="url" 
                               name="social_twitter" 
                               id="social_twitter" 
                               value="{{ old('social_twitter', $space->social_twitter) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('spaces.profile', $space->subdomain) }}" 
                       class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 font-semibold hover:bg-gray-50 transition-colors">
                        Cancelar
                    </a>
                    <button type="submit" 
                            class="px-6 py-3 rounded-lg text-white font-semibold transition-all duration-200 hover:shadow-lg transform hover:scale-105"
                            style="background: linear-gradient(135deg, {{ $space->color_primary }}, {{ $space->color_secondary ?? $space->color_primary }});">
                        Guardar Cambios
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
// Sincronizar color picker con input de texto
document.getElementById('color_primary')?.addEventListener('input', function() {
    this.nextElementSibling.value = this.value;
});

document.getElementById('color_secondary')?.addEventListener('input', function() {
    this.nextElementSibling.value = this.value;
});
</script>

