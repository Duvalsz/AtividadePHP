# 📋 Documentação da API de Usuários - sistema_api

## 📖 Índice
- [Visão Geral](#-visão-geral)
- [Estrutura de Arquivos](#-estrutura-de-arquivos)
- [Pré-requisitos](#-pré-requisitos)
- [Configuração do Banco de Dados](#-configuração-do-banco-de-dados)
- [Endpoints da API](#-endpoints-da-api)
  - [Criar Usuário](#1-criar-usuário-post)
  - [Excluir Usuário](#2-excluir-usuário-delete)
  - [Login](#3-login-post)
- [Estrutura de Dados](#-estrutura-de-dados)
- [Validações](#-validações)
- [Exemplos de Uso](#-exemplos-de-uso)
- [Tratamento de Erros](#-tratamento-de-erros)
- [Testes](#-testes)
- [Funções Utilitárias](#-funções-utilitárias)
- [Suporte](#-suporte)

## 🎯 Visão Geral

API RESTful para gerenciamento de usuários com sistema de autenticação. Desenvolvida em PHP com MySQL, oferece operações CRUD básicas com validações de segurança para dados sensíveis.

## 📁 Estrutura de Arquivos

```
sistema_api/
├── api_noite.php          # Endpoint principal (POST/DELETE)
├── delete.php             # Endpoint alternativo para DELETE
├── login.php              # Endpoint para autenticação
├── db.php                 # Configuração de conexão com banco
├── utils.php              # Funções utilitárias
├── DVLMasterofBitching.sql # Script de criação do banco
├── teste.http             # Exemplos de requisições
└── README.md              # Esta documentação
```

## ⚙️ Pré-requisitos

- Servidor web (Apache/Nginx)
- PHP 7.4 ou superior
- MySQL 5.7 ou superior
- Extensão MySQLi ativada no PHP

## 🗄️ Configuração do Banco de Dados

1. Execute o script SQL para criar o banco e tabela:

```sql
CREATE DATABASE IF NOT EXISTS sistema_api;
USE sistema_api;

CREATE TABLE api_usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) NOT NULL UNIQUE,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE, 
    senha VARCHAR(255) NOT NULL,
    telefone VARCHAR(20) NOT NULL,
    endereco VARCHAR(200) NOT NULL,
    estado CHAR(2) NOT NULL,
    data_nascimento DATE NOT NULL, 
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

2. Configure as credenciais do banco no arquivo `db.php`:

```php
<?php
$host = "localhost";
$user = "root";
$pass = "aluno"; // Altere conforme sua configuração
$db   = "sistema_api";

$conn = new mysqli($host, $user, $pass, $db);
// ... resto do código
```

## 🌐 Endpoints da API

### 1. Criar Usuário (POST)
**Endpoint:** `/api_noite.php`

**Headers:**
```
Content-Type: application/json
```

**Body:**
```json
{
  "nome": "Maria Silva",
  "email": "maria.silva@example.com",
  "senha": "Senha@123",
  "telefone": "11987654321",
  "endereco": "Rua das Flores, 456",
  "estado": "RJ",
  "data_nascimento": "1995-05-10"
}
```

**Respostas:**
- `201 Created`: Usuário criado com sucesso
- `400 Bad Request`: Dados inválidos ou faltantes
- `409 Conflict`: E-mail já cadastrado
- `500 Internal Server Error`: Erro no servidor

### 2. Excluir Usuário (DELETE)
**Endpoint:** `/api_noite.php?uuid={uuid}`

**Parâmetro URL:** `uuid` - Identificador único do usuário

**Respostas:**
- `200 OK`: Usuário excluído com sucesso
- `400 Bad Request`: UUID não fornecido
- `404 Not Found`: Usuário não encontrado
- `500 Internal Server Error`: Erro no servidor

### 3. Login (POST)
**Endpoint:** `/login.php`

**Headers:**
```
Content-Type: application/json
```

**Body:**
```json
{
  "email": "maria.silva@example.com",
  "senha": "Senha@123"
}
```

**Resposta de Sucesso:**
```json
{
  "mensagem": "Login realizado com sucesso.",
  "usuario": {
    "id": 1,
    "uuid": "550e8400-e29b-41d4-a716-446655440000",
    "nome": "Maria Silva",
    "email": "maria.silva@example.com",
    "telefone": "11987654321",
    "endereco": "Rua das Flores, 456",
    "estado": "RJ",
    "data_nascimento": "1995-05-10",
    "criado_em": "2023-10-15 14:30:00"
  }
}
```

## 📊 Estrutura de Dados

### Tabela: `api_usuarios`
| Campo | Tipo | Obrigatório | Descrição |
|-------|------|-------------|-----------|
| id | INT | ✅ | Chave primária autoincrementável |
| uuid | CHAR(36) | ✅ | Identificador único universal |
| nome | VARCHAR(100) | ✅ | Nome completo do usuário |
| email | VARCHAR(150) | ✅ | E-mail único do usuário |
| senha | VARCHAR(255) | ✅ | Hash da senha |
| telefone | VARCHAR(20) | ✅ | Número de telefone |
| endereco | VARCHAR(200) | ✅ | Endereço completo |
| estado | CHAR(2) | ✅ | Sigla do estado (ex: SP, RJ) |
| data_nascimento | DATE | ✅ | Data de nascimento |
| criado_em | TIMESTAMP | ✅ | Data de criação do registro |

## 🔒 Validações

### Senha
- Mínimo 8 caracteres
- Pelo menos 1 letra maiúscula
- Pelo menos 1 letra minúscula  
- Pelo menos 1 número
- Pelo menos 1 caractere especial (@$!%*?&)

### Telefone
- Apenas números
- 10 ou 11 dígitos

### E-mail
- Formato válido de e-mail
- Único no sistema

### Campos Obrigatórios
Todos os campos são obrigatórios para criação de usuário.

## 🧪 Exemplos de Uso

### Criar usuário
```bash
curl -X POST http://localhost/api_noite.php \
  -H "Content-Type: application/json" \
  -d '{
    "nome": "João Santos",
    "email": "joao.santos@example.com",
    "senha": "Senha@123",
    "telefone": "11999998888",
    "endereco": "Av. Principal, 123",
    "estado": "SP",
    "data_nascimento": "1990-01-15"
  }'
```

### Excluir usuário
```bash
curl -X DELETE "http://localhost/api_noite.php?uuid=550e8400-e29b-41d4-a716-446655440000"
```

### Login
```bash
curl -X POST http://localhost/login.php \
  -H "Content-Type: application/json" \
  -d '{
    "email": "joao.santos@example.com",
    "senha": "Senha@123"
  }'
```

## ⚠️ Tratamento de Erros

A API retorna respostas padronizadas com códigos HTTP apropriados:

- `200`: Sucesso
- `400`: Requisição inválida (dados faltantes ou inválidos)
- `401`: Não autorizado (credenciais inválidas)
- `404`: Recurso não encontrado
- `409`: Conflito (e-mail duplicado)
- `500`: Erro interno do servidor

Exemplo de resposta de erro:
```json
{
  "erro": "A senha deve ter pelo menos 1 caractere especial."
}
```

## 🧪 Testes

Use o arquivo `teste.http` com a extensão REST Client do VSCode para testar os endpoints:

```http
### Criar cliente
POST http://localhost/api_noite.php
Content-Type: application/json

{
  "nome": "Maria Silva",
  "email": "maria.silva@example.com",
  "senha": "Senha@123",
  "telefone": "11987654321",
  "endereco": "Rua das Flores, 456",
  "estado": "RJ",
  "data_nascimento": "1995-05-10"
}

### Deletar cliente
DELETE http://localhost/api_noite.php?uuid=COLE-O-UUID-AQUI
```

## 🔧 Funções Utilitárias

O arquivo `utils.php` contém funções essenciais:

- `gerarUuid()`: Gera UUID v4 para identificação única
- `validarSenha($senha)`: Valida força da senha
- `validarTelefone($telefone)`: Valida formato do telefone

## 📞 Suporte

Para dúvidas ou problemas, verifique:
1. Configuração do banco de dados no arquivo `db.php`
2. Permissões de escrita no servidor
3. Logs de erro do PHP e MySQL