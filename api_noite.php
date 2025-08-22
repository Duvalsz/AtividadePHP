<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

require_once "db.php";
require_once "utils.php";

$method = $_SERVER["REQUEST_METHOD"];

if ($method !== "POST") {
    http_response_code(405);
    echo json_encode(["erro" => "Método não permitido. Use POST."], JSON_UNESCAPED_UNICODE);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);

$campos_necessarios = ["nome", "email", "senha", "telefone", "endereco", "estado", "data_nascimento"];
$erros = [];

// Verifica campos obrigatórios
foreach ($campos_necessarios as $campo) {
    if (empty($data[$campo])) {
        $erros[] = "O campo '$campo' é obrigatório.";
    }
}

// Validações
if (!empty($data["email"]) && !filter_var($data["email"], FILTER_VALIDATE_EMAIL)) {
    $erros[] = "Formato de e-mail inválido.";
}

if (!empty($data["telefone"])) {
    $telefone = preg_replace('/\D/', '', $data["telefone"]);
    $tamanho = strlen($telefone);
    if ($tamanho < 10 || $tamanho > 15) {
        $erros[] = "Telefone deve conter entre 10 e 15 dígitos.";
    }
} else {
    $telefone = "";
}

if (!empty($data["estado"]) && strlen($data["estado"]) !== 2) {
    $erros[] = "O estado deve conter exatamente 2 caracteres.";
}

if (!empty($data["data_nascimento"]) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $data["data_nascimento"])) {
    $erros[] = "A data de nascimento deve estar no formato AAAA-MM-DD.";
}

if (!empty($data["senha"]) && !validarSenhaForte($data["senha"])) {
    $erros[] = "A senha deve ter pelo menos 8 caracteres, incluindo maiúsculas, minúsculas, números e caractere especial.";
}

if (!empty($erros)) {
    http_response_code(400);
    echo json_encode(["erros" => $erros], JSON_UNESCAPED_UNICODE);
    exit();
}

// Escapar dados
$nome = $conn->real_escape_string($data["nome"]);
$email = $conn->real_escape_string($data["email"]);
$senha_hash = password_hash($data["senha"], PASSWORD_DEFAULT);
$telefone = $conn->real_escape_string($telefone);
$endereco = $conn->real_escape_string($data["endereco"]);
$estado = strtoupper($conn->real_escape_string($data["estado"]));
$data_nascimento = $conn->real_escape_string($data["data_nascimento"]);
$uuid = gerarUuid();

// Verifica duplicidade de e-mail
$verificaEmail = $conn->prepare("SELECT id FROM api_usuarios WHERE email = ?");
$verificaEmail->bind_param("s", $email);
$verificaEmail->execute();
$verificaEmail->store_result();

if ($verificaEmail->num_rows > 0) {
    http_response_code(409);
    echo json_encode(["erro" => "E-mail já cadastrado."], JSON_UNESCAPED_UNICODE);
    exit();
}
$verificaEmail->close();

// Inserir no banco
$stmt = $conn->prepare("INSERT INTO api_usuarios (uuid, nome, email, senha, telefone, endereco, estado, data_nascimento) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssssss", $uuid, $nome, $email, $senha_hash, $telefone, $endereco, $estado, $data_nascimento);

if ($stmt->execute()) {
    $id = $stmt->insert_id;
    $result = $conn->query("SELECT uuid, nome, email, telefone, endereco, estado, data_nascimento, criado_em FROM api_usuarios WHERE id = $id");
    $cliente = $result->fetch_assoc();

    echo json_encode([
        "mensagem" => "Cliente cadastrado com sucesso.",
        "cliente" => $cliente
    ], JSON_UNESCAPED_UNICODE);
} else {
    http_response_code(500);
    echo json_encode(["erro" => "Erro ao cadastrar o cliente."], JSON_UNESCAPED_UNICODE);
}

$stmt->close();
$conn->close();
?>
