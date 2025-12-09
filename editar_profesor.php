<?php
session_start();

require_once "config.php";
require_once "./includes/functions.php";

$conn = conectarBaseDatos();

// -----------------------------------------------------
// VALIDAR ID RECIBIDO
// -----------------------------------------------------
if (!isset($_GET["id"]) || $_GET["id"] === "") {
    $_SESSION["mensaje"] = "ID de profesor no válido.";
    header("Location: listado_profesores.php");
    exit();
}

$id = intval($_GET["id"]); // seguridad adicional

// -----------------------------------------------------
// 1. OBTENER DATOS DEL PROFESOR
// -----------------------------------------------------
$stmtProfesor = $conn->prepare("
    SELECT nombre, despacho, area_id, consultas_id 
    FROM profesor 
    WHERE profesor_id = ?
");
$stmtProfesor->bind_param("i", $id);
$stmtProfesor->execute();
$result = $stmtProfesor->get_result();

if ($result->num_rows === 0) {
    $_SESSION["mensaje"] = "Profesor no encontrado.";
    header("Location: listado_profesores.php");
    exit();
}

$prof = $result->fetch_assoc();
$nombre = $prof["nombre"];
$despacho = $prof["despacho"];
$area_id_actual = $prof["area_id"];
$consultas_id_actual = $prof["consultas_id"];

// -----------------------------------------------------
// 2. OBTENER TODAS LAS ÁREAS
// -----------------------------------------------------
$stmtAreas = $conn->prepare("SELECT area_id, nombre FROM area ORDER BY nombre");
$stmtAreas->execute();
$areas = $stmtAreas->get_result();

// -----------------------------------------------------
// 3. OBTENER TODAS LAS CONSULTAS
// -----------------------------------------------------
$stmtConsultas = $conn->prepare("SELECT consultas_id, dia,hora FROM consultas ORDER BY dia, hora");
$stmtConsultas->execute();
$consultas = $stmtConsultas->get_result();

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Profesor</title>
</head>

<body>

    <h1>Editar Profesor</h1>

    <form method="POST" action="procesar_editar_profesor.php">

        <!-- ID oculto -->
        <input type="hidden" name="profesor_id" value="<?= htmlspecialchars($id); ?>">

        <!-- Nombre -->
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($nombre); ?>" required>
        <br><br>

        <!-- Despacho -->
        <label for="despacho">Despacho:</label>
        <input type="text" id="despacho" name="despacho" value="<?= htmlspecialchars($despacho); ?>" required>
        <br><br>

        <!-- ÁREA -->
        <label for="area_id">Área:</label>
        <select id="area_id" name="area_id" required>
            <option value="">-- Selecciona un área --</option>

            <?php while ($a = $areas->fetch_assoc()): ?>
                <option value="<?= $a['area_id']; ?>" <?= ($a['area_id'] == $area_id_actual) ? 'selected' : ''; ?>>
                    <?= htmlspecialchars($a['nombre']); ?>
                </option>
            <?php endwhile; ?>
        </select>
        <br><br>

        <!-- CONSULTAS -->
        <label for="consultas_id">Consulta:</label>
        <select id="consultas_id" name="consultas_id" required>
            <option value="">-- Selecciona una consulta --</option>

            <?php while ($c = $consultas->fetch_assoc()): ?>
                <option value="<?= $c['consultas_id']; ?>" <?= ($c['consultas_id'] == $consultas_id_actual) ? 'selected' : ''; ?>>
                    <?= htmlspecialchars($c['dia'] . " - " . $c['hora']); ?>
                </option>
            <?php endwhile; ?>

        </select>
        <br><br>

        <input type="submit" value="Actualizar Profesor">

    </form>

</body>

</html>