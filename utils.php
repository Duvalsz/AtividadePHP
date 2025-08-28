<?php

function gerarUuid() {
    $data = random_bytes(16);
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

function validarSenha($senha) {
    if (strlen($senha) < 8) {
        return "A senha deve ter pelo menos 8 caracteres.";
    }
    if (!preg_match('/[A-Z]/', $senha)) {
        return "A senha deve ter pelo menos 1 letra maiúscula.";
    }
    if (!preg_match('/[a-z]/', $senha)) {
        return "A senha deve ter pelo menos 1 letra minúscula.";
    }
    if (!preg_match('/[0-9]/', $senha)) {
        return "A senha deve ter pelo menos 1 número.";
    }
    if (!preg_match('/[@$!%*?&]/', $senha)) {
        return "A senha deve ter pelo menos 1 caractere especial.";
    }
    return true;
}

function validarTelefone($telefone) {
    if (!preg_match('/^[0-9]{10,11}$/', $telefone)) {
        return false;
    }
    return true;
}
?>
