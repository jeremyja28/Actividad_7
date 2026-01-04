<?php
require_once '../security.php';
require_once '../connect.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Venta | InventoryOS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #4f46e5;
            --bg-color: #f3f4f6;
        }
        body { background-color: var(--bg-color); }
        .card { border: none; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .btn-primary { background-color: var(--primary-color); border-color: var(--primary-color); }
        .btn-primary:hover { background-color: #4338ca; border-color: #4338ca; }
        .text-primary { color: var(--primary-color) !important; }
        
        /* Toast Container */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1055;
        }
        
        /* Autocomplete styles */
        .autocomplete-items {
            position: absolute;
            border: 1px solid #d4d4d4;
            border-bottom: none;
            border-top: none;
            z-index: 99;
            top: 100%;
            left: 0;
            right: 0;
            background-color: #fff;
            max-height: 200px;
            overflow-y: auto;
            border-radius: 0 0 10px 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .autocomplete-item {
            padding: 10px;
            cursor: pointer;
            border-bottom: 1px solid #d4d4d4;
        }
        .autocomplete-item:hover { background-color: #e9e9e9; }
        .autocomplete-item strong { color: var(--primary-color); }
    </style>
</head>
<body>

<!-- Toast Container -->
<div class="toast-container" id="toastContainer"></div>

<div class="container-fluid py-4">
    <div class="row">
        <!-- Sección Izquierda: Productos -->
        <div class="col-lg-8">
            <div class="card mb-4" style="min-height: 85vh;">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="fw-bold text-primary"><i class="bi bi-cart3"></i> Punto de Venta</h4>
                        <a href="../principal.php" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Volver</a>
                    </div>
                    
                    <!-- Buscador de Productos -->
                    <div class="mt-4 position-relative">
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
                            <input type="text" id="buscarProducto" class="form-control bg-light border-start-0" placeholder="Buscar producto por nombre o SKU..." autocomplete="off">
                        </div>
                        <div id="listaProductos" class="autocomplete-items d-none"></div>
                    </div>
                </div>

                <div class="card-body px-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Producto</th>
                                    <th class="text-center" width="150">Cantidad</th>
                                    <th class="text-end" width="150">Precio</th>
                                    <th class="text-end" width="150">Total</th>
                                    <th class="text-center" width="50"></th>
                                </tr>
                            </thead>
                            <tbody id="tablaProductos">
                                <!-- Filas dinámicas -->
                            </tbody>
                        </table>
                    </div>
                    
                    <div id="emptyState" class="text-center py-5 text-muted">
                        <i class="bi bi-basket display-1 opacity-25"></i>
                        <p class="mt-3">Escanea o busca productos para comenzar la venta</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección Derecha: Cliente y Totales -->
        <div class="col-lg-4">
            <!-- Datos del Cliente -->
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title fw-bold mb-3">Datos del Cliente</h5>
                    
                    <div class="position-relative mb-3">
                        <label class="form-label small text-muted">Buscar Cliente (Cédula)</label>
                        <div class="input-group">
                            <input type="text" id="buscarCliente" class="form-control" placeholder="Ingrese cédula..." autocomplete="off" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            <button class="btn btn-outline-secondary" type="button" id="btnBuscarCliente" title="Buscar">
                                <i class="bi bi-search"></i>
                            </button>
                            <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#modalNuevoCliente" title="Nuevo Cliente">
                                <i class="bi bi-person-plus-fill"></i>
                            </button>
                        </div>
                        <div id="listaClientes" class="autocomplete-items d-none"></div>
                        <div id="msgClienteNoEncontrado" class="text-danger small mt-1 d-none">
                            <i class="bi bi-exclamation-circle"></i> Cliente no registrado. 
                            <a href="#" data-bs-toggle="modal" data-bs-target="#modalNuevoCliente">Registrar ahora</a>
                        </div>
                    </div>

                    <input type="hidden" id="clienteId">
                    
                    <div class="mb-2">
                        <input type="text" id="clienteNombre" class="form-control form-control-sm mb-2" placeholder="Nombre del Cliente" readonly>
                        <input type="text" id="clienteDireccion" class="form-control form-control-sm mb-2" placeholder="Dirección" readonly>
                        <input type="text" id="clienteCorreo" class="form-control form-control-sm" placeholder="Correo Electrónico" readonly>
                    </div>
                </div>
            </div>

            <!-- Totales -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title fw-bold mb-4">Resumen de Pago</h5>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Subtotal</span>
                        <span class="fw-bold" id="lblSubtotal">$0.00</span>
                    </div>
                    
                    <div class="mb-2">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-muted">Descuento</span>
                            <div class="input-group input-group-sm w-50">
                                <select class="form-select" id="tipoDescuento">
                                    <option value="fijo">$</option>
                                    <option value="porcentaje">%</option>
                                </select>
                                <input type="number" class="form-control text-end" id="valDescuento" value="0" min="0">
                            </div>
                        </div>
                        <div class="text-end text-danger small" id="lblDescuento">-$0.00</div>
                    </div>

                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">IVA (15%)</span>
                        <span class="fw-bold" id="lblImpuesto">$0.00</span>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <span class="h5 mb-0">Total a Pagar</span>
                        <span class="h3 mb-0 text-primary fw-bold" id="lblTotal">$0.00</span>
                    </div>

                    <div class="d-grid gap-2">
                        <button class="btn btn-primary btn-lg py-3 fw-bold" id="btnProcesar" onclick="procesarVenta()">
                            <i class="bi bi-check-circle-fill me-2"></i> CONFIRMAR VENTA
                        </button>
                        <button class="btn btn-light text-danger" onclick="limpiarVenta()">
                            Cancelar Venta
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nuevo Cliente -->
<div class="modal fade" id="modalNuevoCliente" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Nuevo Cliente</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formNuevoCliente">
                    <div class="mb-3">
                        <label class="form-label">Cédula *</label>
                        <input type="text" name="cedula" class="form-control" required maxlength="10">
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label">Nombres *</label>
                            <input type="text" name="nombres" class="form-control" required>
                        </div>
                        <div class="col">
                            <label class="form-label">Apellidos *</label>
                            <input type="text" name="apellidos" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Correo</label>
                        <input type="email" name="correo" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Teléfono</label>
                        <input type="text" name="telefono" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Dirección</label>
                        <textarea name="direccion" class="form-control" rows="2"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="guardarCliente()">Guardar Cliente</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    let carrito = [];
    const IVA_RATE = 0.15;

    // Función para mostrar notificaciones Toast
    function showToast(message, type = 'success') {
        const container = document.getElementById('toastContainer');
        const id = 'toast-' + Date.now();
        const bgClass = type === 'success' ? 'text-bg-success' : (type === 'danger' ? 'text-bg-danger' : 'text-bg-warning');
        const icon = type === 'success' ? 'bi-check-circle-fill' : (type === 'danger' ? 'bi-x-circle-fill' : 'bi-exclamation-triangle-fill');
        
        const html = `
            <div id="${id}" class="toast align-items-center ${bgClass} border-0 mb-2" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi ${icon} me-2"></i> ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;
        
        container.insertAdjacentHTML('beforeend', html);
        const toastEl = document.getElementById(id);
        const toast = new bootstrap.Toast(toastEl, { delay: 3000 });
        toast.show();
        
        toastEl.addEventListener('hidden.bs.toast', () => {
            toastEl.remove();
        });
    }

    // --- Lógica de Productos ---
    const inputProducto = document.getElementById('buscarProducto');
    const listaProductos = document.getElementById('listaProductos');

    inputProducto.addEventListener('input', function() {
        const val = this.value;
        if (val.length < 2) {
            listaProductos.classList.add('d-none');
            return;
        }

        fetch(`ajax_buscar_producto.php?q=${val}`)
            .then(response => response.json())
            .then(data => {
                listaProductos.innerHTML = '';
                if (data.length > 0) {
                    listaProductos.classList.remove('d-none');
                    data.forEach(item => {
                        const div = document.createElement('div');
                        div.className = 'autocomplete-item';
                        div.innerHTML = `<strong>${item.nombre_completo}</strong><br><small>SKU: ${item.sku} | Precio: $${item.precio} | Stock: ${item.stock}</small>`;
                        div.onclick = () => agregarProducto(item);
                        listaProductos.appendChild(div);
                    });
                } else {
                    listaProductos.classList.add('d-none');
                }
            });
    });

    function agregarProducto(producto) {
        const existente = carrito.find(p => p.id === producto.id);
        
        if (existente) {
            if (existente.cantidad + 1 > producto.stock) {
                showToast('No hay suficiente stock disponible.', 'warning');
                return;
            }
            existente.cantidad++;
        } else {
            carrito.push({
                id: producto.id,
                nombre: producto.nombre_completo,
                sku: producto.sku,
                precio: parseFloat(producto.precio),
                stock: parseInt(producto.stock),
                cantidad: 1
            });
        }
        
        inputProducto.value = '';
        listaProductos.classList.add('d-none');
        renderizarTabla();
    }

    function renderizarTabla() {
        const tbody = document.getElementById('tablaProductos');
        const emptyState = document.getElementById('emptyState');
        
        tbody.innerHTML = '';
        
        if (carrito.length === 0) {
            emptyState.classList.remove('d-none');
        } else {
            emptyState.classList.add('d-none');
            
            carrito.forEach((prod, index) => {
                const total = prod.cantidad * prod.precio;
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="ps-4">
                        <div class="fw-bold">${prod.nombre}</div>
                        <div class="small text-muted">${prod.sku}</div>
                    </td>
                    <td class="text-center">
                        <div class="input-group input-group-sm justify-content-center" style="width: 100px; margin: 0 auto;">
                            <button class="btn btn-outline-secondary" onclick="cambiarCantidad(${index}, -1)">-</button>
                            <input type="text" class="form-control text-center" value="${prod.cantidad}" readonly>
                            <button class="btn btn-outline-secondary" onclick="cambiarCantidad(${index}, 1)">+</button>
                        </div>
                    </td>
                    <td class="text-end">$${prod.precio.toFixed(2)}</td>
                    <td class="text-end fw-bold">$${total.toFixed(2)}</td>
                    <td class="text-center">
                        <button class="btn btn-link text-danger p-0" onclick="eliminarProducto(${index})"><i class="bi bi-trash"></i></button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }
        calcularTotales();
    }

    function cambiarCantidad(index, delta) {
        const prod = carrito[index];
        const nuevaCant = prod.cantidad + delta;
        
        if (nuevaCant > prod.stock) {
            showToast('Stock insuficiente.', 'warning');
            return;
        }
        
        if (nuevaCant <= 0) {
            eliminarProducto(index);
        } else {
            prod.cantidad = nuevaCant;
            renderizarTabla();
        }
    }

    function eliminarProducto(index) {
        carrito.splice(index, 1);
        renderizarTabla();
    }

    // --- Lógica de Totales ---
    const tipoDescuento = document.getElementById('tipoDescuento');
    const valDescuento = document.getElementById('valDescuento');

    // Validaciones en tiempo real para el descuento
    valDescuento.addEventListener('input', function() {
        // 1. No permitir negativos
        if (this.value < 0) this.value = 0;
        
        // 2. No permitir más de 100 si es porcentaje
        if (tipoDescuento.value === 'porcentaje' && parseFloat(this.value) > 100) {
            this.value = 100;
        }

        calcularTotales();
    });

    // Validar al cambiar el tipo de descuento
    tipoDescuento.addEventListener('change', function() {
        if (this.value === 'porcentaje' && parseFloat(valDescuento.value) > 100) {
            valDescuento.value = 100;
        }
        calcularTotales();
    });

    function calcularTotales() {
        let subtotal = carrito.reduce((sum, item) => sum + (item.precio * item.cantidad), 0);
        let valorInput = parseFloat(valDescuento.value) || 0;
        let descuento = 0;
        
        if (tipoDescuento.value === 'fijo') {
            // 3. Validar que el descuento fijo no supere el subtotal
            if (valorInput > subtotal) {
                valorInput = subtotal;
                valDescuento.value = subtotal.toFixed(2); // Ajustar input visualmente
            }
            descuento = valorInput;
        } else {
            // Cálculo de porcentaje
            descuento = subtotal * (valorInput / 100);
        }
        
        // Asegurar que descuento no sea mayor a subtotal (redundancia de seguridad)
        if (descuento > subtotal) descuento = subtotal;

        const baseImponible = subtotal - descuento;
        const impuesto = baseImponible * IVA_RATE;
        const total = baseImponible + impuesto;

        document.getElementById('lblSubtotal').textContent = `$${subtotal.toFixed(2)}`;
        document.getElementById('lblDescuento').textContent = `-$${descuento.toFixed(2)}`;
        document.getElementById('lblImpuesto').textContent = `$${impuesto.toFixed(2)}`;
        document.getElementById('lblTotal').textContent = `$${total.toFixed(2)}`;
    }

    // --- Lógica de Clientes ---
    const inputCliente = document.getElementById('buscarCliente');
    const btnBuscarCliente = document.getElementById('btnBuscarCliente');
    const listaClientes = document.getElementById('listaClientes');
    const msgNoEncontrado = document.getElementById('msgClienteNoEncontrado');

    // Búsqueda al escribir
    inputCliente.addEventListener('input', function() {
        const val = this.value;
        msgNoEncontrado.classList.add('d-none'); // Ocultar mensaje de error al escribir
        
        if (val.length < 3) {
            listaClientes.classList.add('d-none');
            return;
        }

        buscarClientes(val);
    });

    // Búsqueda al presionar Enter
    inputCliente.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const val = this.value;
            if(val.length > 0) {
                buscarClientes(val, true); // true = modo exacto/enter
            }
        }
    });

    // Búsqueda al hacer clic en el botón de lupa
    btnBuscarCliente.addEventListener('click', function() {
        const val = inputCliente.value;
        if(val.length > 0) {
            buscarClientes(val, true);
        }
    });

    function buscarClientes(termino, esEnter = false) {
        fetch(`ajax_buscar_cliente.php?q=${termino}`)
            .then(response => response.json())
            .then(data => {
                listaClientes.innerHTML = '';
                
                if (data.length > 0) {
                    // Si es Enter y hay un solo resultado (o el primero coincide exacto), seleccionarlo
                    if (esEnter) {
                        const exacto = data.find(c => c.cedula === termino);
                        if (exacto) {
                            seleccionarCliente(exacto);
                            return;
                        } else if (data.length === 1) {
                            seleccionarCliente(data[0]);
                            return;
                        }
                    }

                    listaClientes.classList.remove('d-none');
                    msgNoEncontrado.classList.add('d-none');
                    
                    data.forEach(item => {
                        const div = document.createElement('div');
                        div.className = 'autocomplete-item';
                        div.innerHTML = `<strong>${item.cedula}</strong> - ${item.nombres} ${item.apellidos}`;
                        div.onclick = () => seleccionarCliente(item);
                        listaClientes.appendChild(div);
                    });
                } else {
                    listaClientes.classList.add('d-none');
                    if (esEnter || termino.length >= 10) {
                        msgNoEncontrado.classList.remove('d-none');
                    }
                }
            });
    }

    function seleccionarCliente(cliente) {
        document.getElementById('clienteId').value = cliente.id;
        document.getElementById('clienteNombre').value = `${cliente.nombres} ${cliente.apellidos}`;
        document.getElementById('clienteDireccion').value = cliente.direccion || '';
        document.getElementById('clienteCorreo').value = cliente.correo || '';
        
        inputCliente.value = cliente.cedula;
        listaClientes.classList.add('d-none');
        msgNoEncontrado.classList.add('d-none');
    }

    function guardarCliente() {
        const form = document.getElementById('formNuevoCliente');
        const formData = new FormData(form);

        fetch('ajax_guardar_cliente.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                seleccionarCliente({
                    id: data.id,
                    nombres: data.data.nombres,
                    apellidos: data.data.apellidos,
                    cedula: data.data.cedula,
                    direccion: data.data.direccion,
                    correo: data.data.correo
                });
                
                const modal = bootstrap.Modal.getInstance(document.getElementById('modalNuevoCliente'));
                modal.hide();
                form.reset();
                showToast('Cliente registrado correctamente', 'success');
            } else {
                showToast(data.message, 'danger');
            }
        });
    }

    // --- Procesar Venta ---
    function procesarVenta() {
        const clienteId = document.getElementById('clienteId').value;
        
        if (!clienteId) {
            showToast('Por favor seleccione un cliente.', 'warning');
            return;
        }
        if (carrito.length === 0) {
            showToast('El carrito está vacío.', 'warning');
            return;
        }

        const subtotal = parseFloat(document.getElementById('lblSubtotal').textContent.replace('$',''));
        const impuesto = parseFloat(document.getElementById('lblImpuesto').textContent.replace('$',''));
        const total = parseFloat(document.getElementById('lblTotal').textContent.replace('$',''));
        
        const data = {
            cliente_id: clienteId,
            productos: carrito,
            subtotal: subtotal,
            impuesto: impuesto,
            descuento_tipo: tipoDescuento.value,
            descuento_valor: parseFloat(valDescuento.value) || 0,
            total: total
        };

        if(!confirm(`¿Confirmar venta por $${total.toFixed(2)}?`)) return;

        fetch('guardar_venta.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                showToast('Venta registrada con éxito! ID: ' + result.venta_id, 'success');
                limpiarVenta();
            } else {
                showToast('Error: ' + result.message, 'danger');
            }
        })
        .catch(err => showToast('Error de red: ' + err, 'danger'));
    }

    function limpiarVenta() {
        carrito = [];
        renderizarTabla();
        document.getElementById('clienteId').value = '';
        document.getElementById('clienteNombre').value = '';
        document.getElementById('clienteDireccion').value = '';
        document.getElementById('clienteCorreo').value = '';
        inputCliente.value = '';
        valDescuento.value = 0;
        calcularTotales();
    }

    // Cerrar autocompletar al hacer click fuera
    document.addEventListener('click', function(e) {
        if (e.target !== inputProducto) listaProductos.classList.add('d-none');
        if (e.target !== inputCliente) listaClientes.classList.add('d-none');
    });
</script>

</body>
</html>