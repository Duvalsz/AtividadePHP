<?php
header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . "/db.php";
require_once __DIR__ . "/utils.php";

$method = $_SERVER['REQUEST_METHOD'];

if ($method === "POST") {
    $dados = json_decode(file_get_contents("php://input"), true);

    $nome  = $dados["nome"] ?? null;
    $email = $dados["email"] ?? null;
    $senha = $dados["senha"] ?? null;
    $telefone = $dados["telefone"] ?? null;
    $endereco = $dados["endereco"] ?? null;
    $estado = $dados["estado"] ?? null;
    $data_nascimento = $dados["data_nascimento"] ?? null;

    if (!$nome || !$email || !$senha || !$telefone || !$endereco || !$estado || !$data_nascimento) {
        echo json_encode(["erro" => "Todos os campos são obrigatórios."]);
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["erro" => "Formato de e-mail inválido."]);
        exit();
    }

    $validacaoSenha = validarSenha($senha);
    if ($validacaoSenha !== true) {
        echo json_encode(["erro" => $validacaoSenha]);
        exit();
    }

    if (!validarTelefone($telefone)) {
        echo json_encode(["erro" => "Telefone deve conter apenas números (10 ou 11 dígitos)."]);
        exit();
    }

    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
    $uuid = gerarUuid();

    $sql = "INSERT INTO api_usuarios (uuid, nome, email, senha, telefone, endereco, estado, data_nascimento) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssss", $uuid, $nome, $email, $senhaHash, $telefone, $endereco, $estado, $data_nascimento);

    if ($stmt->execute()) {
        echo json_encode(["sucesso" => "Usuário criado com sucesso!", "uuid" => $uuid]);
    } else {
        echo json_encode(["erro" => "Erro ao criar usuário: " . $conn->error]);
    }
}

elseif ($method === "DELETE") {
    if (!isset($_GET["uuid"])) {
        echo json_encode(["erro" => "UUID é obrigatório para excluir."]);
        exit();
    }

    $uuid = $_GET["uuid"];
    $sql = "DELETE FROM api_usuarios WHERE uuid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $uuid);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(["sucesso" => "Usuário excluído com sucesso."]);
        } else {
            echo json_encode(["erro" => "Usuário não encontrado."]);
        }
    } else {
        echo json_encode(["erro" => "Erro ao excluir: " . $conn->error]);
    }
}
else {
    http_response_code(405);
    echo json_encode(["erro" => "Método não permitido."]);
}
