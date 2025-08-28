<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: DELETE");
header("Access-Control-Allow-Headers: Content-Type");

require_once __DIR__ . "/db.php";

$method = $_SERVER["REQUEST_METHOD"];

if ($method !== "DELETE") {
    http_response_code(405);
    echo json_encode(["erro" => "Método não permitido. Use DELETE."], JSON_UNESCAPED_UNICODE);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);

if (empty($data["uuid"])) {
    http_response_code(400);
    echo json_encode(["erro" => "O campo 'uuid' é obrigatório."], JSON_UNESCAPED_UNICODE);
    exit();
}

$uuid = $conn->real_escape_string($data["uuid"]);

$check = $conn->prepare("SELECT id FROM api_usuarios WHERE uuid = ?");
$check->bind_param("s", $uuid);
$check->execute();
$check->store_result();

if ($check->num_rows === 0) {
    http_response_code(404);
    echo json_encode(["erro" => "Usuário não encontrado."], JSON_UNESCAPED_UNICODE);
    exit();
}
$check->close();

$stmt = $conn->prepare("DELETE FROM api_usuarios WHERE uuid = ?");
$stmt->bind_param("s", $uuid);

if ($stmt->execute()) {
    echo json_encode(["mensagem" => "Usuário excluído com sucesso."], JSON_UNESCAPED_UNICODE);
} else {
    http_response_code(500);
    echo json_encode(["erro" => "Erro ao excluir usuário."], JSON_UNESCAPED_UNICODE);
}

$stmt->close();
$conn->close();
