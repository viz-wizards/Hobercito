<?php
session_start();

require_once __DIR__ . '/../controller/AuthController.php';
require_once __DIR__ . '/../controller/ReporteCitaController.php';
require_once __DIR__ . '/../controller/ClienteController.php';

AuthController::verificarSesion();

function e($valor): string
{
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
}

function estadoClaseReporte(string $estado): string
{
    return match ($estado) {
        'Pendiente' => 'estado-pendiente',
        'Confirmada' => 'estado-confirmada',
        'Cancelada' => 'estado-cancelada',
        'Atendida' => 'estado-atendida',
        default => 'estado-pendiente'
    };
}

$reporteController = new ReporteCitaController();
$clienteController = new ClienteController();

$resultado = $reporteController->generar($_GET);

$mensaje = $resultado['mensaje'];
$filtros = $resultado['filtros'];
$citas = $resultado['citas'];
$resumen = $resultado['resumen'];

$clientes = $clienteController->listar();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="bc tech">
    <meta name="robots" content="index,follow">
    <meta name="description" content="agenda basica">
    <meta name="keywords" content="agenda">

    <link rel="icon" href="../imagenes/logo1.png">
    <link rel="stylesheet" href="../assetes/css/reporte_cita.css">

    <title>Reportes de Citas - Agenda Juanita v2</title>
