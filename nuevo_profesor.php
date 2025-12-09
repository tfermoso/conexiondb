<?php
require_once "config.php";
require_once "./includes/functions.php";

$conn=conectarBaseDatos();
$queryAreas="SELECT area_id, nombre FROM area ORDER BY nombre";
$areasResult=$conn->query($queryAreas);
$areas=[];
if($areasResult->num_rows>0){
    while($fila=$areasResult->fetch_assoc()){
        $areas[]=$fila;
    }
}

$queryConsultas="SELECT consulta_id, dia_semana, hora_inicio, hora_fin FROM consulta ORDER BY dia_semana, hora_inicio";
$consultasResult=$conn->query($queryConsultas); 
$consultas=[];
if($consultasResult->num_rows>0){
    while($fila=$consultasResult->fetch_assoc()){
        $consultas[]=$fila;
    }
}

?>