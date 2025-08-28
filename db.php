<?php
$host = "localhost";
$user = "root";
$pass = "aluno"; // Banco de dados desse pc n possui senha
$db   = "sistema_api";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["erro" => "Erro ao conectar com o banco de dados."], JSON_UNESCAPED_UNICODE);
    exit();
}
?>
