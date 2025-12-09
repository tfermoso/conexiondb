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

$queryConsultas="SELECT consultas_id, dia, hora FROM consultas ORDER BY dia, hora";
$consultasResult=$conn->query($queryConsultas); 
$consultas=[];
if($consultasResult->num_rows>0){
    while($fila=$consultasResult->fetch_assoc()){
        $consultas[]=$fila;
    }
}

?>
<!DOCTYPE html>
<html lang="es">        
<head>
    <meta charset="UTF-8">
    <title>Nuevo Profesor</title>
</head>
<body>
<h1>Agregar Nuevo Profesor</h1>
<form method="POST" action="procesar_nuevo_profesor.php">
    <label for="nombre">Nombre:</label>
    <input type="text" id="nombre" name="nombre" required><br><br>

    <label for="despacho">Despacho:</label>
    <input type="text" id="despacho" name="despacho" required><br><br>

    <label for="area_id">Área:</label>
    <select id="area_id" name="area_id" required>
        <option value="">-- Selecciona un área --</option>
        <?php foreach($areas as $area): ?>
            <option value="<?php echo $area['area_id']; ?>"><?php echo $area['nombre']; ?></option>
        <?php endforeach; ?>
    </select><br><br>

    <label for="consultas_id">Consultas:</label>
    <select id="consultas_id" name="consultas_id" required>
        <option value="">-- Selecciona una consulta --</option>
        <?php foreach($consultas as $consulta): ?>
            <option value="<?php echo $consulta['consultas_id']; ?>">
                <?php echo $consulta['dia'] . " a las " . $consulta['hora']; ?>
            </option>
        <?php endforeach; ?>
    </select><br><br>

    <input type="submit" value="Agregar Profesor">

</form>
</body>
</html>
