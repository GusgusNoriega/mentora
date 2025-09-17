@extends('layouts.dashboard')

@section('title', 'Prueba API Media')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Prueba API de Media Assets</h1>

            <!-- Formulario para subir archivo -->
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Subir Nuevo Archivo</h2>
                <form id="uploadForm" enctype="multipart/form-data">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="file" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Archivo</label>
                            <input type="file" id="file" name="file" required
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tipo (opcional)</label>
                            <input type="text" id="type" name="type" placeholder="Ej: video, audio, image, etc."
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label for="provider" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Proveedor</label>
                            <input type="text" id="provider" name="provider" placeholder="Opcional"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        </div>
                    </div>
                    <button type="submit" class="mt-4 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Subir Archivo
                    </button>
                </form>
            </div>

            <!-- Lista de archivos -->
            <div>
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Archivos Multimedia</h2>
                    <button id="loadMedia" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                        Cargar Archivos
                    </button>
                </div>
                <div id="mediaList" class="space-y-4">
                    <!-- Los archivos se cargarán aquí -->
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const baseUrl = window.location.origin;

    // Función para obtener headers sin token
    function getHeaders() {
        return {
            'Accept': 'application/json'
        };
    }

    // Cargar lista de media
    async function loadMedia() {
        try {
            const response = await fetch(`${baseUrl}/api/media-assets`, {
                headers: getHeaders()
            });

            if (!response.ok) {
                throw new Error(`Error: ${response.status}`);
            }

            const mediaAssets = await response.json();
            displayMedia(mediaAssets);
        } catch (error) {
            console.error('Error cargando media:', error);
            alert('Error al cargar los archivos multimedia');
        }
    }

    // Mostrar media en la lista
    function displayMedia(mediaAssets) {
        const mediaList = document.getElementById('mediaList');
        mediaList.innerHTML = '';

        if (mediaAssets.length === 0) {
            mediaList.innerHTML = '<p class="text-gray-500 dark:text-gray-400">No hay archivos multimedia.</p>';
            return;
        }

        mediaAssets.forEach(asset => {
            const assetDiv = document.createElement('div');
            assetDiv.className = 'bg-gray-50 dark:bg-gray-700 p-4 rounded-lg';
            assetDiv.innerHTML = `
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <h3 class="font-semibold text-gray-900 dark:text-white">${asset.type.toUpperCase()}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-300">Proveedor: ${asset.provider || 'N/A'}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-300">Tamaño: ${formatBytes(asset.size_bytes)}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-300">URL: <a href="${asset.url}" target="_blank" class="text-blue-500 hover:text-blue-700">${asset.url}</a></p>
                        <p class="text-sm text-gray-600 dark:text-gray-300">Creado: ${new Date(asset.created_at).toLocaleString()}</p>
                    </div>
                    <div class="flex space-x-2">
                        <button onclick="editMedia(${asset.id})" class="bg-yellow-500 hover:bg-yellow-700 text-white px-3 py-1 rounded text-sm">Editar</button>
                        <button onclick="deleteMedia(${asset.id})" class="bg-red-500 hover:bg-red-700 text-white px-3 py-1 rounded text-sm">Eliminar</button>
                    </div>
                </div>
            `;
            mediaList.appendChild(assetDiv);
        });
    }

    // Formatear bytes
    function formatBytes(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Subir archivo
    document.getElementById('uploadForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        // Enviar FormData

        try {
            const response = await fetch(`${baseUrl}/api/media-assets`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json'
                },
                credentials: 'same-origin',
                body: formData
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || `Error: ${response.status}`);
            }

            const result = await response.json();
            alert('Archivo subido exitosamente');
            this.reset();
            loadMedia(); // Recargar lista
        } catch (error) {
            console.error('Error subiendo archivo:', error);
            alert('Error al subir el archivo');
        }
    });

    // Editar media (simplificado, solo tipo y proveedor)
    window.editMedia = async function(id) {
        const newType = prompt('Nuevo tipo (opcional, ej: video, audio, image, etc.):');
        const newProvider = prompt('Nuevo proveedor (opcional):');

        if (newType === null) return; // Cancelado

        try {
            const response = await fetch(`${baseUrl}/api/media-assets/${id}`, {
                method: 'PUT',
                headers: {
                    ...getHeaders(),
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    ...(newType && { type: newType }),
                    ...(newProvider && { provider: newProvider })
                })
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || `Error: ${response.status}`);
            }

            alert('Archivo actualizado');
            loadMedia();
        } catch (error) {
            console.error('Error actualizando:', error);
            alert('Error al actualizar: ' + error.message);
        }
    };

    // Eliminar media
    window.deleteMedia = async function(id) {
        if (!confirm('¿Estás seguro de que quieres eliminar este archivo?')) return;

        try {
            const response = await fetch(`${baseUrl}/api/media-assets/${id}`, {
                method: 'DELETE',
                headers: getHeaders()
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || `Error: ${response.status}`);
            }

            alert('Archivo eliminado');
            loadMedia();
        } catch (error) {
            console.error('Error eliminando:', error);
            alert('Error al eliminar: ' + error.message);
        }
    };

    // Cargar media al hacer clic en el botón
    document.getElementById('loadMedia').addEventListener('click', loadMedia);

    // Cargar media automáticamente al cargar la página
    loadMedia();
});
</script>
@endpush
@endsection