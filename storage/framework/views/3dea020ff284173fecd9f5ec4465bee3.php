<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Configuración de Impresora - Sistema POS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border: 1px solid rgba(0, 0, 0, 0.125);
        }
        .btn-primary {
            background-color: #0066cc;
            border-color: #0066cc;
        }
        .btn-primary:hover {
            background-color: #0052a3;
            border-color: #0052a3;
        }
        .navbar-brand {
            font-weight: bold;
        }
        .config-section {
            margin-bottom: 2rem;
        }
        .status-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 8px;
        }
        .status-connected {
            background-color: #28a745;
        }
        .status-disconnected {
            background-color: #dc3545;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark bg-dark">
        <div class="container">
            <span class="navbar-brand mb-0 h1">
                <i class="fas fa-print me-2"></i>
                Sistema POS - Configuración de Impresora
            </span>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-cog me-2"></i>
                            Configuración de Impresora
                        </h5>
                    </div>
                    <div class="card-body">
                        <form id="printerConfigForm">
                            <div class="config-section">
                                <h6 class="text-muted mb-3">
                                    <i class="fas fa-printer me-2"></i>
                                    Configuración Actual
                                </h6>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="printerName" class="form-label">Nombre de la Impresora</label>
                                            <select class="form-select" id="printerName" name="printer_name">
                                                <option value="POS58" <?php echo e($configuracion['impresora'] == 'POS58' ? 'selected' : ''); ?>>POS58 (58mm)</option>
                                                <option value="POS80" <?php echo e($configuracion['impresora'] == 'POS80' ? 'selected' : ''); ?>>POS80 (80mm)</option>
                                                <option value="EPSON TM-T20" <?php echo e($configuracion['impresora'] == 'EPSON TM-T20' ? 'selected' : ''); ?>>EPSON TM-T20</option>
                                                <option value="EPSON TM-T88" <?php echo e($configuracion['impresora'] == 'EPSON TM-T88' ? 'selected' : ''); ?>>EPSON TM-T88</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="paperWidth" class="form-label">Ancho del Papel (caracteres)</label>
                                            <select class="form-select" id="paperWidth" name="paper_width">
                                                <option value="21" <?php echo e($configuracion['papel'] == 21 ? 'selected' : ''); ?>>21 (58mm)</option>
                                                <option value="37" <?php echo e($configuracion['papel'] == 37 ? 'selected' : ''); ?>>37 (80mm)</option>
                                                <option value="42" <?php echo e($configuracion['papel'] == 42 ? 'selected' : ''); ?>>42 (110mm)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Estado de Conexión</label>
                                    <div class="form-control-plaintext">
                                        <span class="status-indicator <?php echo e($estado_conexion ? 'status-connected' : 'status-disconnected'); ?>"></span>
                                        <span id="connectionStatus"><?php echo e($estado_conexion ? 'Conectada' : 'Desconectada'); ?> - <?php echo e($configuracion['impresora']); ?></span>
                                    </div>
                                </div>
                            </div>

                            <div class="config-section">
                                <h6 class="text-muted mb-3">
                                    <i class="fas fa-network-wired me-2"></i>
                                    Configuración de Conexión
                                </h6>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="connectionType" class="form-label">Tipo de Conexión</label>
                                            <select class="form-select" id="connectionType" name="connection_type">
                                                <option value="network" <?php echo e($configuracion['tipo_conexion'] == 'network' ? 'selected' : ''); ?>>Red/IP (cPanel/Web)</option>
                                                <option value="windows" <?php echo e($configuracion['tipo_conexion'] == 'windows' ? 'selected' : ''); ?>>Windows Local</option>
                                                <option value="file" <?php echo e($configuracion['tipo_conexion'] == 'file' ? 'selected' : ''); ?>>Archivo/Linux</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="printerIp" class="form-label">IP de la Impresora</label>
                                            <input type="text" class="form-control" id="printerIp" name="printer_ip" value="<?php echo e($configuracion['ip_impresora']); ?>" placeholder="192.168.1.100">
                                            <div class="form-text">Solo para conexión de red</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="printerPort" class="form-label">Puerto</label>
                                            <input type="number" class="form-control" id="printerPort" name="printer_port" value="<?php echo e($configuracion['puerto_impresora']); ?>" placeholder="9100">
                                            <div class="form-text">Puerto TCP (típicamente 9100)</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Configuración para cPanel:</strong> Use "Red/IP" e ingrese la dirección IP de su impresora térmica en la red local.
                                </div>
                            </div>

                            <div class="config-section">
                                <h6 class="text-muted mb-3">
                                    <i class="fas fa-building me-2"></i>
                                    Información de la Empresa
                                </h6>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="companyName" class="form-label">Nombre de la Empresa</label>
                                            <input type="text" class="form-control" id="companyName" name="company_name" value="<?php echo e($configuracion['nombre_empresa']); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="brandName" class="form-label">Marca/Logo (texto)</label>
                                            <input type="text" class="form-control" id="brandName" name="brand" value="<?php echo e($configuracion['marca_empresa']); ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="footerMessage" class="form-label">Mensaje de Pie</label>
                                            <input type="text" class="form-control" id="footerMessage" name="footer_message" value="<?php echo e($configuracion['mensaje_pie']); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="contact" class="form-label">Información de Contacto</label>
                                            <input type="text" class="form-control" id="contact" name="contact" value="<?php echo e($configuracion['contacto']); ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="config-section">
                                <h6 class="text-muted mb-3">
                                    <i class="fas fa-file-invoice me-2"></i>
                                    Configuración de Recibos
                                </h6>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" id="showLogo" name="show_logo" <?php echo e($configuracion['mostrar_logo'] ? 'checked' : ''); ?>>
                                            <label class="form-check-label" for="showLogo">
                                                Mostrar Logo
                                            </label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" id="showQR" name="show_qr" <?php echo e($configuracion['mostrar_qr'] ? 'checked' : ''); ?>>
                                            <label class="form-check-label" for="showQR">
                                                Mostrar Código QR
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" id="autoCut" name="auto_cut" <?php echo e($configuracion['cortar_automatico'] ? 'checked' : ''); ?>>
                                            <label class="form-check-label" for="autoCut">
                                                Corte Automático
                                            </label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" id="soundAlert" name="sound_alert" <?php echo e($configuracion['alerta_sonora'] ? 'checked' : ''); ?>>
                                            <label class="form-check-label" for="soundAlert">
                                                Alerta Sonora
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="config-section">
                                <h6 class="text-muted mb-3">
                                    <i class="fas fa-vial me-2"></i>
                                    Pruebas
                                </h6>

                                <div class="d-grid gap-2 d-md-flex">
                                    <button type="button" class="btn btn-outline-secondary" id="testPrint">
                                        <i class="fas fa-print me-2"></i>
                                        Imprimir Prueba
                                    </button>
                                    <button type="button" class="btn btn-outline-info" id="detectPrinters">
                                        <i class="fas fa-search me-2"></i>
                                        Detectar Impresoras
                                    </button>
                                </div>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <button type="button" class="btn btn-secondary me-md-2" id="resetConfig">
                                    <i class="fas fa-undo me-2"></i>
                                    Restablecer
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>
                                    Guardar Configuración
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Panel de información -->
                <div class="card mt-4">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            Información del Sistema
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <strong>Rutas Disponibles:</strong>
                                <ul class="list-unstyled mt-2">
                                    <li><code>/venta/{id}</code> - Imprimir Venta</li>
                                    <li><code>/boleta/{id}</code> - Imprimir Boleta</li>
                                    <li><code>/transferencia/{id}</code> - Imprimir Transferencia</li>
                                </ul>
                            </div>
                            <div class="col-md-4">
                                <strong>Tipos de Impresión:</strong>
                                <ul class="list-unstyled mt-2">
                                    <li>✓ Ventas</li>
                                    <li>✓ Boletas de Garantía</li>
                                    <li>✓ Transferencias</li>
                                </ul>
                            </div>
                            <div class="col-md-4">
                                <strong>Formatos Soportados:</strong>
                                <ul class="list-unstyled mt-2">
                                    <li>• Papel 58mm (21 caracteres)</li>
                                    <li>• Papel 80mm (37 caracteres)</li>
                                    <li>• Papel 110mm (42 caracteres)</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Configurar CSRF token para AJAX
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Sincronizar el selector de impresora con el ancho del papel
        document.getElementById('printerName').addEventListener('change', function() {
            const printerName = this.value;
            const paperWidthSelect = document.getElementById('paperWidth');
            const statusText = document.getElementById('connectionStatus');

            if (printerName.includes('POS58')) {
                paperWidthSelect.value = '21';
            } else if (printerName.includes('POS80')) {
                paperWidthSelect.value = '37';
            }

            statusText.textContent = `Conectada - ${printerName}`;
        });

        // Sincronizar el ancho del papel con la impresora
        document.getElementById('paperWidth').addEventListener('change', function() {
            const paperWidth = this.value;
            const printerSelect = document.getElementById('printerName');

            if (paperWidth === '21') {
                printerSelect.value = 'POS58';
            } else if (paperWidth === '37') {
                printerSelect.value = 'POS80';
            }
        });

        // Manejar el formulario para guardar configuración
        document.getElementById('printerConfigForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;

            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Guardando...';
            submitBtn.disabled = true;

            // Recopilar datos del formulario
            const formData = new FormData(this);

            fetch('/guardar-config', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    submitBtn.innerHTML = '<i class="fas fa-check me-2"></i>Guardado';

                    // Mostrar mensaje de éxito
                    alert('Configuración guardada correctamente');

                    setTimeout(() => {
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;

                        // Recargar página para mostrar nueva configuración
                        window.location.reload();
                    }, 1000);
                } else {
                    throw new Error(data.message || 'Error desconocido');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al guardar configuración: ' + error.message);

                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });

        // Botón de prueba de impresión
        document.getElementById('testPrint').addEventListener('click', function() {
            const btn = this;
            const originalText = btn.innerHTML;

            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Imprimiendo...';
            btn.disabled = true;

            fetch('/test-print', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    btn.innerHTML = '<i class="fas fa-check me-2"></i>Enviado';
                    alert('Impresión de prueba enviada correctamente');
                } else {
                    throw new Error(data.message || 'Error desconocido');
                }

                setTimeout(() => {
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }, 2000);
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error en la impresión: ' + error.message);

                btn.innerHTML = originalText;
                btn.disabled = false;
            });
        });

        // Botón de detectar impresoras
        document.getElementById('detectPrinters').addEventListener('click', function() {
            const btn = this;
            const originalText = btn.innerHTML;

            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Detectando...';
            btn.disabled = true;

            setTimeout(() => {
                btn.innerHTML = '<i class="fas fa-check me-2"></i>Detectadas';
                setTimeout(() => {
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }, 1000);
            }, 3000);
        });

        // Gestión de tipo de conexión
        document.getElementById('connectionType').addEventListener('change', function() {
            const connectionType = this.value;
            const ipField = document.getElementById('printerIp').closest('.col-md-4');
            const portField = document.getElementById('printerPort').closest('.col-md-4');
            
            if (connectionType === 'network') {
                ipField.style.display = 'block';
                portField.style.display = 'block';
            } else {
                ipField.style.display = 'none';
                portField.style.display = 'none';
            }
        });

        // Inicializar visibilidad al cargar
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('connectionType').dispatchEvent(new Event('change'));
        });

        // Botón de restablecer
        document.getElementById('resetConfig').addEventListener('click', function() {
            if (confirm('¿Estás seguro de que quieres restablecer la configuración?')) {
                document.getElementById('printerName').value = 'POS80';
                document.getElementById('paperWidth').value = '37';
                document.getElementById('showLogo').checked = true;
                document.getElementById('showQR').checked = false;
                document.getElementById('autoCut').checked = true;
                document.getElementById('soundAlert').checked = false;
                document.getElementById('companyName').value = 'DISTRIBUIDORA';
                document.getElementById('brandName').value = '¤ FADI ¤';
                document.getElementById('footerMessage').value = '___GRACIAS POR SU COMPRA___';
                document.getElementById('contact').value = 'CEL: 73010688';
                document.getElementById('connectionType').value = 'network';
                document.getElementById('printerIp').value = '192.168.1.100';
                document.getElementById('printerPort').value = '9100';
                document.getElementById('connectionStatus').textContent = 'Conectada - POS80';
            }
        });
    </script>
</body>
</html>
<?php /**PATH C:\laragon\www\pos\resources\views/config-impresora.blade.php ENDPATH**/ ?>