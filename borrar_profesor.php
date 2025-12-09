<?php
if(isset($_GET["id"]) && $_GET["id"] !== ""){
    $id = intval($_GET["id"]);
    require_once "config.php";
    require_once "./includes/functions.php";
    $conn = conectarBaseDatos();
    $stmt = $conn->prepare("DELETE FROM profesor WHERE profesor_id = ?");
    $stmt->bind_param("i",$id);
    $stmt->execute();
    if($stmt->affected_rows > 0){
        header("Location: listado_profesores.php");
        
    } else {
        header("Location: listado_profesores.php");
    } 

}

?>