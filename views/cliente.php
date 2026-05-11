<?php
session_start();

require_once __DIR__ . '/../controller/AuthController.php';
require_once __DIR__ . '/../controller/ClienteController.php';

AuthController::verificarSesion();

function e($valor): string
{
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
}

$clienteController = new ClienteController();

$mensaje = '';
$tipoMensaje = '';

$idCliente = '';
$nombre = '';
$apellido = '';
$correo = '';
$dni = '';
$telefono = '';
$direccion = '';
$edad = '';

$modoEditar = false;

/* Procesar acciones */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    if ($accion === 'registrar') {
        $resultado = $clienteController->registrar($_POST);

        $mensaje = $resultado['mensaje'];
        $tipoMensaje = $resultado['ok'] ? 'exito' : 'error';

        if (!$resultado['ok']) {
            $nombre = trim($_POST['txtNombre'] ?? '');
            $apellido = trim($_POST['txtApellido'] ?? '');
            $correo = trim($_POST['txtCorreo'] ?? '');
            $dni = trim($_POST['txtDni'] ?? '');
            $telefono = trim($_POST['txtTelefono'] ?? '');
            $direccion = trim($_POST['txtDireccion'] ?? '');
            $edad = trim($_POST['txtEdad'] ?? '');
        }
    }

    if ($accion === 'actualizar') {
        $resultado = $clienteController->actualizar($_POST);

        $mensaje = $resultado['mensaje'];
        $tipoMensaje = $resultado['ok'] ? 'exito' : 'error';

        if (!$resultado['ok']) {
            $modoEditar = true;

            $idCliente = trim($_POST['idCliente'] ?? '');
            $nombre = trim($_POST['txtNombre'] ?? '');
            $apellido = trim($_POST['txtApellido'] ?? '');
            $correo = trim($_POST['txtCorreo'] ?? '');
            $dni = trim($_POST['txtDni'] ?? '');
            $telefono = trim($_POST['txtTelefono'] ?? '');
            $direccion = trim($_POST['txtDireccion'] ?? '');
            $edad = trim($_POST['txtEdad'] ?? '');
        }
    }

    if ($accion === 'eliminar') {
        $idEliminar = (int)($_POST['idCliente'] ?? 0);

        $resultado = $clienteController->eliminar($idEliminar);

        $mensaje = $resultado['mensaje'];
        $tipoMensaje = $resultado['ok'] ? 'exito' : 'error';
    }
}

