<?php
session_start();
require_once "config.php";
require_once "./includes/functions.php";

$conn = conectarBaseDatos();
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
if($_SESSION['area_id'] ?? false){
    $area_id = $_SESSION['area_id'];
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

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["area_id"])) {

    $area_id = intval($_POST["area_id"]);
    $_SESSION['area_id'] = $area_id;
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

    <table border="1" cellpadding="8" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Despacho</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>

            <?php while ($p = $profesores->fetch_assoc()): ?>
                <tr>
                    <td><?= $p["profesor_id"]; ?></td>
                    <td><?= htmlspecialchars($p["nombre"]); ?></td>
                    <td><?= htmlspecialchars($p["despacho"]); ?></td>

                    <td>
                        <!-- Botón Editar -->
                        <a 
                            href="editar_profesor.php?id=<?= $p['profesor_id']; ?>" 
                            style="padding:4px 8px; background:blue; color:white; text-decoration:none;">
                            Editar
                        </a>

                        <!-- Botón Borrar -->
                        <a 
                            href="borrar_profesor.php?id=<?= $p['profesor_id']; ?>"
                            onclick="return confirm('¿Seguro que quieres borrar este profesor?');"
                            style="padding:4px 8px; background:red; color:white; text-decoration:none; margin-left:5px;">
                            Borrar
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>

        </tbody>
    </table>

<?php else: ?>
    <p>No hay profesores en esta área.</p>
<?php endif; ?>

<?php endif; ?>
<?php
if (isset($_SESSION["mensaje"])) {
    echo "<p style='color:green;'>" . htmlspecialchars($_SESSION["mensaje"]) . "</p>";
    unset($_SESSION["mensaje"]);
}       
?>

</body>
</html>

