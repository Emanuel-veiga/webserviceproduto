<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include DB connection
require_once 'conexao.php';
$con->set_charset("utf8");

// Consulta todos os produtos sem filtro
$sql = "
    SELECT id_produto, nome, fabricante, categoria, numero_serie, fragil
    FROM Produtos
";

$result = $con->query($sql);

$response = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $response[] = array_map(function ($val) {
    return $val !== null ? mb_convert_encoding($val, 'UTF-8', 'ISO-8859-1') : null;}, $row);
    }
} else {
    $response[] = [
        "id_produto" => 0,
        "nome" => "",
        "fabricante" => "",
        "categoria" => "",
        "numero_serie" => null,
        "fragil" => false
    ];
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);

$con->close();

?>
