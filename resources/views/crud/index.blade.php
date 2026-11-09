@extends('layouts.app')

@section('content')
<div class="container-fluid p-0" id="crud-app">
    <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">Gestión de datos</h1>
            <p class="text-secondary mb-0">Prueba las operaciones disponibles para cada recurso.</p>
        </div>
        <div class="d-flex flex-wrap gap-2 align-items-end">
            <div>
                <label for="resource-select" class="form-label small mb-1">Recurso</label>
                <select id="resource-select" class="form-select"></select>
            </div>
            <button type="button" class="btn btn-outline-secondary" id="refresh-button">Actualizar</button>
            <button type="button" class="btn btn-primary" id="new-button">Nuevo registro</button>
        </div>
    </div>

    <div id="status-message" class="alert d-none" role="alert"></div>

    <section class="card border-0 shadow-sm mb-4" aria-labelledby="records-title">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h2 class="h6 fw-bold mb-0" id="records-title">Registros</h2>
            <span class="small text-secondary" id="records-count"></span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light"><tr id="table-head"></tr></thead>
                <tbody id="records-body">
                    <tr><td class="text-center text-secondary py-4">Cargando...</td></tr>
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white d-flex justify-content-between align-items-center">
            <button type="button" class="btn btn-sm btn-outline-secondary" id="previous-button" disabled>Anterior</button>
            <span class="small text-secondary" id="page-label"></span>
            <button type="button" class="btn btn-sm btn-outline-secondary" id="next-button" disabled>Siguiente</button>
        </div>
    </section>

    <section class="card border-0 shadow-sm" id="editor-card" aria-labelledby="editor-title">
        <div class="card-header bg-white py-3">
            <h2 class="h6 fw-bold mb-0" id="editor-title">Crear registro</h2>
        </div>
        <div class="card-body">
            <form id="record-form" novalidate>
                <div class="row g-3" id="form-fields"></div>
                <div class="d-flex gap-2 mt-4">
                    <button class="btn btn-primary" id="save-button" type="submit">Guardar</button>
                    <button class="btn btn-outline-secondary d-none" id="cancel-edit-button" type="button">Cancelar edición</button>
                </div>
            </form>
        </div>
    </section>
</div>

<dialog id="details-dialog" class="border-0 rounded shadow p-0" style="width:min(720px, 94vw)">
    <div class="d-flex justify-content-between align-items-center border-bottom p-3">
        <h2 class="h6 fw-bold mb-0">Detalle del registro</h2>
        <button type="button" class="btn-close" id="close-details-button" aria-label="Cerrar"></button>
    </div>
    <pre class="p-3 mb-0 overflow-auto" style="max-height:70vh" id="details-content"></pre>
</dialog>

