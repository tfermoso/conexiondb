<?php
if (isset($_POST["nombre"]) && $_POST["nombre"] !== "") {
    require_once "config.php";
    require_once "./includes/functions.php";
    $conn = conectarBaseDatos();
    $nombre=$_POST["nombre"];
    $despacho=$_POST["despacho"];
    $area_id=intval($_POST["area_id"]);
    $consultas_id=intval($_POST["consultas_id"]);   

    $stmt=$conn->prepare("INSERT INTO profesor (nombre, despacho, area_id, consultas_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssii", $nombre, $despacho, $area_id, $consultas_id); 
    if($stmt->execute()){
        echo "Profesor agregado exitosamente.";
    } else {
        echo "Error al agregar el profesor: " . $stmt->error;
    }
}





?>