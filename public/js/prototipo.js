(() => {
    'use strict';
    const root = document.getElementById('prototipo');
    if (!root) return;
    const data = JSON.parse(document.getElementById('datos-prototipo').textContent);
    const byId = id => document.getElementById(id);
    const form = byId('formulario'), filters = byId('filtros');
    const keys = Object.keys(data.campos), readonly = root.dataset.consulta === '1';
    const annul = root.dataset.anulable === '1';
    let page = 1, editing = null, pending = null, nextId = 8;
    const vehicles = [
        { codigo: 'C-29', placa: '3992NII', tipo_vehiculo: 'Camioneta', unidad: 'Administración central', apertura: 'Administración central' },
        { codigo: 'C-30', placa: '4821ABC', tipo_vehiculo: 'Camión', unidad: 'Obras públicas', apertura: 'Mantenimiento vial' },
        { codigo: 'M-12', placa: '2536XYZ', tipo_vehiculo: 'Motocicleta', unidad: 'Policía', apertura: 'Seguridad ciudadana' }
    ];
    let rows = Array.from({ length: 7 }, (_, i) => {
        const row = { id: i + 1, estado: 'Activo', fecha: `2026-06-${String(i + 10).padStart(2, '0')}` };
        Object.entries(data.opciones).forEach(([key, values]) => { row[key] = values[i % values.length]; });
        keys.forEach(key => {
            const type = data.campos[key][1];
            row[key] = data.opciones[type]?.[i % data.opciones[type].length] ?? (type === 'number' ? String(30 + i * 5) : type === 'date' ? row.fecha : `${data.campos[key][0]} de ejemplo ${i + 1}`);
        });
        if (keys.includes('correo')) row.correo = `auxiliar${i + 1}@example.test`;
        if (keys.includes('codigo')) row.codigo = `E-${String(i + 1).padStart(3, '0')}`;
        if (keys.includes('nombre') && data.ejemplos.length) row.nombre = data.ejemplos[i % data.ejemplos.length];
        if (keys.includes('placa')) Object.assign(row, vehicles[i % 3], { marca: 'Marca de ejemplo', industria: 'Industria de ejemplo', color: ['Blanco', 'Azul', 'Gris'][i % 3] });
        if (data.policia) { row.vehiculo = data.opciones.vehiculo[2]; row.apertura = 'Seguridad ciudadana'; }
        if (annul) row.estado = 'Registrado';
        return row;
    });
    const message = text => { byId('mensaje').textContent = text; byId('mensaje').hidden = false; };
    const normalize = value => String(value).normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
    function filtered() {
        const query = normalize(byId('buscar').value.trim());
        const criteria = [...new FormData(filters)];
        return rows.filter(row => keys.some(key => normalize(row[key]).includes(query)) && criteria.every(([key, value]) => !value || (key === 'desde' ? row.fecha >= value : key === 'hasta' ? row.fecha <= value : row[key] === value)));
    }
    function button(label, action, id, style = 'outline-secondary') {
        const element = document.createElement('button');
        element.type = 'button'; element.className = `btn btn-sm btn-${style}`;
        element.textContent = label; element.dataset.action = action; element.dataset.id = id;
        return element;
    }
    function render() {
        const result = filtered(), pages = Math.max(1, Math.ceil(result.length / 5));
        page = Math.min(page, pages);
        byId('filas').replaceChildren();
        result.slice((page - 1) * 5, page * 5).forEach(row => {
            const tr = document.createElement('tr');
            keys.forEach(key => { const td = document.createElement('td'); td.textContent = row[key]; tr.append(td); });
            const status = document.createElement('td'), badge = document.createElement('span');
            badge.className = `badge ${['Inactivo', 'Anulado'].includes(row.estado) ? 'text-bg-secondary' : 'text-bg-success'}`;
            badge.textContent = row.estado; status.append(badge); tr.append(status);
            const actions = document.createElement('td'), group = document.createElement('div'); group.className = 'd-flex gap-2';
            group.append(button('Ver', 'ver', row.id));
            if (!readonly) {
                group.append(button('Editar', 'editar', row.id, 'primary'));
                if (row.estado !== 'Anulado') group.append(button(annul ? 'Anular' : row.estado === 'Activo' ? 'Desactivar' : 'Activar', 'estado', row.id));
            }
            actions.append(group); tr.append(actions); byId('filas').append(tr);
        });
        if (!result.length) { const tr = document.createElement('tr'), td = document.createElement('td'); td.colSpan = keys.length + 2; td.className = 'text-center py-4'; td.textContent = 'No se encontraron registros. Pruebe otros filtros.'; tr.append(td); byId('filas').append(tr); }
        byId('resumen').textContent = `${result.length} registros · Página ${page} de ${pages}`;
        byId('anterior').disabled = page === 1; byId('siguiente').disabled = page === pages;
    }
    function vehicleInfo() {
        const box = byId('datos-vehiculo'); if (!box) return;
        const index = data.opciones.vehiculo.indexOf(form.elements.vehiculo.value), v = vehicles[index];
        box.textContent = v ? `${v.codigo} · ${v.placa} · ${v.tipo_vehiculo} · Unidad: ${v.unidad} · Apertura: ${v.apertura}` : 'Seleccione un vehículo para ver sus datos relacionados.';
    }
    function openEditor(row = null) {
        editing = row?.id ?? null; form.reset();
        [...form.elements].forEach(el => el.setCustomValidity?.(''));
        keys.forEach(key => { form.elements[key].value = row?.[key] ?? ''; });
        if (!row && data.policia) form.elements.vehiculo.value = data.opciones.vehiculo[2];
        byId('titulo-editor').textContent = row ? 'Editar registro' : 'Nuevo registro';
        byId('editor').hidden = false; vehicleInfo(); form.elements[keys[0]].focus();
    }
    root.querySelectorAll('[data-nuevo]').forEach(el => el.addEventListener('click', () => openEditor()));
    byId('cancelar').addEventListener('click', () => { byId('editor').hidden = true; root.querySelector('[data-nuevo]')?.focus(); });
    form.addEventListener('change', vehicleInfo);
    form.addEventListener('input', event => event.target.setCustomValidity(''));
    form.addEventListener('invalid', event => { event.target.setCustomValidity('Complete este campo con un valor válido. Los números deben ser mayores que cero.'); }, true);
    form.addEventListener('submit', event => {
        event.preventDefault();
        const values = Object.fromEntries([...new FormData(form)].map(([key, value]) => [key, value.trim()]));
        if (Object.values(values).some(value => !value)) { message('Complete los campos obligatorios sin usar solo espacios.'); return; }
        if (editing !== null) rows = rows.map(row => row.id === editing ? { ...row, ...values } : row);
        else rows.unshift({ id: nextId++, ...values, estado: annul ? 'Registrado' : 'Activo' });
        byId('editor').hidden = true; filters.reset(); page = 1; render(); message('Registro guardado en la demostración. No se ha escrito en la base de datos.'); root.querySelector('[data-nuevo]')?.focus();
    });
    byId('filas').addEventListener('click', event => {
        const target = event.target.closest('button[data-action]'); if (!target) return;
        const row = rows.find(item => item.id === Number(target.dataset.id)); if (!row) return;
        if (target.dataset.action === 'editar') openEditor(row);
        if (target.dataset.action === 'estado') {
            pending = row.id; byId('texto-confirmacion').textContent = `¿Desea ${annul ? 'anular' : row.estado === 'Activo' ? 'desactivar' : 'activar'} el registro ${row[keys[0]]}? El cambio solo afecta a esta demostración.`; byId('confirmacion').showModal();
        }
        if (target.dataset.action === 'ver') {
            byId('contenido-detalle').replaceChildren();
            [...keys, 'estado'].forEach(key => { const dt = document.createElement('dt'), dd = document.createElement('dd'); dt.textContent = data.campos[key]?.[0] ?? 'Estado'; dd.textContent = row[key]; byId('contenido-detalle').append(dt, dd); });
            byId('detalle').showModal();
        }
    });
    byId('confirmar').addEventListener('click', () => { const row = rows.find(item => item.id === pending); if (row) row.estado = annul ? 'Anulado' : row.estado === 'Activo' ? 'Inactivo' : 'Activo'; byId('confirmacion').close(); render(); message('Estado actualizado en la demostración.'); });
    byId('cerrar-confirmacion').addEventListener('click', () => byId('confirmacion').close());
    byId('cerrar-detalle').addEventListener('click', () => byId('detalle').close());
    filters.addEventListener('submit', event => { event.preventDefault(); const from = filters.elements.desde?.value, to = filters.elements.hasta?.value; if (from && to && from > to) { message('La fecha inicial no puede ser posterior a la fecha final.'); return; } page = 1; render(); });
    filters.addEventListener('reset', () => { setTimeout(() => { page = 1; render(); }, 0); });
    byId('buscar').addEventListener('input', () => { page = 1; render(); });
    byId('anterior').addEventListener('click', () => { page--; render(); });
    byId('siguiente').addEventListener('click', () => { page++; render(); });
    root.querySelectorAll('[data-salida]').forEach(el => el.addEventListener('click', () => message(`La salida de ${el.dataset.salida} está prevista para la fase de reportes. Este botón es una demostración visual.`)));
    render();
})();
