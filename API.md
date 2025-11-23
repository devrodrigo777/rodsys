# 📡 API Documentation - RODSYS

Documentação das APIs RESTful disponíveis no RODSYS.

---

## 🔐 Autenticação

Todas as requisições requerem **sessão ativa** com permissões correspondentes.

A autenticação é validada em nível de controller antes de processar requests.

---

## 🎛️ Dashboard Modules API

**Base URL**: `/dashboard/modules/api`

### 1. Listar Módulos (Global)

```http
GET /dashboard/modules/api/list
```

**Response**:
```json
[
  {
    "id": 1,
    "name": "Login",
    "description": "Autenticação e gerenciamento de usuários",
    "controller": "Modules\\Login\\Controllers\\Login"
  },
  {
    "id": 2,
    "name": "Empresas",
    "description": "Gerenciamento de empresas",
    "controller": "Modules\\Empresas\\Controllers\\Empresas"
  }
]
```

**Permissão Requerida**: `mod.modules.view`

---

### 2. Listar Módulos por Empresa

```http
GET /dashboard/modules/api/list/:company_id
```

**Path Parameters**:
- `:company_id` = `id_empresa`

**Response**:
```json
{
  "company_id": 1,
  "company_name": "Acme Corp",
  "modules": [
    {
      "id": 1,
      "name": "Login",
      "enabled": true
    },
    {
      "id": 2,
      "name": "Empresas",
      "enabled": true
    }
  ]
}
```

**Permissão Requerida**: `mod.modules.view`

---

## 👥 Usuários API (Melhorias)

**Base URL**: `/login/api/usuarios`

### 1. Listar Usuários (DataTables)

```http
GET /login/api/usuarios/list
```

**Parâmetros** (DataTables server-side):
- `draw`: número sequencial da requisição
- `start`: índice de início
- `length`: quantidade de registros por página
- `search[value]`: termo de busca (busca em nome, cargo, empresa)

**Filtros Automáticos**:
- Superadmin: vê todos os usuários
- Usuário comum: vê apenas usuários da sua empresa
- Permissão `mod.user.company.listall`: permite listar usuários de todas as empresas

**Response**:
```json
{
  "draw": 1,
  "recordsTotal": 15,
  "recordsFiltered": 3,
  "data": [
    [
      "João Silva",
      "RH",
      "Acme Corp",
      "2025-01-10",
      "<button onclick='editUser(5)'>...</button>"
    ]
  ]
}
```

**Permissão Requerida**: `mod.user.view`

---

### 2. Criar Usuário

```http
POST /login/api/usuarios/create
```

**Headers**:
```
Content-Type: application/x-www-form-urlencoded
```

**Body**:
```
inputNome=João Silva
inputLogin=JOAO.SILVA
inputSenha=senha123
inputEmpresa=1
inputCargo=3
```

**Response** (Success):
```
Redirect to /dashboard/acessos/usuarios
Header: Set-Cookie: user.feedback.success=Usuário criado com sucesso.
```

**Response** (Error):
```
Redirect to /login/api/usuarios/novo
Header: Set-Cookie: user.feedback.error=Este login já está em uso.
```

**Validações**:
- `inputNome`: obrigatório, min 3 caracteres
- `inputLogin`: obrigatório, único por empresa
- `inputSenha`: obrigatório, min 8 caracteres
- `inputEmpresa`: empresa deve existir
- `inputCargo`: cargo deve existir

**Permissão Requerida**: `user.create`

---

### 3. Editar Usuário

```http
POST /login/api/usuarios/update/:id
```

**Path Parameters**:
- `:id` = `id_usuario_login` do usuário

**Body**:
```
inputNome=João Silva Novo
inputSenha=novaSenha123    [OPCIONAL - deixe vazio para manter]
inputEmpresa=1
inputCargo=2
```

**Response** (Success):
```
Redirect to /dashboard/acessos/usuarios
Header: Set-Cookie: user.feedback.success=Usuário atualizado com sucesso.
```

**Response** (Error):
```
Redirect back to /login/acessos/usuarios/:id
Header: Set-Cookie: user.feedback.error=Erro ao atualizar usuário: ...
Preserva dados via withInput()
```

**Notas**:
- `inputLogin` é ignorado (readonly em edição)
- `inputSenha` vazio = mantém senha atual
- `inputSenha` preenchido = atualiza com hash novo

**Permissão Requerida**: `user.edit`

---

### 4. Deletar Usuário

```http
DELETE /login/api/usuarios/:id
```

**Path Parameters**:
- `:id` = `id_pessoa` (não id_usuario_login)

**Response** (Success):
```json
{
  "message": "User deleted successfully"
}
HTTP 200 OK
```

**Response** (Error):
```json
{
  "message": "User not found"
}
HTTP 404 Not Found
```

**Permissão Requerida**: `user.delete`

---

## 🏢 Empresas API

**Base URL**: `/empresas/api`

### 1. Listar Empresas (DataTables)

```http
GET /empresas/api/list
```

**Parâmetros**: Mesmos do Usuários (DataTables format)

**Response**:
```json
{
  "data": [
    [
      "Acme Corporation",
      "11.222.333/0001-81",
      "Ativo",
      "2025-01-15",
      "<button>Editar</button> <button>Deletar</button>"
    ]
  ]
}
```

