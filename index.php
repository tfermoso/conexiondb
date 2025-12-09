<?php
require_once "config.php";

// Crear conexión
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Comprobar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$sql = "SELECT profesor_id, nombre, despacho, area_id, consultas_id 
        FROM profesor";

$result = $conn->query($sql);



if ($result->num_rows > 0) {

    while ($fila = $result->fetch_assoc()) {
        echo "ID: " . $fila["profesor_id"] . "<br>";
        echo "Nombre: " . $fila["nombre"] . "<br>";
        echo "Despacho: " . $fila["despacho"] . "<br>";
        echo "Área ID: " . $fila["area_id"] . "<br>";
        echo "Consultas ID: " . $fila["consultas_id"] . "<br><br>";
    }

} else {
    echo "No hay profesores registrados.";
}

$conn->close();
?>