/* Cargar cliente para editar */
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['editar'])) {
    $idEditar = (int)$_GET['editar'];
    $clienteEditar = $clienteController->obtenerPorId($idEditar);

    if ($clienteEditar) {
        $modoEditar = true;

        $idCliente = $clienteEditar['id_cliente'];
        $nombre = $clienteEditar['nombre'];
        $apellido = $clienteEditar['apellido'];
        $correo = $clienteEditar['correo'];
        $dni = $clienteEditar['dni'];
        $telefono = $clienteEditar['telefono'];
        $direccion = $clienteEditar['direccion'];
        $edad = $clienteEditar['edad'];
    } else {
        $mensaje = 'Cliente no encontrado';
        $tipoMensaje = 'error';
    }
}

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
    <link rel="stylesheet" href="../assetes/css/style_cliente.css">

    <title>Clientes - Agenda Juanita v2</title>
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
                        <a href="citaView.php">Citas</a>
                    </li>
                    <li>
                        <a href="cliente.php" class="activo">Clientes</a>
                    </li>
                    <li>
                        <a href="logout.php">Cerrar Sesión</a>
                    </li>
                </ul>
            </nav>

            <div class="buscar">
                <div class="cajaBusca">
                    <button type="button" id="btnBuscarCliente">Buscar</button>
                    <input type="text" id="txtBuscar" placeholder="Buscar por cliente">
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
                    <a href="citaView.php">Citas</a>
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

        <section class="contenido contenido-clientes">
            <div class="panel-clientes">
                <div class="encabezado-panel">
                    <div>
                        <h1><?php echo $modoEditar ? 'Editar Cliente' : 'Registro de Cliente'; ?></h1>
                        <p>
                            <?php echo $modoEditar ? 'Actualiza la información del cliente seleccionado' : 'Registra y administra tus clientes'; ?>
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

                <form action="" method="post" class="form-cliente">
                    <input type="hidden" name="accion" value="<?php echo $modoEditar ? 'actualizar' : 'registrar'; ?>">
                    <input type="hidden" name="idCliente" value="<?php echo e($idCliente); ?>">

                    <div class="grid-formulario">
                        <div class="grupo-input">
                            <label for="txtNombre">Nombre *</label>
                            <input
                                type="text"
                                name="txtNombre"
                                id="txtNombre"
                                maxlength="30"
                                placeholder="Nombre"
                                value="<?php echo e($nombre); ?>"
                            >
                        </div>

                        <div class="grupo-input">
                            <label for="txtApellido">Apellido *</label>
                            <input
                                type="text"
                                name="txtApellido"
                                id="txtApellido"
                                maxlength="50"
                                placeholder="Apellido"
                                value="<?php echo e($apellido); ?>"
                            >
                        </div>

                        <div class="grupo-input">
                            <label for="txtCorreo">Correo *</label>
                            <input
                                type="email"
                                name="txtCorreo"
                                id="txtCorreo"
                                maxlength="80"
                                placeholder="correo@ejemplo.com"
                                value="<?php echo e($correo); ?>"
                            >
                        </div>

                        <div class="grupo-input">
                            <label for="txtDni">DNI</label>
                            <input
                                type="text"
                                name="txtDni"
                                id="txtDni"
                                maxlength="8"
                                placeholder="DNI"
                                value="<?php echo e($dni); ?>"
                            >
                        </div>

                        <div class="grupo-input">
                            <label for="txtTelefono">Teléfono *</label>
                            <input
                                type="text"
                                name="txtTelefono"
                                id="txtTelefono"
                                maxlength="12"
                                placeholder="Teléfono"
                                value="<?php echo e($telefono); ?>"
                            >
                        </div>

                        <div class="grupo-input">
                            <label for="txtDireccion">Dirección</label>
                            <input
                                type="text"
                                name="txtDireccion"
                                id="txtDireccion"
                                maxlength="90"
                                placeholder="Dirección"
                                value="<?php echo e($direccion); ?>"
                            >
                        </div>

                        <div class="grupo-input">
                            <label for="txtEdad">Edad</label>
                            <input
                                type="number"
                                name="txtEdad"
                                id="txtEdad"
                                min="0"
                                max="120"
                                placeholder="Edad"
                                value="<?php echo e($edad); ?>"
                            >
                        </div>
                    </div>

                    <div class="botones-cliente">
                        <button type="submit" class="btn-principal">
                            <?php echo $modoEditar ? 'Actualizar Cliente' : 'Registrar Cliente'; ?>
                        </button>

                        <?php if ($modoEditar): ?>
                            <a href="cliente.php" class="btn-secundario">Cancelar</a>
                        <?php else: ?>
                            <button type="reset" class="btn-secundario">Cancelar</button>
                        <?php endif; ?>
                    </div>
                </form>

                <div class="separador"></div>

                <div class="encabezado-tabla">
                    <div>
                        <h2>Listado de Clientes</h2>
                        <p>Total registrados: <?php echo count($clientes); ?></p>
                    </div>
                </div>

                <div class="tabla-responsive">
                    <table class="tabla-clientes" id="tablaClientes">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Correo</th>
                                <th>DNI</th>
                                <th>Teléfono</th>
                                <th>Dirección</th>
                                <th>Edad</th>
                                <th>Registro</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if (count($clientes) > 0): ?>
                                <?php foreach ($clientes as $cliente): ?>
                                    <tr>
                                        <td><?php echo e($cliente['id_cliente']); ?></td>
                                        <td>
                                            <?php echo e($cliente['nombre'] . ' ' . $cliente['apellido']); ?>
                                        </td>
                                        <td><?php echo e($cliente['correo']); ?></td>
                                        <td><?php echo e($cliente['dni'] ?? '-'); ?></td>
                                        <td><?php echo e($cliente['telefono']); ?></td>
                                        <td><?php echo e($cliente['direccion'] ?? '-'); ?></td>
                                        <td><?php echo e($cliente['edad'] ?? '-'); ?></td>
                                        <td><?php echo e($cliente['fecha_registro']); ?></td>
                                        <td>
                                            <div class="acciones-tabla">
                                                <a
                                                    href="cliente.php?editar=<?php echo e($cliente['id_cliente']); ?>"
                                                    class="btn-editar"
                                                >
                                                    Editar
                                                </a>

                                                <form
                                                    action=""
                                                    method="post"
                                                    onsubmit="return confirm('¿Seguro que deseas eliminar este cliente?');"
                                                >
                                                    <input type="hidden" name="accion" value="eliminar">
                                                    <input type="hidden" name="idCliente" value="<?php echo e($cliente['id_cliente']); ?>">
                                                    <button type="submit" class="btn-eliminar">Eliminar</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="sin-registros">
                                        No hay clientes registrados
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
                <img src="../imagenes/logo4.png" alt="facebook">
                <a href="">Facebook</a>
            </div>
            <div class="redes">
                <img src="../imagenes/Instagram.png" alt="Instagran">
                <a href="">Instagram</a>
            </div>
            <div class="redes">
                <img src="../imagenes/YouTube.png" alt="Youtube">
                <a href="">Youtube</a>
            </div>
            
            <div class="redes">
                <img src="../imagenes/Whatsapp.png" alt="Whatsapp">
                <a href="">Whatsapp</a>
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
        const tablaClientes = document.getElementById('tablaClientes');

        if (txtBuscar && tablaClientes) {
            txtBuscar.addEventListener('keyup', function () {
                const texto = txtBuscar.value.toLowerCase();
                const filas = tablaClientes.querySelectorAll('tbody tr');

                filas.forEach(function (fila) {
                    const contenido = fila.textContent.toLowerCase();
                    fila.style.display = contenido.includes(texto) ? '' : 'none';
                });
            });
        }
    </script>
</body>
</html>