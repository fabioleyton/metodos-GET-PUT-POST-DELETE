<?php

$host = "localhost";
$usuario = "root";
$password = "";
$basededatos = "api";

//Prueba de Conexión

$conexion = mysqli_connect($host, $usuario, $password, $basededatos);
// Check connection
if (!$conexion) {
    die("Connection failed: " . mysqli_connect_error());
}
//echo "Conectado con éxito";/
//mysqli_close($conexion);

header("Content-Type: application/json");
$metodo = $_SERVER['REQUEST_METHOD'];
//print_r($metodo);/

$path = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '/';
$buscarId = explode('/', $path);
$id = ($path != '/') ? end($buscarId) : null;

switch ($metodo) {
        //SELECT 
    case 'GET':
        //echo "Consulta de Registros - GET";/
        consultaSelect($conexion, $id);
        break;
        //INSERT
    case 'POST':
        //echo "Insertar Registros - POST";/
        insertar($conexion);
        break;
        //UPDATE
    case 'PUT':
        //echo "Edición de Registros - PUT";/
        actualizar($conexion, $id);
        break;
        //DELETE
    case 'DELETE':
        //echo "Borrado de Registros - DELETE";/
        borrar($conexion, $id);
        break;
    default:
        echo "Método no permitido";
        break;
}

function consultaSelect($conexion, $id)
{

    $sql = ($id === null) ? "SELECT * FROM usuarios" : "SELECT * FROM usuarios WHERE id=$id";
    $resultado = $conexion->query($sql);

    if ($resultado) {
        $datos = array();
        while ($fila = $resultado->fetch_assoc()) {
            $datos[] = $fila;
        }
        echo json_encode($datos);
    }
}

function insertar($conexion)
{

    $dato = json_decode(file_get_contents('php://input'), true);
    $nombre = $dato['nombre'];

    $sql = "INSERT INTO usuarios(nombre) VALUES ('$nombre')";
    $resultado = $conexion->query($sql);

    if ($resultado) {
        $dato['id'] = $conexion->insert_id;
        echo json_encode($dato);
    } else {
        echo json_encode(array('error' => 'Error al crear usuario'));
    }
}

function borrar($conexion, $id)
{

    echo "El id a borrar es: " . $id;

    $sql = "DELETE FROM usuarios WHERE id = $id";
    $resultado = $conexion->query($sql);

    if ($resultado) {
        echo json_encode(array('Mensaje' => 'Usuario borrado'));
    } else {
        echo json_encode(array('error' => 'Error al borrar usuario'));
    }
}

function actualizar($conexion, $id)
{

    $dato = json_decode(file_get_contents('php://input'), true);
    $nombre = $dato['nombre'];

    echo "El id a editar es: " . $id . " con el dato " . $nombre;

    $sql = "UPDATE usuarios SET nombre = '$nombre' WHERE id = $id";
    $resultado = $conexion->query($sql);

    if ($resultado) {
        echo json_encode(array('Mensaje' => 'Usuario actualizado'));
    } else {
        echo json_encode(array('error' => 'Error al actualizar el usuario'));
    }
}
