<?php
session_start();

require_once __DIR__ . '/../controller/AuthController.php';
require_once __DIR__ . '/../controller/CitaController.php';
require_once __DIR__ . '/../controller/ClienteController.php';

AuthController::verificarSesion();

function e($valor): string
{
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
}

function estadoClase(string $estado): string
{
    return match ($estado) {
        'Pendiente' => 'estado-pendiente',
        'Confirmada' => 'estado-confirmada',
        'Cancelada' => 'estado-cancelada',
        'Atendida' => 'estado-atendida',
        default => 'estado-pendiente'
    };
}

$citaController = new CitaController();
$clienteController = new ClienteController();

$mensaje = '';
$tipoMensaje = '';

$idCita = '';
$asunto = '';
$detalleCita = '';
$tipoCita = '';
$lugarCita = '';
$referencia = '';
$fecha = '';
$hora = '';
$estado = 'Pendiente';
$idCliente = '';

$modoEditar = false;

/* Procesar acciones */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    if ($accion === 'registrar') {
        $resultado = $citaController->registrar($_POST);

        $mensaje = $resultado['mensaje'];
        $tipoMensaje = $resultado['ok'] ? 'exito' : 'error';

        if (!$resultado['ok']) {
            $asunto = trim($_POST['txtAsunto'] ?? '');
            $detalleCita = trim($_POST['txtDetalleCita'] ?? '');
            $tipoCita = trim($_POST['txtTipoCita'] ?? '');
            $lugarCita = trim($_POST['txtLugarCita'] ?? '');
            $referencia = trim($_POST['txtReferencia'] ?? '');
            $fecha = trim($_POST['txtFecha'] ?? '');
            $hora = trim($_POST['txtHora'] ?? '');
            $estado = trim($_POST['txtEstado'] ?? 'Pendiente');
            $idCliente = trim($_POST['txtCliente'] ?? '');
        }
    }

    if ($accion === 'actualizar') {
        $resultado = $citaController->actualizar($_POST);

        $mensaje = $resultado['mensaje'];
        $tipoMensaje = $resultado['ok'] ? 'exito' : 'error';

        if (!$resultado['ok']) {
            $modoEditar = true;

            $idCita = trim($_POST['idCita'] ?? '');
            $asunto = trim($_POST['txtAsunto'] ?? '');
            $detalleCita = trim($_POST['txtDetalleCita'] ?? '');
            $tipoCita = trim($_POST['txtTipoCita'] ?? '');
            $lugarCita = trim($_POST['txtLugarCita'] ?? '');
            $referencia = trim($_POST['txtReferencia'] ?? '');
            $fecha = trim($_POST['txtFecha'] ?? '');
            $hora = trim($_POST['txtHora'] ?? '');
            $estado = trim($_POST['txtEstado'] ?? 'Pendiente');
            $idCliente = trim($_POST['txtCliente'] ?? '');
        }
    }

    if ($accion === 'eliminar') {
        $idEliminar = (int)($_POST['idCita'] ?? 0);

        $resultado = $citaController->eliminar($idEliminar);

        $mensaje = $resultado['mensaje'];
        $tipoMensaje = $resultado['ok'] ? 'exito' : 'error';
    }
}

/* Cargar cita para editar */
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['editar'])) {
    $idEditar = (int)$_GET['editar'];
    $citaEditar = $citaController->obtenerPorId($idEditar);

    if ($citaEditar) {
        $modoEditar = true;

        $idCita = $citaEditar['id_cita'];
        $asunto = $citaEditar['asunto'];
        $detalleCita = $citaEditar['detalle_cita'];
        $tipoCita = $citaEditar['tipo_cita'];
        $lugarCita = $citaEditar['lugar_cita'];
        $referencia = $citaEditar['referencia'];
        $fecha = $citaEditar['fecha'];
        $hora = substr($citaEditar['hora'], 0, 5);
        $estado = $citaEditar['estado'];
        $idCliente = $citaEditar['id_cliente'];
    } else {
        $mensaje = 'Cita no encontrada';
        $tipoMensaje = 'error';
    }
}

