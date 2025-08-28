<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

require_once "db.php";

$method = $_SERVER["REQUEST_METHOD"];

if ($method !== "POST") {
    http_response_code(405);
    echo json_encode(["erro" => "Método não permitido. Use POST."], JSON_UNESCAPED_UNICODE);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);

if (empty($data["email"]) || empty($data["senha"])) {
    http_response_code(400);
    echo json_encode(["erro" => "E-mail e senha são obrigatórios."], JSON_UNESCAPED_UNICODE);
    exit();
}

$email = $conn->real_escape_string($data["email"]);
$senha = $data["senha"];

$stmt = $conn->prepare("SELECT id, uuid, nome, email, senha, telefone, endereco, estado, data_nascimento, criado_em FROM api_usuarios WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(401);
    echo json_encode(["erro" => "E-mail ou senha incorretos."], JSON_UNESCAPED_UNICODE);
    exit();
}

$usuario = $result->fetch_assoc();

if (!password_verify($senha, $usuario["senha"])) {
    http_response_code(401);
    echo json_encode(["erro" => "E-mail ou senha incorretos."], JSON_UNESCAPED_UNICODE);
    exit();
}

unset($usuario["senha"]);

echo json_encode([
    "mensagem" => "Login realizado com sucesso.",
    "usuario" => $usuario
], JSON_UNESCAPED_UNICODE);

$stmt->close();
$conn->close();
?>