<script>
(() => {
    const resources = {
        'academic-periods': {
            label: 'Periodos académicos', endpoint: '/academic-periods', key: 'id_periodo',
            columns: ['id_periodo', 'nombre_periodo', 'fecha_inicio', 'fecha_fin'],
            fields: [
                { name: 'nombre_periodo', label: 'Nombre del periodo', required: true, max: 30 },
                { name: 'fecha_inicio', label: 'Fecha de inicio', type: 'date', required: true },
                { name: 'fecha_fin', label: 'Fecha de finalización', type: 'date', required: true },
            ],
        },
        courses: {
            label: 'Asignaturas', endpoint: '/courses', key: 'id_asignatura',
            columns: ['id_asignatura', 'codigo_asig', 'nombre_asig', 'programa_academico', 'creditos'],
            fields: [
                { name: 'codigo_asig', label: 'Código', required: true, max: 20 },
                { name: 'nombre_asig', label: 'Nombre', required: true, max: 120 },
                { name: 'programa_academico', label: 'Programa académico', required: true, max: 80 },
                { name: 'creditos', label: 'Créditos', type: 'number', min: 0, max: 255, step: 1, required: true },
            ],
        },
        students: {
            label: 'Estudiantes', endpoint: '/students', key: 'id_estudiante',
            columns: ['id_estudiante', 'codigo_estud', 'doc_identidad', 'nombres', 'apellidos', 'id_usuario'],
            fields: [
                { name: 'id_usuario', label: 'Usuario', relation: 'users', required: true },
                { name: 'codigo_estud', label: 'Código estudiantil', required: true, max: 20 },
                { name: 'doc_identidad', label: 'Documento de identidad', required: true, max: 20 },
                { name: 'nombres', label: 'Nombres', required: true, max: 80 },
                { name: 'apellidos', label: 'Apellidos', required: true, max: 80 },
                { name: 'colegio_origen', label: 'Colegio de origen', max: 120 },
                { name: 'puntaje_saber11', label: 'Puntaje Saber 11', type: 'number', min: 0, max: 500, step: 1 },
                { name: 'prueba_admision', label: 'Puntaje de admisión', type: 'number', min: 0, max: 65535, step: 1 },
                { name: 'salud_fisica_mental', label: 'Salud física y mental', type: 'textarea' },
            ],
        },
        teachers: {
            label: 'Profesores', endpoint: '/teachers', key: 'id_profesor',
            columns: ['id_profesor', 'id_usuario', 'departamento', 'facultad'],
            fields: [
                { name: 'id_usuario', label: 'Usuario', relation: 'users', required: true },
                { name: 'departamento', label: 'Departamento', required: true, max: 80 },
                { name: 'facultad', label: 'Facultad', required: true, max: 80 },
            ],
        },
        'student-activities': {
            label: 'Actividades estudiantiles', endpoint: '/student-activities', key: 'id_actividad',
            columns: ['id_actividad', 'id_estudiante', 'id_periodo', 'tipo_actividad', 'nombre_actividad'],
            fields: [
                { name: 'id_estudiante', label: 'Estudiante', relation: 'students', required: true },
                { name: 'id_periodo', label: 'Periodo', relation: 'academic-periods', required: true },
                { name: 'tipo_actividad', label: 'Tipo de actividad', type: 'select', required: true, options: ['semillero', 'investigacion', 'monitoria', 'beca', 'publicacion', 'evento', 'cultural', 'deportiva', 'practica', 'otra'] },
                { name: 'nombre_actividad', label: 'Nombre de la actividad', required: true, max: 150 },
                { name: 'organizacion_empresa', label: 'Organización o empresa', max: 150 },
                { name: 'rol_o_actividades', label: 'Rol o actividades', max: 255 },
                { name: 'evaluacion', label: 'Evaluación', max: 255 },
            ],
        },
        'academic-requests': {
            label: 'Solicitudes académicas', endpoint: '/academic-requests', key: 'id_solicitud',
            columns: ['id_solicitud', 'id_estudiante', 'id_periodo', 'tipo_solicitud', 'fecha_radicacion', 'estado'],
            fields: [
                { name: 'id_estudiante', label: 'Estudiante', relation: 'students', required: true },
                { name: 'id_periodo', label: 'Periodo', relation: 'academic-periods', required: true },
                { name: 'tipo_solicitud', label: 'Tipo de solicitud', type: 'select', required: true, options: ['cancelacion', 'reintegro', 'homologacion', 'excusa', 'otra'] },
                { name: 'fecha_radicacion', label: 'Fecha de radicación', type: 'date', required: true },
                { name: 'estado', label: 'Estado', type: 'select', options: ['radicada', 'en_tramite', 'aprobada', 'rechazada'] },
            ],
        },
        tuitions: {
            label: 'Matrículas', endpoint: '/tuitions', key: 'id_matricula',
            columns: ['id_matricula', 'id_estudiante', 'id_asignatura', 'id_periodo', 'nota_final', 'estado_materia'],
            fields: [
                { name: 'id_estudiante', label: 'Estudiante', relation: 'students', required: true },
                { name: 'id_asignatura', label: 'Asignatura', relation: 'courses', required: true },
                { name: 'id_periodo', label: 'Periodo', relation: 'academic-periods', required: true },
                { name: 'nota_final', label: 'Nota final (0 a 5)', type: 'number', min: 0, max: 5, step: 0.1 },
                { name: 'estado_materia', label: 'Estado de la materia', type: 'select', options: ['inscrita', 'aprobada', 'reprobada', 'cancelada'] },
            ],
        },
        'early-warnings': {
            label: 'Alertas tempranas', endpoint: '/early-warnings', key: 'id_alerta',
            columns: ['id_alerta', 'id_estudiante', 'id_periodo', 'tipo_riesgo', 'nivel_riesgo', 'fecha_reporte', 'estado'],
            fields: [
                { name: 'id_estudiante', label: 'Estudiante', relation: 'students', required: true },
                { name: 'id_periodo', label: 'Periodo', relation: 'academic-periods', required: true },
                { name: 'tipo_riesgo', label: 'Tipo de riesgo', type: 'select', required: true, options: ['academico', 'inasistencia', 'socioeconomico', 'bienestar'] },
                { name: 'nivel_riesgo', label: 'Nivel de riesgo', type: 'select', required: true, options: ['bajo', 'medio', 'alto'] },
                { name: 'fecha_reporte', label: 'Fecha del reporte', type: 'date', required: true },
                { name: 'estado', label: 'Estado', type: 'select', options: ['abierta', 'en_seguimiento', 'cerrada'] },
            ],
        },
        'followup-interventions': {
            label: 'Intervenciones de seguimiento', endpoint: '/followup-interventions', key: 'id_intervencion',
            canEditOrDelete: false,
            columns: ['id_intervencion', 'id_alerta', 'id_profesor', 'fecha_sesion', 'tipo_intervencion'],
            fields: [
                { name: 'id_alerta', label: 'Alerta', relation: 'early-warnings', required: true },
                { name: 'id_profesor', label: 'Profesor', relation: 'teachers', required: true },
                { name: 'fecha_sesion', label: 'Fecha de sesión', type: 'date', required: true, maxDate: 'today' },
                { name: 'tipo_intervencion', label: 'Tipo de intervención', type: 'select', required: true, options: ['tutoria', 'llamada', 'citacion', 'remision', 'otra'] },
                { name: 'observaciones', label: 'Observaciones', type: 'textarea' },
                { name: 'compromisos', label: 'Compromisos', type: 'textarea' },
            ],
        },
        users: {
            label: 'Usuarios', endpoint: '/users', key: 'id_usuario',
            columns: ['id_usuario', 'correo_inst', 'rol', 'estado'],
            fields: [
                { name: 'correo_inst', label: 'Correo institucional', type: 'email', required: true, max: 120 },
                { name: 'contrasena', label: 'Contraseña', type: 'password', required: true, min: 8, help: 'Déjala vacía al editar para conservar la contraseña actual.' },
                { name: 'rol', label: 'Rol', type: 'select', required: true, options: ['estudiante', 'profesor', 'coordinador'] },
                { name: 'estado', label: 'Estado', type: 'select', options: ['activo', 'inactivo'] },
            ],
        },
    };

    const labels = {
        id_periodo: 'ID', id_asignatura: 'ID', id_estudiante: 'ID estudiante',
        id_usuario: 'ID usuario', id_profesor: 'ID profesor', id_actividad: 'ID',
        id_solicitud: 'ID', id_matricula: 'ID', id_alerta: 'ID alerta',
        id_intervencion: 'ID', codigo_asig: 'Código', nombre_asig: 'Asignatura',
        programa_academico: 'Programa', codigo_estud: 'Código estudiante',
        doc_identidad: 'Documento', nombres: 'Nombres', apellidos: 'Apellidos',
        nombre_periodo: 'Periodo', fecha_inicio: 'Inicio', fecha_fin: 'Fin',
        tipo_actividad: 'Actividad', nombre_actividad: 'Nombre', tipo_solicitud: 'Solicitud',
        fecha_radicacion: 'Radicación', tipo_riesgo: 'Riesgo', nivel_riesgo: 'Nivel',
        fecha_reporte: 'Reporte', fecha_sesion: 'Sesión', tipo_intervencion: 'Intervención',
        nota_final: 'Nota', estado_materia: 'Estado materia', correo_inst: 'Correo',
    };

    const relationLabels = {
        users: item => `${item.correo_inst} (#${item.id_usuario})`,
        students: item => `${item.nombres} ${item.apellidos} (#${item.id_estudiante})`,
        'academic-periods': item => `${item.nombre_periodo} (#${item.id_periodo})`,
        courses: item => `${item.codigo_asig} - ${item.nombre_asig}`,
        teachers: item => `${item.usuario?.correo_inst ?? 'Profesor'} (#${item.id_profesor})`,
        'early-warnings': item => `Alerta #${item.id_alerta} - ${item.tipo_riesgo} (${item.nivel_riesgo})`,
    };
    const dateFields = new Set(['fecha_inicio', 'fecha_fin', 'fecha_radicacion', 'fecha_reporte', 'fecha_sesion']);
    const today = @json(now()->toDateString());

    const resourceSelect = document.getElementById('resource-select');
    const head = document.getElementById('table-head');
    const body = document.getElementById('records-body');
    const form = document.getElementById('record-form');
    const fieldsContainer = document.getElementById('form-fields');
    const status = document.getElementById('status-message');
    const csrf = document.querySelector('meta[name="csrf-token"]').content;
    const cache = new Map();
    let activeResource = null;
    let activePage = 1;
    let lastPage = 1;
    let editingId = null;

    Object.entries(resources).forEach(([key, resource]) => {
        const option = document.createElement('option');
        option.value = key;
        option.textContent = resource.label;
        resourceSelect.append(option);
    });

    function showMessage(message, type = 'success') {
        status.className = `alert alert-${type}`;
        status.textContent = message;
        status.classList.remove('d-none');
    }

    function hideMessage() {
        status.classList.add('d-none');
    }

    function clearFieldErrors() {
        form.querySelectorAll('.is-invalid').forEach(input => {
            input.classList.remove('is-invalid');
            input.removeAttribute('aria-invalid');
        });
        form.querySelectorAll('[data-field-error]').forEach(message => {
            message.textContent = '';
        });
    }

    function showFieldErrors(errors) {
        clearFieldErrors();

        Object.entries(errors).forEach(([name, messages]) => {
            const input = form.elements.namedItem(name);
            const errorElement = document.getElementById(`error-${name}`);
            if (!input || !errorElement) return;

            input.classList.add('is-invalid');
            input.setAttribute('aria-invalid', 'true');
            errorElement.textContent = Array.isArray(messages) ? messages.join(' ') : messages;
        });
    }

    function getFieldLabel(name) {
        return activeResource.fields.find(field => field.name === name)?.label || name;
    }

    function formatErrors(errors) {
        return Object.entries(errors)
            .map(([name, messages]) => `${getFieldLabel(name)}: ${Array.isArray(messages) ? messages.join(' ') : messages}`)
            .join(' ');
    }

    function validateForm() {
        const errors = {};

        activeResource.fields.forEach(field => {
            const input = form.elements.namedItem(field.name);
            const value = input.value.trim();
            const required = field.required && !(editingId !== null && field.name === 'contrasena');

            if (required && !value) {
                errors[field.name] = [`El campo ${field.label.toLowerCase()} es obligatorio.`];
                return;
            }
            if (!value) return;

            if (field.type === 'email' && input.validity.typeMismatch) {
                errors[field.name] = ['Ingresa un correo electrónico válido.'];
            } else if (field.max && value.length > field.max) {
                errors[field.name] = [`Usa máximo ${field.max} caracteres.`];
            } else if (field.type === 'number' && field.min !== undefined && Number(value) < field.min) {
                errors[field.name] = [`El valor mínimo es ${field.min}.`];
            } else if (field.type === 'number' && field.max !== undefined && Number(value) > field.max) {
                errors[field.name] = [`El valor máximo es ${field.max}.`];
            } else if (field.type === 'number' && field.step && input.validity.stepMismatch) {
                errors[field.name] = [`Ingresa un valor en incrementos de ${field.step}.`];
            } else if (field.type === 'date' && field.maxDate === 'today' && value > today) {
                errors[field.name] = ['La fecha no puede ser posterior a hoy.'];
            } else if (field.name === 'contrasena' && value.length < (field.min || 0)) {
                errors[field.name] = [`La contraseña debe tener al menos ${field.min} caracteres.`];
            }
        });

        const startDate = form.elements.namedItem('fecha_inicio');
        const endDate = form.elements.namedItem('fecha_fin');
        if (startDate && endDate && startDate.value && endDate.value && endDate.value < startDate.value) {
            errors.fecha_fin = ['La fecha de finalización debe ser igual o posterior a la fecha de inicio.'];
        }

        return errors;
    }

    async function request(url, options = {}) {
        const response = await fetch(url, {
            ...options,
            headers: {
                Accept: 'application/json',
                'X-CSRF-TOKEN': csrf,
                ...(options.body ? { 'Content-Type': 'application/json' } : {}),
                ...options.headers,
            },
        });
        const contentType = response.headers.get('content-type') || '';
        const data = contentType.includes('application/json') ? await response.json() : null;

        if (response.status === 401 || response.redirected) {
            window.location.assign('{{ route('login') }}');
            throw new Error('La sesión expiró. Inicia sesión nuevamente.');
        }
        if (!response.ok) {
            const error = new Error(data?.message || `Error HTTP ${response.status}`);
            error.validationErrors = data?.errors || null;
            throw error;
        }

        return data;
    }

    function getRelationValue(item, field) {
        if (field.relation === 'users') return item.id_usuario;
        if (field.relation === 'students') return item.id_estudiante;
        if (field.relation === 'academic-periods') return item.id_periodo;
        if (field.relation === 'courses') return item.id_asignatura;
        if (field.relation === 'teachers') return item.id_profesor;
        return item.id_alerta;
    }

    async function loadRelationOptions(relation) {
        if (cache.has(relation)) return cache.get(relation);
        const promise = (async () => {
            const endpoint = resources[relation].endpoint;
            let page = 1;
            let items = [];
            let hasMore = true;
            while (hasMore) {
                const result = await request(`${endpoint}?page=${page}`);
                items = items.concat(result.data || []);
                hasMore = Boolean(result.next_page_url);
                page += 1;
            }
            return items;
        })();
        cache.set(relation, promise);
        return promise;
    }

    function renderInput(field) {
        const wrapper = document.createElement('div');
        wrapper.className = 'col-12 col-md-6';
        const label = document.createElement('label');
        label.className = 'form-label';
        label.htmlFor = `field-${field.name}`;
        const required = field.required && !(editingId !== null && field.name === 'contrasena');
        label.textContent = field.label + (required ? ' *' : '');

        let input;
        if (field.type === 'textarea') {
            input = document.createElement('textarea');
            input.rows = 3;
        } else {
            input = document.createElement('input');
            input.type = field.type || 'text';
        }
        input.className = 'form-control';
        input.id = `field-${field.name}`;
        input.name = field.name;
        input.required = required;
        if (field.max) input.maxLength = field.max;
        if (field.min !== undefined && field.type !== 'number') input.minLength = field.min;
        if (field.max !== undefined && field.type === 'number') input.max = field.max;
        if (field.min !== undefined && field.type === 'number') input.min = field.min;
        if (field.maxDate === 'today') input.max = today;
        if (field.step) input.step = field.step;
        if (field.type === 'password' && editingId !== null) {
            input.placeholder = 'Deja vacío para conservar la contraseña';
        }
        wrapper.append(label, input);

        if (field.type === 'select' || field.relation) {
            input = document.createElement('select');
            input.className = 'form-select';
            input.id = `field-${field.name}`;
            input.name = field.name;
            input.required = required;
            const placeholder = document.createElement('option');
            placeholder.value = '';
            placeholder.textContent = field.required ? 'Selecciona una opción' : 'Sin especificar';
            input.append(placeholder);

            if (field.options) {
                field.options.forEach(value => {
                    const option = document.createElement('option');
                    option.value = value;
                    option.textContent = value;
                    input.append(option);
                });
            }
            wrapper.replaceChild(input, wrapper.querySelector('input, textarea'));
        }

        const hint = document.createElement('div');
        hint.className = 'form-text';
        hint.id = `hint-${field.name}`;
        const requirements = [];
        if (required) requirements.push('Obligatorio.');
        else if (field.name !== 'contrasena' || editingId !== null) requirements.push('Opcional.');
        if (field.type === 'date') requirements.push('Ingresa solo la fecha; no se necesita hora.');
        if (field.relation) requirements.push('Selecciona un registro existente.');
        if (field.options) requirements.push(`Opciones: ${field.options.join(', ')}.`);
        if (field.max) requirements.push(`Máximo ${field.max} caracteres.`);
        if (field.type === 'number' && field.min !== undefined && field.max !== undefined) {
            requirements.push(`Valor entre ${field.min} y ${field.max}${field.step ? `, en incrementos de ${field.step}` : ''}.`);
        }
        if (field.name === 'contrasena') requirements.push(`Mínimo ${field.min} caracteres.`);
        if (field.help) requirements.push(field.help);
        hint.textContent = requirements.join(' ');
        input.setAttribute('aria-describedby', hint.id);
        wrapper.append(hint);

        const errorMessage = document.createElement('div');
        errorMessage.className = 'invalid-feedback';
        errorMessage.id = `error-${field.name}`;
        errorMessage.dataset.fieldError = '';
        input.setAttribute('aria-describedby', `${hint.id} ${errorMessage.id}`);
        wrapper.append(errorMessage);

        if (field.help && !requirements.includes(field.help)) {
            const help = document.createElement('div');
            help.className = 'form-text';
            help.textContent = field.help;
            wrapper.append(help);
        }
        return wrapper;
    }

    async function renderForm() {
        const resource = activeResource;
        const formEditingId = editingId;
        const wrappers = resource.fields.map(renderInput);
        await Promise.all(resource.fields.map(async (field, index) => {
            if (!field.relation) return;
            try {
                const items = await loadRelationOptions(field.relation);
                const input = wrappers[index].querySelector('select');
                items.forEach(item => {
                    const option = document.createElement('option');
                    option.value = getRelationValue(item, field);
                    option.textContent = relationLabels[field.relation](item);
                    input.append(option);
                });
            } catch (error) {
                showMessage(error.message, 'danger');
            }
        }));
        if (resource !== activeResource || formEditingId !== editingId) return;
        fieldsContainer.replaceChildren(...wrappers);
        document.getElementById('editor-title').textContent = formEditingId === null
            ? `Crear ${resource.label.toLowerCase()}`
            : `Editar ${resource.label.toLowerCase()} #${formEditingId}`;
        document.getElementById('save-button').textContent = formEditingId === null ? 'Guardar' : 'Guardar cambios';
        document.getElementById('cancel-edit-button').classList.toggle('d-none', formEditingId === null);
    }

    function renderTableHeader() {
        head.replaceChildren();
        activeResource.columns.forEach(column => {
            const cell = document.createElement('th');
            cell.scope = 'col';
            cell.textContent = labels[column] || column.replaceAll('_', ' ');
            head.append(cell);
        });
        const actions = document.createElement('th');
        actions.scope = 'col';
        actions.textContent = 'Acciones';
        head.append(actions);
        document.getElementById('records-title').textContent = activeResource.label;
    }

    function renderTable(items) {
        body.replaceChildren();
        if (!items.length) {
            const row = body.insertRow();
            const cell = row.insertCell();
            cell.colSpan = activeResource.columns.length + 1;
            cell.className = 'text-center text-secondary py-4';
            cell.textContent = 'No hay registros para este recurso.';
            return;
        }

        items.forEach(item => {
            const row = body.insertRow();
            activeResource.columns.forEach(column => {
                const cell = row.insertCell();
                const value = item[column];
                cell.textContent = value === null || value === undefined ? '—'
                    : dateFields.has(column) ? String(value).slice(0, 10)
                    : typeof value === 'object' ? JSON.stringify(value) : String(value);
            });

            const actions = row.insertCell();
            actions.className = 'text-nowrap';
            const detailButton = document.createElement('button');
            detailButton.type = 'button';
            detailButton.className = 'btn btn-sm btn-outline-secondary me-1';
            detailButton.textContent = 'Ver';
            detailButton.addEventListener('click', () => showDetails(item[activeResource.key]));
            actions.append(detailButton);

            if (activeResource.canEditOrDelete !== false) {
                const editButton = document.createElement('button');
                editButton.type = 'button';
                editButton.className = 'btn btn-sm btn-outline-primary me-1';
                editButton.textContent = 'Editar';
                editButton.addEventListener('click', () => startEdit(item[activeResource.key]));
                actions.append(editButton);

                const deleteButton = document.createElement('button');
                deleteButton.type = 'button';
                deleteButton.className = 'btn btn-sm btn-outline-danger';
                deleteButton.textContent = 'Eliminar';
                deleteButton.addEventListener('click', () => deleteRecord(item[activeResource.key]));
                actions.append(deleteButton);
            }
        });
    }

    async function loadRecords(page = 1) {
        body.innerHTML = '<tr><td class="text-center text-secondary py-4">Cargando...</td></tr>';
        try {
            const result = await request(`${activeResource.endpoint}?page=${page}`);
            activePage = result.current_page || page;
            lastPage = result.last_page || 1;
            renderTable(result.data || []);
            document.getElementById('records-count').textContent = `${result.total ?? result.data?.length ?? 0} registros`;
            document.getElementById('page-label').textContent = `Página ${activePage} de ${lastPage}`;
            document.getElementById('previous-button').disabled = activePage <= 1;
            document.getElementById('next-button').disabled = activePage >= lastPage;
        } catch (error) {
            renderTable([]);
            showMessage(error.message, 'danger');
        }
    }

    async function showDetails(id) {
        try {
            const item = await request(`${activeResource.endpoint}/${id}`);
            document.getElementById('details-content').textContent = JSON.stringify(formatDetailDates(item), null, 2);
            document.getElementById('details-dialog').showModal();
        } catch (error) {
            showMessage(error.message, 'danger');
        }
    }

    function formatDetailDates(value) {
        if (Array.isArray(value)) return value.map(formatDetailDates);
        if (value && typeof value === 'object') {
            return Object.fromEntries(Object.entries(value).map(([key, item]) => [
                key,
                dateFields.has(key) && typeof item === 'string' ? item.slice(0, 10) : formatDetailDates(item),
            ]));
        }
        return value;
    }

    async function startEdit(id) {
        hideMessage();
        const resource = activeResource;
        try {
            const item = await request(`${resource.endpoint}/${id}`);
            if (resource !== activeResource) return;
            editingId = id;
            await renderForm();
            if (resource !== activeResource || editingId !== id) return;
            resource.fields.forEach(field => {
                const input = form.elements.namedItem(field.name);
                if (input && field.name !== 'contrasena') {
                    const value = item[field.name] ?? '';
                    input.value = field.type === 'date' && value ? String(value).slice(0, 10) : value;
                }
            });
            document.getElementById('editor-card').scrollIntoView({ behavior: 'smooth', block: 'start' });
        } catch (error) {
            showMessage(error.message, 'danger');
        }
    }

    async function deleteRecord(id) {
        if (!window.confirm(`¿Eliminar el registro #${id}?`)) return;
        try {
            await request(`${activeResource.endpoint}/${id}`, { method: 'DELETE' });
            cache.clear();
            showMessage('Registro eliminado correctamente.');
            await loadRecords(activePage);
        } catch (error) {
            showMessage(error.message, 'danger');
        }
    }

    resourceSelect.addEventListener('change', async () => {
        activeResource = resources[resourceSelect.value];
        editingId = null;
        hideMessage();
        renderTableHeader();
        await renderForm();
        loadRecords();
    });

    document.getElementById('new-button').addEventListener('click', async () => {
        editingId = null;
        hideMessage();
        form.reset();
        await renderForm();
        document.getElementById('editor-card').scrollIntoView({ behavior: 'smooth', block: 'start' });
    });

    document.getElementById('cancel-edit-button').addEventListener('click', async () => {
        editingId = null;
        hideMessage();
        form.reset();
        await renderForm();
    });

    document.getElementById('refresh-button').addEventListener('click', async () => {
        cache.clear();
        hideMessage();
        await renderForm();
        loadRecords(activePage);
    });

    document.getElementById('previous-button').addEventListener('click', () => {
        hideMessage();
        loadRecords(activePage - 1);
    });
    document.getElementById('next-button').addEventListener('click', () => {
        hideMessage();
        loadRecords(activePage + 1);
    });
    document.getElementById('close-details-button').addEventListener('click', () => document.getElementById('details-dialog').close());

    form.addEventListener('submit', async event => {
        event.preventDefault();
        hideMessage();
        clearFieldErrors();
        const clientErrors = validateForm();
        if (Object.keys(clientErrors).length) {
            showFieldErrors(clientErrors);
            showMessage(formatErrors(clientErrors), 'danger');
            form.querySelector('.is-invalid')?.focus();
            return;
        }

        const payload = Object.fromEntries(new FormData(form).entries());
        activeResource.fields.forEach(field => {
            if (field.type === 'number' && payload[field.name] !== '') {
                payload[field.name] = Number(payload[field.name]);
            }
        });
        if (editingId !== null && !payload.contrasena) delete payload.contrasena;
        const isEditing = editingId !== null;
        const url = isEditing ? `${activeResource.endpoint}/${editingId}` : activeResource.endpoint;

        try {
            await request(url, {
                method: isEditing ? 'PATCH' : 'POST',
                body: JSON.stringify(payload),
            });
            cache.clear();
            showMessage(isEditing ? 'Registro actualizado correctamente.' : 'Registro creado correctamente.');
            editingId = null;
            form.reset();
            await renderForm();
            await loadRecords(activePage);
        } catch (error) {
            if (error.validationErrors) {
                showFieldErrors(error.validationErrors);
                showMessage(formatErrors(error.validationErrors), 'danger');
                form.querySelector('.is-invalid')?.focus();
            } else {
                showMessage(error.message, 'danger');
            }
        }
    });

    (async () => {
        const preferredResource = new URLSearchParams(window.location.search).get('resource');
        if (preferredResource && resources[preferredResource]) resourceSelect.value = preferredResource;
        activeResource = resources[resourceSelect.value];
        renderTableHeader();
        await renderForm();
        await loadRecords();
    })();
})();
</script>
@endsection
