<?php
require_once "config.php";
// Crear conexión
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Error en la conexión: " . $conn->connect_error);
}

// -------------------------------------------------------
// 1. Obtener lista de áreas para el select (consulta segura)
// -------------------------------------------------------
$stmt_areas = $conn->prepare("SELECT area_id, nombre FROM area ORDER BY nombre");
$stmt_areas->execute();
$result_areas = $stmt_areas->get_result();


// -------------------------------------------------------
// 2. Si el usuario seleccionó un área, obtener profesores
// -------------------------------------------------------
$profesores = [];
$area_seleccionada = null;

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["area_id"])) {

    $area_id = intval($_POST["area_id"]);

    // Obtener nombre del área seleccionada
    $stmt_area_nombre = $conn->prepare("SELECT nombre FROM area WHERE area_id = ?");
    $stmt_area_nombre->bind_param("i", $area_id);
    $stmt_area_nombre->execute();
    $result_area_nombre = $stmt_area_nombre->get_result();

    if ($result_area_nombre->num_rows > 0) {
        $area_seleccionada = $result_area_nombre->fetch_assoc()["nombre"];
    }

    // Obtener profesores de esa área
    $stmt_prof = $conn->prepare("
        SELECT profesor_id, nombre, despacho 
        FROM profesor
        WHERE area_id = ?
    ");
    $stmt_prof->bind_param("i", $area_id);
    $stmt_prof->execute();
    $profesores = $stmt_prof->get_result();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Áreas y Profesores</title>
</head>
<body>

<h1>Seleccionar un área</h1>

<form method="POST" action="">
    <label for="area_id">Área:</label>
    <select name="area_id" id="area_id" required>
        <option value="">-- Selecciona un área --</option>

        <?php while ($fila = $result_areas->fetch_assoc()): ?>
            <option value="<?= $fila['area_id']; ?>"
                <?= (isset($area_id) && $area_id == $fila['area_id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($fila['nombre']); ?>
            </option>
        <?php endwhile; ?>

    </select>

    <button type="submit">Mostrar profesores</button>
</form>

<hr>

<?php if ($area_seleccionada): ?>
    <h2>Profesores del área: <?= htmlspecialchars($area_seleccionada); ?></h2>

    <?php if ($profesores->num_rows > 0): ?>
        <ul>
            <?php while ($p = $profesores->fetch_assoc()): ?>
                <li>
                    <strong><?= htmlspecialchars($p["nombre"]); ?></strong>
                    — Despacho: <?= htmlspecialchars($p["despacho"]); ?>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p>No hay profesores en esta área.</p>
    <?php endif; ?>
<?php endif; ?>

</body>
</html>

