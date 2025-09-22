@props([
    'name' => 'role',
    'id' => 'role-select-' . uniqid(),
    'value' => '',
    'placeholder' => 'Selecciona un rol',
    'required' => false
])

<select
    id="{{ $id }}"
    name="{{ $name }}"
    class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-700 bg-surface px-3 py-2 focus:outline-none focus:ring-2 focus:ring-ring"
    {{ $required ? 'required' : '' }}
>
    <option value="">{{ $placeholder }}</option>
    <!-- Opciones se cargarán vía JS -->
</select>

<script>
(function() {
    const select = document.getElementById('{{ $id }}');
    const TOKEN = document.querySelector('meta[name="api-token"]')?.content || '';
    const API_BASE = '/mentora/public/api';

    if (!select) return;

    async function loadRoles() {
        if (!TOKEN) {
            console.error('No se encontró token API');
            return;
        }

        try {
            const res = await fetch(`${API_BASE}/rbac/roles`, {
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${TOKEN}`
                }
            });

            if (!res.ok) {
                console.error('Error al cargar roles:', res.status);
                return;
            }

            const data = await res.json();
            if (data.success && data.data) {
                // Limpiar opciones existentes excepto la primera
                while (select.options.length > 1) {
                    select.remove(1);
                }

                // Agregar roles
                data.data.forEach(role => {
                    const option = document.createElement('option');
                    option.value = role.name; // Usar name como value
                    option.textContent = role.name;
                    if (role.name === '{{ $value }}') {
                        option.selected = true;
                    }
                    select.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Error al cargar roles:', error);
        }
    }

    // Cargar roles al cargar la página
    loadRoles();
})();
</script>