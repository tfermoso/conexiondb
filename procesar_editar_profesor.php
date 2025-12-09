<?php
session_start();

require_once "config.php";
require_once "./includes/functions.php";

$conn = conectarBaseDatos();

// ---------------------------------------------------------
// 1. VALIDAR QUE LLEGA POR POST
// ---------------------------------------------------------
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    $_SESSION["mensaje"] = "Acceso no permitido.";
    header("Location: listado_profesores.php");
    exit();
}

// ---------------------------------------------------------
// 2. VALIDAR CAMPOS OBLIGATORIOS
// ---------------------------------------------------------
if (
    !isset($_POST["profesor_id"]) ||
    !isset($_POST["nombre"]) ||
    !isset($_POST["despacho"]) ||
    !isset($_POST["area_id"]) ||
    !isset($_POST["consultas_id"])
) {
    $_SESSION["mensaje"] = "Faltan datos en el formulario.";
    header("Location: listado_profesores.php");
    exit();
}

// ---------------------------------------------------------
// 3. LIMPIAR Y PREPARAR DATOS
// ---------------------------------------------------------
$id           = intval($_POST["profesor_id"]);
$nombre       = trim($_POST["nombre"]);
$despacho     = trim($_POST["despacho"]);
$area_id      = intval($_POST["area_id"]);
$consultas_id = intval($_POST["consultas_id"]);

// ---------------------------------------------------------
// 4. VALIDACIONES BÁSICAS
// ---------------------------------------------------------
if ($nombre === "" || $despacho === "") {
    $_SESSION["mensaje"] = "El nombre y el despacho no pueden estar vacíos.";
    header("Location: editar_profesor.php?id=$id");
    exit();
}

// ---------------------------------------------------------
// 5. UPDATE SEGURO CON PREPARE STATEMENT
// ---------------------------------------------------------
$stmt = $conn->prepare("
    UPDATE profesor
    SET nombre = ?, despacho = ?, area_id = ?, consultas_id = ?
    WHERE profesor_id = ?
");

$stmt->bind_param("ssiii", $nombre, $despacho, $area_id, $consultas_id, $id);

if ($stmt->execute()) {
    $_SESSION["mensaje"] = "Profesor actualizado correctamente.";
} else {
    $_SESSION["mensaje"] = "Error al actualizar el profesor: " . $conn->error;
}

// ---------------------------------------------------------
// 6. REDIRECCIÓN AL LISTADO
// ---------------------------------------------------------
header("Location: listado_profesores.php");
exit();