$clientes = $clienteController->listar();
$citas = $citaController->listar();
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
    <link rel="stylesheet" href="../assetes/css/style_cita.css">

    <title>Citas - Agenda Juanita v2</title>
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
                        <a href="cita.php" class="activo">Citas</a>
                    </li>
                    <li>
                        <a href="cliente.php">Clientes</a>
                    </li>
                    <li>
                        <a href="logout.php">Cerrar Sesión</a>
                    </li>
                </ul>
            </nav>

            <div class="buscar">
                <div class="cajaBusca">
                    <button type="button" id="btnBuscarCita">Buscar</button>
                    <input type="text" id="txtBuscar" placeholder="Buscar por cita o cliente">
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
                    <a href="reporte_citaView.php">Reportes</a>
                </li>
                <li>
                    <a href="logout.php">Cerrar Sesión</a>
                </li>
            </ul>
        </section>

        <section class="contenido contenido-citas">
            <div class="panel-citas">
                <div class="encabezado-panel">
                    <div>
                        <h1><?php echo $modoEditar ? 'Editar Cita' : 'Registro de Cita'; ?></h1>
                        <p>
                            <?php echo $modoEditar ? 'Actualiza los datos de la cita seleccionada' : 'Agenda y administra las citas de tus clientes'; ?>
                        </p>
                    </div>

                    <span class="usuario-activo">
                        <?php echo e($_SESSION['usuario'] ?? 'Usuario'); ?>
                    </span>
                </div>

                <?php if ($mensaje !== ''): ?>
                    <div class="mensaje <?php echo e($tipoMensaje); ?>">
                        <?php echo e($mensaje); ?>
                    </div>
                <?php endif; ?>

                <?php if (count($clientes) === 0): ?>
                    <div class="mensaje error">
                        Primero debes registrar al menos un cliente para poder crear citas.
                    </div>
                <?php endif; ?>

                <form action="" method="post" class="form-cita">
                    <input type="hidden" name="accion" value="<?php echo $modoEditar ? 'actualizar' : 'registrar'; ?>">
                    <input type="hidden" name="idCita" value="<?php echo e($idCita); ?>">

                    <div class="grid-formulario-cita">
                        <div class="grupo-input">
                            <label for="txtCliente">Cliente *</label>
                            <select name="txtCliente" id="txtCliente">
                                <option value="">Seleccione cliente</option>

                                <?php foreach ($clientes as $cliente): ?>
                                    <option 
                                        value="<?php echo e($cliente['id_cliente']); ?>"
                                        <?php echo ((string)$idCliente === (string)$cliente['id_cliente']) ? 'selected' : ''; ?>
                                    >
                                        <?php echo e($cliente['nombre'] . ' ' . $cliente['apellido'] . ' - ' . $cliente['correo']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="grupo-input">
                            <label for="txtAsunto">Asunto *</label>
                            <input
                                type="text"
                                name="txtAsunto"
                                id="txtAsunto"
                                maxlength="90"
                                placeholder="Ejemplo: Reunión de seguimiento"
                                value="<?php echo e($asunto); ?>"
                            >
                        </div>

                        <div class="grupo-input">
                            <label for="txtTipoCita">Tipo de cita</label>
                            <select name="txtTipoCita" id="txtTipoCita">
                                <option value="">Seleccione tipo</option>
                                <option value="Presencial" <?php echo $tipoCita === 'Presencial' ? 'selected' : ''; ?>>Presencial</option>
                                <option value="Virtual" <?php echo $tipoCita === 'Virtual' ? 'selected' : ''; ?>>Virtual</option>
                                <option value="Telefonica" <?php echo $tipoCita === 'Telefonica' ? 'selected' : ''; ?>>Telefónica</option>
                                <option value="Otro" <?php echo $tipoCita === 'Otro' ? 'selected' : ''; ?>>Otro</option>
                            </select>
                        </div>

                        <div class="grupo-input">
                            <label for="txtFecha">Fecha *</label>
                            <input
                                type="date"
                                name="txtFecha"
                                id="txtFecha"
                                value="<?php echo e($fecha); ?>"
                            >
                        </div>

                        <div class="grupo-input">
                            <label for="txtHora">Hora *</label>
                            <input
                                type="time"
                                name="txtHora"
                                id="txtHora"
                                value="<?php echo e($hora); ?>"
                            >
                        </div>

                        <div class="grupo-input">
                            <label for="txtEstado">Estado *</label>
                            <select name="txtEstado" id="txtEstado">
                                <option value="Pendiente" <?php echo $estado === 'Pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                                <option value="Confirmada" <?php echo $estado === 'Confirmada' ? 'selected' : ''; ?>>Confirmada</option>
                                <option value="Cancelada" <?php echo $estado === 'Cancelada' ? 'selected' : ''; ?>>Cancelada</option>
                                <option value="Atendida" <?php echo $estado === 'Atendida' ? 'selected' : ''; ?>>Atendida</option>
                            </select>
                        </div>

                        <div class="grupo-input">
                            <label for="txtLugarCita">Lugar *</label>
                            <input
                                type="text"
                                name="txtLugarCita"
                                id="txtLugarCita"
                                maxlength="100"
                                placeholder="Lugar de la cita"
                                value="<?php echo e($lugarCita); ?>"
                            >
                        </div>

                        <div class="grupo-input">
                            <label for="txtReferencia">Referencia</label>
                            <input
                                type="text"
                                name="txtReferencia"
                                id="txtReferencia"
                                maxlength="200"
                                placeholder="Referencia o indicaciones"
                                value="<?php echo e($referencia); ?>"
                            >
                        </div>

                        <div class="grupo-input grupo-completo">
                            <label for="txtDetalleCita">Detalle de la cita</label>
                            <textarea
                                name="txtDetalleCita"
                                id="txtDetalleCita"
                                maxlength="300"
                                placeholder="Descripción breve de la cita"
                            ><?php echo e($detalleCita); ?></textarea>
                        </div>
                    </div>

                    <div class="botones-cita">
                        <button 
                            type="submit" 
                            class="btn-principal"
                            <?php echo count($clientes) === 0 ? 'disabled' : ''; ?>
                        >
                            <?php echo $modoEditar ? 'Actualizar Cita' : 'Registrar Cita'; ?>
                        </button>

                        <?php if ($modoEditar): ?>
                            <a href="cita.php" class="btn-secundario">Cancelar</a>
                        <?php else: ?>
                            <button type="reset" class="btn-secundario">Cancelar</button>
                        <?php endif; ?>
                    </div>
                </form>

                <div class="separador"></div>

                <div class="encabezado-tabla">
                    <div>
                        <h2>Listado de Citas</h2>
                        <p>Total registradas: <?php echo count($citas); ?></p>
                    </div>
                </div>

                <div class="tabla-responsive">
                    <table class="tabla-citas" id="tablaCitas">
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
                                <th>Acciones</th>
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
                                            <span class="badge-estado <?php echo e(estadoClase($cita['estado'])); ?>">
                                                <?php echo e($cita['estado']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="acciones-tabla">
                                                <a
                                                    href="cita.php?editar=<?php echo e($cita['id_cita']); ?>"
                                                    class="btn-editar"
                                                >
                                                    Editar
                                                </a>

                                                <form
                                                    action=""
                                                    method="post"
                                                    onsubmit="return confirm('¿Seguro que deseas eliminar esta cita?');"
                                                >
                                                    <input type="hidden" name="accion" value="eliminar">
                                                    <input type="hidden" name="idCita" value="<?php echo e($cita['id_cita']); ?>">
                                                    <button type="submit" class="btn-eliminar">Eliminar</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="sin-registros">
                                        No hay citas registradas
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
                <img src="../imagenes/logo2.png" alt="">
                <a href="">Facebook</a>
            </div>
            <div class="redes">
                <img src="../imagenes/logo2.png" alt="">
                <a href="">Instagram</a>
            </div>
            <div class="redes">
                <img src="../imagenes/logo2.png" alt="">
                <a href="">Youtube</a>
            </div>
            <div class="redes">
                <img src="../imagenes/logo2.png" alt="">
                <a href="">Tik tok</a>
            </div>
            <div class="redes">
                <img src="../imagenes/logo2.png" alt="">
                <a href="">X</a>
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
        const tablaCitas = document.getElementById('tablaCitas');

        if (txtBuscar && tablaCitas) {
            txtBuscar.addEventListener('keyup', function () {
                const texto = txtBuscar.value.toLowerCase();
                const filas = tablaCitas.querySelectorAll('tbody tr');

                filas.forEach(function (fila) {
                    const contenido = fila.textContent.toLowerCase();
                    fila.style.display = contenido.includes(texto) ? '' : 'none';
                });
            });
        }
    </script>
</body>
</html>