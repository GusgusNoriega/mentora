@extends('layouts.dashboard')

@section('title', 'Gestión de Cursos')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-content">Gestión de Cursos</h1>
        <button id="btn-create-course" class="bg-brand-600 hover:bg-brand-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Nuevo Curso
        </button>
    </div>

    <div id="courses-container" class="space-y-4">
        <!-- Los cursos se cargarán aquí dinámicamente -->
    </div>

    <!-- Modal para crear/editar curso -->
    <div id="course-modal" class="fixed inset-0 bg-black bg-opacity-50 dark:bg-opacity-70 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-card rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <h2 id="course-modal-title" class="text-2xl font-bold mb-4 text-content">Crear Curso</h2>
                    <form id="course-form">
                        <input type="hidden" id="course-id" name="id">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium mb-1 text-content">Título</label>
                                <input type="text" id="course-title" name="title" class="w-full px-3 py-2 bg-card border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-transparent text-content" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1 text-content">Slug</label>
                                <input type="text" id="course-slug" name="slug" class="w-full px-3 py-2 bg-card border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-transparent text-content">
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-1 text-content">Resumen</label>
                            <textarea id="course-summary" name="summary" rows="2" class="w-full px-3 py-2 bg-card border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-transparent text-content"></textarea>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-1 text-content">Descripción</label>
                            <textarea id="course-description" name="description" rows="4" class="w-full px-3 py-2 bg-card border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-transparent text-content"></textarea>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium mb-1 text-content">Nivel</label>
                                <select id="course-level" name="level" class="w-full px-3 py-2 bg-card border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-transparent text-content" required>
                                    <option value="beginner">Principiante</option>
                                    <option value="intermediate">Intermedio</option>
                                    <option value="advanced">Avanzado</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1 text-content">Idioma</label>
                                <input type="text" id="course-language" name="language" value="es" class="w-full px-3 py-2 bg-card border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-transparent text-content" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1 text-content">Estado</label>
                                <select id="course-status" name="status" class="w-full px-3 py-2 bg-card border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-transparent text-content" required>
                                    <option value="draft">Borrador</option>
                                    <option value="published">Publicado</option>
                                    <option value="archived">Archivado</option>
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                            <div>
                                <label class="block text-sm font-medium mb-1 text-content">Acceso</label>
                                <select id="course-access-mode" name="access_mode" class="w-full px-3 py-2 bg-card border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-transparent text-content" required>
                                    <option value="free">Gratuito</option>
                                    <option value="paid">Pago</option>
                                    <option value="subscription">Suscripción</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1 text-content">Precio (centavos)</label>
                                <input type="number" id="course-price" name="price_cents" class="w-full px-3 py-2 bg-card border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-transparent text-content">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1 text-content">Moneda</label>
                                <input type="text" id="course-currency" name="currency" value="USD" class="w-full px-3 py-2 bg-card border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-transparent text-content">
                            </div>
                        </div>
                        <div class="flex justify-end space-x-3">
                            <button type="button" id="btn-cancel-course" class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200">Cancelar</button>
                            <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white px-4 py-2 rounded-lg font-medium">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para secciones -->
    <div id="section-modal" class="fixed inset-0 bg-black bg-opacity-50 dark:bg-opacity-70 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-card rounded-lg shadow-xl max-w-md w-full">
                <div class="p-6">
                    <h2 id="section-modal-title" class="text-2xl font-bold mb-4 text-content">Crear Sección</h2>
                    <form id="section-form">
                        <input type="hidden" id="section-course-id" name="course_id">
                        <input type="hidden" id="section-id" name="id">
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-1 text-content">Título</label>
                            <input type="text" id="section-title" name="title" class="w-full px-3 py-2 bg-card border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-transparent text-content" required>
                        </div>
                        <div class="flex justify-end space-x-3">
                            <button type="button" id="btn-cancel-section" class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200">Cancelar</button>
                            <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white px-4 py-2 rounded-lg font-medium">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para lecciones -->
    <div id="lesson-modal" class="fixed inset-0 bg-black bg-opacity-50 dark:bg-opacity-70 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-card rounded-lg shadow-xl max-w-lg w-full">
                <div class="p-6">
                    <h2 id="lesson-modal-title" class="text-2xl font-bold mb-4 text-content">Crear Lección</h2>
                    <form id="lesson-form">
                        <input type="hidden" id="lesson-course-id" name="course_id">
                        <input type="hidden" id="lesson-section-id" name="section_id">
                        <input type="hidden" id="lesson-id" name="id">
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-1 text-content">Título</label>
                            <input type="text" id="lesson-title" name="title" class="w-full px-3 py-2 bg-card border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-transparent text-content" required>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium mb-1 text-content">Tipo de Contenido</label>
                                <select id="lesson-content-type" name="content_type" class="w-full px-3 py-2 bg-card border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-transparent text-content" required>
                                    <option value="video">Video</option>
                                    <option value="text">Texto</option>
                                    <option value="pdf">PDF</option>
                                    <option value="external">Enlace Externo</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1 text-content">Duración (segundos)</label>
                                <input type="number" id="lesson-duration" name="duration_seconds" class="w-full px-3 py-2 bg-card border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-transparent text-content">
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-1 text-content">URL del Contenido</label>
                            <input type="url" id="lesson-content-url" name="content_url" class="w-full px-3 py-2 bg-card border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-transparent text-content">
                        </div>
                        <div class="flex items-center mb-4">
                            <input type="checkbox" id="lesson-is-preview" name="is_preview" class="mr-2">
                            <label for="lesson-is-preview" class="text-sm font-medium text-content">Es vista previa</label>
                        </div>
                        <div class="flex justify-end space-x-3">
                            <button type="button" id="btn-cancel-lesson" class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200">Cancelar</button>
                            <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white px-4 py-2 rounded-lg font-medium">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const apiToken = document.querySelector('meta[name="api-token"]').getAttribute('content');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Headers para API
    const headers = {
        'Authorization': `Bearer ${apiToken}`,
        'X-CSRF-TOKEN': csrfToken,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    };

    // Elementos del DOM
    const coursesContainer = document.getElementById('courses-container');
    const courseModal = document.getElementById('course-modal');
    const courseForm = document.getElementById('course-form');
    const sectionModal = document.getElementById('section-modal');
    const sectionForm = document.getElementById('section-form');
    const lessonModal = document.getElementById('lesson-modal');
    const lessonForm = document.getElementById('lesson-form');

    // Cargar cursos al inicio
    loadCourses();

    // Event listeners
    document.getElementById('btn-create-course').addEventListener('click', () => openCourseModal());
    document.getElementById('btn-cancel-course').addEventListener('click', () => closeCourseModal());
    document.getElementById('btn-cancel-section').addEventListener('click', () => closeSectionModal());
    document.getElementById('btn-cancel-lesson').addEventListener('click', () => closeLessonModal());

    courseForm.addEventListener('submit', handleCourseSubmit);
    sectionForm.addEventListener('submit', handleSectionSubmit);
    lessonForm.addEventListener('submit', handleLessonSubmit);

    // Funciones principales
    async function loadCourses() {
        try {
            const response = await fetch('/mentora/public/api/courses', { headers });
            const data = await response.json();
            if (data.success) {
                renderCourses(data.data.data);
            }
        } catch (error) {
            console.error('Error loading courses:', error);
        }
    }

    function renderCourses(courses) {
        coursesContainer.innerHTML = '';
        if (courses.length === 0) {
            coursesContainer.innerHTML = '<p class="text-center text-muted py-8 text-content">No hay cursos disponibles. Crea tu primer curso.</p>';
            return;
        }

        courses.forEach(course => {
            const courseElement = createCourseElement(course);
            coursesContainer.appendChild(courseElement);
        });
    }

    function createCourseElement(course) {
        const div = document.createElement('div');
        div.className = 'bg-card rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6';
        div.innerHTML = `
            <div class="flex justify-between items-start mb-4">
                <div class="flex-1">
                    <h3 class="text-xl font-semibold text-content">${course.title}</h3>
                    <p class="text-muted mt-1">${course.summary || 'Sin resumen'}</p>
                    <div class="flex items-center space-x-4 mt-2 text-sm text-muted">
                        <span>Nivel: ${course.level}</span>
                        <span>Estado: ${course.status}</span>
                        <span>Acceso: ${course.access_mode}</span>
                        <span>Secciones: ${course.sections_count || 0}</span>
                        <span>Lecciones: ${course.lessons_count || 0}</span>
                    </div>
                </div>
                <div class="flex space-x-2">
                    <button class="btn-edit-course bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm" data-id="${course.id}">Editar</button>
                    <button class="btn-delete-course bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm" data-id="${course.id}">Eliminar</button>
                    <button class="btn-toggle-sections bg-gray-500 hover:bg-gray-600 text-white px-3 py-1 rounded text-sm" data-id="${course.id}">Secciones</button>
                </div>
            </div>
            <div class="sections-container hidden" data-course-id="${course.id}">
                <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                    <div class="flex justify-between items-center mb-3">
                        <h4 class="font-medium text-content">Secciones</h4>
                        <button class="btn-create-section bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm" data-course-id="${course.id}">Nueva Sección</button>
                    </div>
                    <div class="sections-list space-y-2" data-course-id="${course.id}">
                        <!-- Secciones se cargarán aquí -->
                    </div>
                </div>
            </div>
        `;

        // Event listeners para el curso
        div.querySelector('.btn-edit-course').addEventListener('click', () => editCourse(course.id));
        div.querySelector('.btn-delete-course').addEventListener('click', () => deleteCourse(course.id));
        div.querySelector('.btn-toggle-sections').addEventListener('click', () => toggleSections(course.id));
        div.querySelector('.btn-create-section').addEventListener('click', () => openSectionModal(course.id));

        return div;
    }

    async function toggleSections(courseId) {
        const container = document.querySelector(`.sections-container[data-course-id="${courseId}"]`);
        const isHidden = container.classList.contains('hidden');

        if (isHidden) {
            await loadSections(courseId);
            container.classList.remove('hidden');
        } else {
            container.classList.add('hidden');
        }
    }

    async function loadSections(courseId) {
        try {
            const response = await fetch(`/mentora/public/api/courses/${courseId}/sections`, { headers });
            const data = await response.json();
            if (data.success) {
                renderSections(courseId, data.data);
            }
        } catch (error) {
            console.error('Error loading sections:', error);
        }
    }

    function renderSections(courseId, sections) {
        const container = document.querySelector(`.sections-list[data-course-id="${courseId}"]`);
        container.innerHTML = '';

        if (sections.length === 0) {
            container.innerHTML = '<p class="text-muted text-sm text-content">No hay secciones en este curso.</p>';
            return;
        }

        sections.forEach(section => {
            const sectionElement = createSectionElement(courseId, section);
            container.appendChild(sectionElement);
        });
    }

    function createSectionElement(courseId, section) {
        const div = document.createElement('div');
        div.className = 'bg-gray-50 dark:bg-gray-800 rounded p-3';
        div.innerHTML = `
            <div class="flex justify-between items-center mb-2">
                <h5 class="font-medium text-content">${section.title}</h5>
                <div class="flex space-x-2">
                    <button class="btn-edit-section bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-xs" data-course-id="${courseId}" data-id="${section.id}">Editar</button>
                    <button class="btn-delete-section bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-xs" data-course-id="${courseId}" data-id="${section.id}">Eliminar</button>
                    <button class="btn-toggle-lessons bg-gray-500 hover:bg-gray-600 text-white px-2 py-1 rounded text-xs" data-course-id="${courseId}" data-id="${section.id}">Lecciones</button>
                </div>
            </div>
            <div class="lessons-container hidden" data-section-id="${section.id}">
                <div class="border-t border-gray-200 dark:border-gray-700 pt-2 mt-2">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-content">Lecciones</span>
                        <button class="btn-create-lesson bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded text-xs" data-course-id="${courseId}" data-section-id="${section.id}">Nueva Lección</button>
                    </div>
                    <div class="lessons-list space-y-1" data-section-id="${section.id}">
                        <!-- Lecciones se cargarán aquí -->
                    </div>
                </div>
            </div>
        `;

        // Event listeners para la sección
        div.querySelector('.btn-edit-section').addEventListener('click', () => editSection(courseId, section.id));
        div.querySelector('.btn-delete-section').addEventListener('click', () => deleteSection(courseId, section.id));
        div.querySelector('.btn-toggle-lessons').addEventListener('click', () => toggleLessons(courseId, section.id));
        div.querySelector('.btn-create-lesson').addEventListener('click', () => openLessonModal(courseId, section.id));

        return div;
    }

    async function toggleLessons(courseId, sectionId) {
        const container = document.querySelector(`.lessons-container[data-section-id="${sectionId}"]`);
        const isHidden = container.classList.contains('hidden');

        if (isHidden) {
            await loadLessons(courseId, sectionId);
            container.classList.remove('hidden');
        } else {
            container.classList.add('hidden');
        }
    }

    async function loadLessons(courseId, sectionId) {
        try {
            const response = await fetch(`/mentora/public/api/courses/${courseId}/sections/${sectionId}/lessons`, { headers });
            const data = await response.json();
            if (data.success) {
                renderLessons(sectionId, data.data);
            }
        } catch (error) {
            console.error('Error loading lessons:', error);
        }
    }

    function renderLessons(sectionId, lessons) {
        const container = document.querySelector(`.lessons-list[data-section-id="${sectionId}"]`);
        container.innerHTML = '';

        if (lessons.length === 0) {
            container.innerHTML = '<p class="text-muted text-xs text-content">No hay lecciones en esta sección.</p>';
            return;
        }

        lessons.forEach(lesson => {
            const lessonElement = createLessonElement(lesson);
            container.appendChild(lessonElement);
        });
    }

    function createLessonElement(lesson) {
        const div = document.createElement('div');
        div.className = 'bg-white dark:bg-gray-700 rounded p-2 text-sm';
        div.innerHTML = `
            <div class="flex justify-between items-center">
                <div>
                    <span class="font-medium text-content">${lesson.title}</span>
                    <span class="text-muted ml-2">(${lesson.content_type})</span>
                    ${lesson.is_preview ? '<span class="bg-yellow-200 dark:bg-yellow-600 text-yellow-800 dark:text-yellow-200 px-1 rounded text-xs ml-1">Vista previa</span>' : ''}
                </div>
                <div class="flex space-x-1">
                    <button class="btn-edit-lesson bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-xs" data-id="${lesson.id}">Editar</button>
                    <button class="btn-delete-lesson bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-xs" data-id="${lesson.id}">Eliminar</button>
                </div>
            </div>
        `;

        // Event listeners para la lección
        div.querySelector('.btn-edit-lesson').addEventListener('click', () => editLesson(lesson));
        div.querySelector('.btn-delete-lesson').addEventListener('click', () => deleteLesson(lesson));

        return div;
    }

    // Funciones para modales
    function openCourseModal(course = null) {
        if (course) {
            document.getElementById('course-modal-title').textContent = 'Editar Curso';
            document.getElementById('course-id').value = course.id;
            document.getElementById('course-title').value = course.title;
            document.getElementById('course-slug').value = course.slug;
            document.getElementById('course-summary').value = course.summary;
            document.getElementById('course-description').value = course.description;
            document.getElementById('course-level').value = course.level;
            document.getElementById('course-language').value = course.language;
            document.getElementById('course-status').value = course.status;
            document.getElementById('course-access-mode').value = course.access_mode;
            document.getElementById('course-price').value = course.price_cents;
            document.getElementById('course-currency').value = course.currency;
        } else {
            document.getElementById('course-modal-title').textContent = 'Crear Curso';
            courseForm.reset();
        }
        courseModal.classList.remove('hidden');
    }

    function closeCourseModal() {
        courseModal.classList.add('hidden');
    }

    function openSectionModal(courseId, section = null) {
        document.getElementById('section-course-id').value = courseId;
        if (section) {
            document.getElementById('section-modal-title').textContent = 'Editar Sección';
            document.getElementById('section-id').value = section.id;
            document.getElementById('section-title').value = section.title;
        } else {
            document.getElementById('section-modal-title').textContent = 'Crear Sección';
            sectionForm.reset();
            document.getElementById('section-course-id').value = courseId;
        }
        sectionModal.classList.remove('hidden');
    }

    function closeSectionModal() {
        sectionModal.classList.add('hidden');
    }

    function openLessonModal(courseId, sectionId, lesson = null) {
        document.getElementById('lesson-course-id').value = courseId;
        document.getElementById('lesson-section-id').value = sectionId;
        if (lesson) {
            document.getElementById('lesson-modal-title').textContent = 'Editar Lección';
            document.getElementById('lesson-id').value = lesson.id;
            document.getElementById('lesson-title').value = lesson.title;
            document.getElementById('lesson-content-type').value = lesson.content_type;
            document.getElementById('lesson-content-url').value = lesson.content_url;
            document.getElementById('lesson-duration').value = lesson.duration_seconds;
            document.getElementById('lesson-is-preview').checked = lesson.is_preview;
        } else {
            document.getElementById('lesson-modal-title').textContent = 'Crear Lección';
            lessonForm.reset();
            document.getElementById('lesson-course-id').value = courseId;
            document.getElementById('lesson-section-id').value = sectionId;
        }
        lessonModal.classList.remove('hidden');
    }

    function closeLessonModal() {
        lessonModal.classList.add('hidden');
    }

    // Funciones para editar
    async function editCourse(id) {
        try {
            const response = await fetch(`/mentora/public/api/courses/${id}`, { headers });
            const data = await response.json();
            if (data.success) {
                openCourseModal(data.data);
            }
        } catch (error) {
            console.error('Error loading course:', error);
        }
    }

    async function editSection(courseId, id) {
        try {
            const response = await fetch(`/mentora/public/api/courses/${courseId}/sections/${id}`, { headers });
            const data = await response.json();
            if (data.success) {
                openSectionModal(courseId, data.data);
            }
        } catch (error) {
            console.error('Error loading section:', error);
        }
    }

    function editLesson(lesson) {
        const courseId = document.querySelector('.sections-container:not(.hidden)').dataset.courseId;
        const sectionId = lesson.section_id;
        openLessonModal(courseId, sectionId, lesson);
    }

    // Funciones para eliminar
    async function deleteCourse(id) {
        if (!confirm('¿Estás seguro de que quieres eliminar este curso?')) return;

        try {
            const response = await fetch(`/mentora/public/api/courses/${id}`, {
                method: 'DELETE',
                headers
            });
            const data = await response.json();
            if (data.success) {
                loadCourses();
            } else {
                alert(data.meta.message);
            }
        } catch (error) {
            console.error('Error deleting course:', error);
        }
    }

    async function deleteSection(courseId, id) {
        if (!confirm('¿Estás seguro de que quieres eliminar esta sección?')) return;

        try {
            const response = await fetch(`/mentora/public/api/courses/${courseId}/sections/${id}`, {
                method: 'DELETE',
                headers
            });
            const data = await response.json();
            if (data.success) {
                loadSections(courseId);
            } else {
                alert(data.meta.message);
            }
        } catch (error) {
            console.error('Error deleting section:', error);
        }
    }

    async function deleteLesson(lesson) {
        if (!confirm('¿Estás seguro de que quieres eliminar esta lección?')) return;

        const courseId = document.querySelector('.sections-container:not(.hidden)').dataset.courseId;
        const sectionId = lesson.section_id;

        try {
            const response = await fetch(`/mentora/public/api/courses/${courseId}/sections/${sectionId}/lessons/${lesson.id}`, {
                method: 'DELETE',
                headers
            });
            const data = await response.json();
            if (data.success) {
                loadLessons(courseId, sectionId);
            } else {
                alert(data.meta.message);
            }
        } catch (error) {
            console.error('Error deleting lesson:', error);
        }
    }

    // Funciones para manejar submits
    async function handleCourseSubmit(e) {
        e.preventDefault();
        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData);
        const isEdit = data.id;

        try {
            const response = await fetch(`/mentora/public/api/courses${isEdit ? `/${data.id}` : ''}`, {
                method: isEdit ? 'PUT' : 'POST',
                headers,
                body: JSON.stringify(data)
            });
            const result = await response.json();
            if (result.success) {
                closeCourseModal();
                loadCourses();
            } else {
                alert(result.meta.message);
            }
        } catch (error) {
            console.error('Error saving course:', error);
        }
    }

    async function handleSectionSubmit(e) {
        e.preventDefault();
        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData);
        const courseId = data.course_id;
        const isEdit = data.id;

        try {
            const response = await fetch(`/mentora/public/api/courses/${courseId}/sections${isEdit ? `/${data.id}` : ''}`, {
                method: isEdit ? 'PUT' : 'POST',
                headers,
                body: JSON.stringify(data)
            });
            const result = await response.json();
            if (result.success) {
                closeSectionModal();
                loadSections(courseId);
            } else {
                alert(result.meta.message);
            }
        } catch (error) {
            console.error('Error saving section:', error);
        }
    }

    async function handleLessonSubmit(e) {
        e.preventDefault();
        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData);
        const courseId = data.course_id;
        const sectionId = data.section_id;
        const isEdit = data.id;

        // Convertir checkbox a boolean
        data.is_preview = data.is_preview === 'on';

        try {
            const response = await fetch(`/mentora/public/api/courses/${courseId}/sections/${sectionId}/lessons${isEdit ? `/${data.id}` : ''}`, {
                method: isEdit ? 'PUT' : 'POST',
                headers,
                body: JSON.stringify(data)
            });
            const result = await response.json();
            if (result.success) {
                closeLessonModal();
                loadLessons(courseId, sectionId);
            } else {
                alert(result.meta.message);
            }
        } catch (error) {
            console.error('Error saving lesson:', error);
        }
    }
});
</script>
@endpush