**Permissão Requerida**: `empresas.view`

---

### 2. Criar Empresa

```http
POST /empresas/api/create
```

**Body**:
```
inputRazaoSocial=Acme Corporation
inputCnpj=11222333000181
inputPlanoAtivo=1
```

**Response** (Success):
```
Redirect to /dashboard/empresas
Set-Cookie: user.feedback.success=Empresa criada com sucesso.
```

**Validações**:
- `inputCnpj`: 14 dígitos, CNPJ válido (check-digit), único
- `inputRazaoSocial`: obrigatório, max 255 caracteres
- `inputPlanoAtivo`: 0 ou 1

**Nota**: `data_adesao` é preenchida automaticamente com `date('Y-m-d')`

**Permissão Requerida**: `empresas.create`

---

### 3. Editar Empresa

```http
POST /empresas/api/update/:id
```

**Path Parameters**:
- `:id` = `id_empresa`

**Body**:
```
inputRazaoSocial=Acme Corporation Updated
inputPlanoAtivo=0
```

**Response**: Redirect com flashdata

**Permissão Requerida**: `empresas.edit`

---

### 4. Deletar Empresa

```http
DELETE /empresas/api/delete/:id
```

**Response**:
```json
{
  "success": true,
  "message": "Empresa deletada com sucesso."
}
```

**Permissão Requerida**: `empresas.delete`

---

## 🏛️ Departamentos API

**Base URL**: `/dashboard/departamentos/api`

### 1. Listar Departamentos

```http
GET /dashboard/departamentos/api/read
```

**Response**: DataTables format (mesma estrutura de Usuários)

**Permissão Requerida**: `departments.view`

---

### 2. Criar Departamento

```http
POST /departments/api/create
```

**Body**:
```
inputNome=Recursos Humanos
inputDescricao=Depto responsável por RH
permissoes[]=1
permissoes[]=2
permissoes[]=5
```

**Response**:
```
Redirect to /dashboard/departamentos
Set-Cookie: user.feedback.success=Departamento criado com sucesso.
```

**Permissão Requerida**: `departments.create`

---

### 3. Editar Departamento

```http
POST /departments/api/update/:id
```

**Body**:
```
inputNome=Recursos Humanos Atualizado
inputDescricao=...
permissoes[]=1
permissoes[]=3
```

**Nota**: Departamentos globais (`is_global=1`) ou readonly não podem ser editados

**Permissão Requerida**: `departments.edit`

---

### 4. Deletar Departamento

```http
DELETE /departments/api/delete/:id
```

**Fluxo**:
1. Busca todas as pessoas com este cargo
2. Reatribui para cargo "nenhum"
3. Deleta permissões associadas
4. Deleta o cargo

**Permissão Requerida**: `departments.delete`

---

## 🔢 Códigos de Status

| Código | Significado |
|--------|------------|
| `200` | Sucesso (GET, update, delete) |
| `201` | Criado com sucesso (POST create) |
| `400` | Bad request (validação falhou) |
| `403` | Forbidden (sem permissão) |
| `404` | Not found (registro não existe) |
| `500` | Server error (erro na transação) |

---

## 📝 Exemplos com cURL

### Listar Usuários
```bash
curl -X GET "http://rodsys.local/login/api/usuarios/list?draw=1&start=0&length=10" \
  -H "Cookie: PHPSESSID=abc123"
```

### Criar Empresa
```bash
curl -X POST "http://rodsys.local/empresas/api/create" \
  -d "inputRazaoSocial=Acme&inputCnpj=11222333000181&inputPlanoAtivo=1" \
  -H "Cookie: PHPSESSID=abc123"
```

### Deletar Usuário
```bash
curl -X DELETE "http://rodsys.local/login/api/usuarios/5" \
  -H "Cookie: PHPSESSID=abc123"
```

---

## 🔐 Validação de Permissões

Todas as rotas validam automaticamente:

```php
if (!$permissionsModel->user_has_permission('recurso.acao')) {
    // Retorna erro 403 ou redireciona
}
```

Permissões disponíveis:
- `mod.user.view`, `mod.user.create`, `mod.user.edit`, `mod.user.delete`
- `mod.user.company.listall` - permite visualizar usuários de todas as empresas
- `mod.empresas.view`, `mod.empresas.create`, `mod.empresas.edit`, `mod.empresas.delete`
- `mod.departments.view`, `mod.departments.create`, `mod.departments.edit`, `mod.departments.delete`
- `mod.modules.view` - visualizar módulos do sistema

---

## 📋 Flashdata (Mensagens)

Após redirect, a view pode acessar:

```php
<?php if(session()->getFlashdata('user.feedback.success')): ?>
    <div class="alert alert-success">
        <?= session()->getFlashdata('user.feedback.success') ?>
    </div>
<?php endif; ?>
```

---

## ⚙️ Transações

Operações críticas usam transações:

```php
$db->transStart();
  // INSERT/UPDATE/DELETE
  if (erro) throw Exception
$db->transComplete();

// Rollback automático em exceção
```

---

## 🐛 Debug

Para debug de requisições:

1. Ative logs em `.env`: `log.threshold = 0`
2. Verifique `/writable/logs/`
3. Use `$db->getLastQuery()` para SQL
4. Use DevTools do browser (Network tab)

---

**Última atualização**: 15 de Novembro, 2025
