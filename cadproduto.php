<?php

// Enable error reporting for debugging (remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set response headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Include database connection
require_once 'conexao.php';
$con->set_charset("utf8");

// Read and decode incoming JSON
$jsonParam = json_decode(file_get_contents('php://input'), true);

if (!$jsonParam) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid or missing JSON data.']);
    exit;
}

// Extract and sanitize fields
$nome         = trim($jsonParam['nome'] ?? '');
$fabricante   = isset($jsonParam['fabricante']) ? trim($jsonParam['fabricante']) : null;
$categoria    = isset($jsonParam['categoria']) ? trim($jsonParam['categoria']) : null;
$numero_serie = isset($jsonParam['numero_serie']) ? (int)$jsonParam['numero_serie'] : null;
$fragil       = !empty($jsonParam['fragil']) ? 1 : 0;

// Validate required field
if (empty($nome)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'The "nome" field is required.']);
    exit;
}

// Prepare SQL statement
$stmt = $con->prepare("
    INSERT INTO Produtos (nome, fabricante, categoria, numero_serie, fragil)
    VALUES (?, ?, ?, ?, ?)
");

if (!$stmt) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database preparation error: ' . $con->error]);
    exit;
}

// Bind parameters
$stmt->bind_param("sssii", $nome, $fabricante, $categoria, $numero_serie, $fragil);

// Execute statement
if ($stmt->execute()) {
    http_response_code(201);
    echo json_encode([
        'success' => true,
        'message' => 'Product inserted successfully.',
        'id_produto' => $stmt->insert_id
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error inserting product: ' . $stmt->error
    ]);
}

$stmt->close();
$con->close();

?>
