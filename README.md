📌 API - Sistema de Usuários

API REST para cadastro, login e gerenciamento de usuários.  
Todas as respostas são no formato *JSON*.

---

## 🔑 Endpoints

### 1. Criar Usuário
*POST* /api_noite.php

#### Exemplo de requisição
```json
{
  "nome": "João Victor",
  "email": "joao@example.com",
  "senha": "Senha@123",
  "telefone": "11987654321",
  "endereco": "Rua A, 123",
  "estado": "SP",
  "data_nascimento": "1990-01-01"
}