</head>
<body>
    <header>
        <section class="lineaSuperior">
            <div class="linaSupIzquierda">
                <img src="../imagenes/logo1.png" alt="logo" class="logo">
                <h2>Agenda Digital</h2>
            </div>

            <div class="lineaSupDerecha">
                <a href="quienes.php">Quienes somos</a>
                <a href="contacto.php">Contacto</a>
            </div>
        </section>

        <section class="lineaInferior">
            <nav>
                <ul>
                    <li>
                        <a href="home.php">Inicio</a>
                    </li>
                    <li>
                        <a href="cita.php">Citas</a>
                    </li>
                    <li>
                        <a href="cliente.php">Clientes</a>
                    </li>
                    <li>
                        <a href="#" class="activo">Reportes</a>
                    </li>
                    <li>
                        <a href="logout.php">Cerrar Sesión</a>
                    </li>
                </ul>
            </nav>

            <div class="buscar">
                <div class="cajaBusca">
                    <button type="button" id="btnBuscarReporte">Buscar</button>
                    <input type="text" id="txtBuscar" placeholder="Buscar en reporte">
                </div>
            </div>
        </section>
    </header>

    <main>
        <section class="menu_vertical">
            <ul>
                <li>
                    <a href="home.php">Inicio</a>
                </li>
                <li>
                    <a href="cita.php">Citas</a>
                </li>
                <li>
                    <a href="cliente.php">Clientes</a>
                </li>
                <li>
                    <a href="reporte_citas.php">Reportes</a>
                </li>
                <li>
                    <a href="logout.php">Cerrar Sesión</a>
                </li>
            </ul>
        </section>

        <section class="contenido contenido-reportes">
            <div class="panel-reportes">
                <div class="encabezado-panel">
                    <div>
                        <h1>Reportes de Citas</h1>
                        <p>Consulta tus citas por fechas, estado y cliente</p>
                    </div>

                    <span class="usuario-activo">
                        <?php echo e($_SESSION['usuario'] ?? 'Usuario'); ?>
                    </span>
                </div>

                <?php if ($mensaje !== ''): ?>
                    <div class="mensaje error">
                        <?php echo e($mensaje); ?>
                    </div>
                <?php endif; ?>

                <form action="" method="get" class="form-reporte">
                    <div class="grid-filtros-reporte">
                        <div class="grupo-input">
                            <label for="fecha_desde">Fecha desde</label>
                            <input 
                                type="date" 
                                name="fecha_desde" 
                                id="fecha_desde"
                                value="<?php echo e($filtros['fecha_desde']); ?>"
                            >
                        </div>

                        <div class="grupo-input">
                            <label for="fecha_hasta">Fecha hasta</label>
                            <input 
                                type="date" 
                                name="fecha_hasta" 
                                id="fecha_hasta"
                                value="<?php echo e($filtros['fecha_hasta']); ?>"
                            >
                        </div>

                        <div class="grupo-input">
                            <label for="estado">Estado</label>
                            <select name="estado" id="estado">
                                <option value="">Todos</option>
                                <option value="Pendiente" <?php echo $filtros['estado'] === 'Pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                                <option value="Confirmada" <?php echo $filtros['estado'] === 'Confirmada' ? 'selected' : ''; ?>>Confirmada</option>
                                <option value="Cancelada" <?php echo $filtros['estado'] === 'Cancelada' ? 'selected' : ''; ?>>Cancelada</option>
                                <option value="Atendida" <?php echo $filtros['estado'] === 'Atendida' ? 'selected' : ''; ?>>Atendida</option>
                            </select>
                        </div>

                        <div class="grupo-input">
                            <label for="id_cliente">Cliente</label>
                            <select name="id_cliente" id="id_cliente">
                                <option value="0">Todos</option>

                                <?php foreach ($clientes as $cliente): ?>
                                    <option 
                                        value="<?php echo e($cliente['id_cliente']); ?>"
                                        <?php echo (int)$filtros['id_cliente'] === (int)$cliente['id_cliente'] ? 'selected' : ''; ?>
                                    >
                                        <?php echo e($cliente['nombre'] . ' ' . $cliente['apellido']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="botones-reporte">
                        <button type="submit" class="btn-principal">Filtrar Reporte</button>
                        <a href="reporte_citas.php" class="btn-secundario">Limpiar</a>
                        <button type="button" class="btn-imprimir" onclick="window.print()">Imprimir</button>
                    </div>
                </form>

                <div class="tarjetas-resumen">
                    <div class="tarjeta-reporte">
                        <span>Total</span>
                        <strong><?php echo e($resumen['total']); ?></strong>
                    </div>

                    <div class="tarjeta-reporte pendiente">
                        <span>Pendientes</span>
                        <strong><?php echo e($resumen['pendientes']); ?></strong>
                    </div>

                    <div class="tarjeta-reporte confirmada">
                        <span>Confirmadas</span>
                        <strong><?php echo e($resumen['confirmadas']); ?></strong>
                    </div>

                    <div class="tarjeta-reporte cancelada">
                        <span>Canceladas</span>
                        <strong><?php echo e($resumen['canceladas']); ?></strong>
                    </div>

                    <div class="tarjeta-reporte atendida">
                        <span>Atendidas</span>
                        <strong><?php echo e($resumen['atendidas']); ?></strong>
                    </div>
                </div>

                <div class="separador"></div>

                <div class="encabezado-tabla">
                    <div>
                        <h2>Resultado del Reporte</h2>
                        <p>Total encontrado: <?php echo count($citas); ?></p>
                    </div>
                </div>

                <div class="tabla-responsive">
                    <table class="tabla-reportes" id="tablaReportes">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Asunto</th>
                                <th>Tipo</th>
                                <th>Lugar</th>
                                <th>Fecha</th>
                                <th>Hora</th>
                                <th>Estado</th>
                                <th>Contacto</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if (count($citas) > 0): ?>
                                <?php foreach ($citas as $cita): ?>
                                    <tr>
                                        <td><?php echo e($cita['id_cita']); ?></td>

                                        <td>
                                            <strong><?php echo e($cita['cliente']); ?></strong><br>
                                            <span class="texto-suave"><?php echo e($cita['correo']); ?></span>
                                        </td>

                                        <td>
                                            <strong><?php echo e($cita['asunto']); ?></strong>

                                            <?php if (!empty($cita['detalle_cita'])): ?>
                                                <br>
                                                <span class="texto-suave">
                                                    <?php echo e($cita['detalle_cita']); ?>
                                                </span>
                                            <?php endif; ?>
                                        </td>

                                        <td><?php echo e($cita['tipo_cita'] ?? '-'); ?></td>

                                        <td>
                                            <?php echo e($cita['lugar_cita']); ?>

                                            <?php if (!empty($cita['referencia'])): ?>
                                                <br>
                                                <span class="texto-suave">
                                                    Ref: <?php echo e($cita['referencia']); ?>
                                                </span>
                                            <?php endif; ?>
                                        </td>

                                        <td><?php echo e($cita['fecha']); ?></td>
                                        <td><?php echo e(substr($cita['hora'], 0, 5)); ?></td>

                                        <td>
                                            <span class="badge-estado <?php echo e(estadoClaseReporte($cita['estado'])); ?>">
                                                <?php echo e($cita['estado']); ?>
                                            </span>
                                        </td>

                                        <td>
                                            <span class="texto-suave">
                                                Tel: <?php echo e($cita['telefono'] ?? '-'); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="sin-registros">
                                        No hay citas para los filtros seleccionados
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <div class="redes_sociales">
            <div class="redes">
                <img src="../imagenes/logo4.png" alt="Facebook">
                <a href="">Facebook</a>
            </div>
            <div class="redes">
                <img src="../imagenes/Instagram.png" alt="Instagran">
                <a href="">Instagram</a>
            </div>
            <div class="redes">
                <img src="../imagenes/YouTube.png" alt="YouTube">
                <a href="">Youtube</a>
            </div>
        </div>

        <div class="contacto">
            <a href="">Contacto</a>
            <a href="">Nuestros clientes</a>
            <a href="">Quienes somos</a>
        </div>
    </footer>

    <script>
        const txtBuscar = document.getElementById('txtBuscar');
        const tablaReportes = document.getElementById('tablaReportes');

        if (txtBuscar && tablaReportes) {
            txtBuscar.addEventListener('keyup', function () {
                const texto = txtBuscar.value.toLowerCase();
                const filas = tablaReportes.querySelectorAll('tbody tr');

                filas.forEach(function (fila) {
                    const contenido = fila.textContent.toLowerCase();
                    fila.style.display = contenido.includes(texto) ? '' : 'none';
                });
            });
        }
    </script>
</body>
</html>