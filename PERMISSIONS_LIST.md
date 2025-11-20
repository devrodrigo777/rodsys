# 📋 Catálogo Completo de Permissões do Sistema

**Data**: 20 de Novembro de 2025  
**Padrão**: `mod.{recurso}.{ação}`  
**Total de Permissões Únicas**: 11

---

## 🗂️ Permissões por Módulo

### 📦 Módulo: Departments (Departamentos)

| Permissão | Descrição | Localização | Operação |
|---|---|---|---|
| `mod.departments.view` | Visualizar lista de departamentos | Routes, Libraries/Menu | GET |
| `mod.departments.create` | Criar novo departamento | Controllers/Departments::novo() | POST |
| `mod.departments.edit` | Editar departamento existente | Controllers/Departments::edit(), Controllers/API::list() | PUT |
| `mod.departments.delete` | Deletar departamento | Controllers/API::list() | DELETE |

**Arquivos Afetados**:
- `Modules/Departments/Config/Routes.php`
- `Modules/Departments/Controllers/Departments.php`
- `Modules/Departments/Controllers/API.php`
- `Modules/Departments/Libraries/Menu.php`

---

### 🏢 Módulo: Empresas (Companhias)

| Permissão | Descrição | Localização | Operação |
|---|---|---|---|
| `mod.empresas.view` | Visualizar lista de empresas | Routes, Controllers, Libraries/Menu | GET |
| `mod.empresas.create` | Criar nova empresa | Controllers/API::create(), Controllers/Empresas::novo(), Services | POST |
| `mod.empresas.edit` | Editar empresa existente | Controllers/API::update(), Controllers/Empresas::editar(), Services | PUT |
| `mod.empresas.delete` | Deletar empresa | Controllers/API::delete(), Services | DELETE |

**Arquivos Afetados**:
- `Modules/Empresas/Config/Routes.php`
- `Modules/Empresas/Controllers/API.php`
- `Modules/Empresas/Controllers/Empresas.php`
- `Modules/Empresas/Services/EmpresasService.php`
- `Modules/Empresas/Libraries/Menu.php`

---

### 👥 Módulo: Login/User (Usuários e Acessos)

| Permissão | Descrição | Localização | Operação |
|---|---|---|---|
| `mod.user.view` | Visualizar lista de usuários | Routes, Controllers/LoginAPI::userList(), Libraries/Menu | GET |
| `mod.user.create` | Criar novo usuário | Controllers/LoginAPI::createUser(), Services, Views | POST |
| `mod.user.edit` | Editar usuário existente | Controllers/Login::edit(), Controllers/LoginAPI::update(), Services | PUT |
| `mod.user.delete` | Deletar usuário | Controllers/LoginAPI::delete(), Services | DELETE |
| `mod.user.company.listall` | Listar TODAS as empresas | Models/EmpresaModel::listForMe() | GET (Admin) |
| `mod.user.company.listme` | Listar apenas SUA empresa | Models/EmpresaModel::listForMe() | GET (User) |

**Arquivos Afetados**:
- `Modules/Login/Config/Routes.php`
- `Modules/Login/Controllers/Login.php`
- `Modules/Login/Controllers/LoginAPI.php`
- `Modules/Login/Services/UserManagement.php`
- `Modules/Login/Models/EmpresaModel.php`
- `Modules/Login/Libraries/Menu.php`
- `Modules/Login/Views/Login/ManageUsers.php`

---

## 📊 Lista Consolidada Alfabética

```
1. mod.departments.create     - Criar departamentos
2. mod.departments.delete     - Deletar departamentos
3. mod.departments.edit       - Editar departamentos
4. mod.departments.view       - Visualizar departamentos
5. mod.empresas.create        - Criar empresas
6. mod.empresas.delete        - Deletar empresas
7. mod.empresas.edit          - Editar empresas
8. mod.empresas.view          - Visualizar empresas
9. mod.user.company.listall   - Listar todas as empresas
10. mod.user.company.listme   - Listar apenas sua empresa
11. mod.user.create           - Criar usuários
12. mod.user.delete           - Deletar usuários
13. mod.user.edit             - Editar usuários
14. mod.user.view             - Visualizar usuários
```

---

## 🔑 Mapeamento de Operações CRUD

### CREATE (Criar)
- `mod.departments.create`
- `mod.empresas.create`
- `mod.user.create`

### READ (Visualizar)
- `mod.departments.view`
- `mod.empresas.view`
- `mod.user.view`
- `mod.user.company.listall`
- `mod.user.company.listme`

### UPDATE (Editar)
- `mod.departments.edit`
- `mod.empresas.edit`
- `mod.user.edit`

### DELETE (Deletar)
- `mod.departments.delete`
- `mod.empresas.delete`
- `mod.user.delete`

---

## 📍 Matriz de Localização

### Por Controller

#### Departments
- `Controllers/Departments.php` 
  - `novo()` - `mod.departments.create`
  - `edit()` - `mod.departments.edit`
  - `update()` - `mod.departments.edit`

- `Controllers/API.php`
  - `list()` - `mod.departments.edit`, `mod.departments.delete` (renderização de botões)

#### Empresas
- `Controllers/API.php`
  - `create()` - `mod.empresas.create`
  - `update()` - `mod.empresas.edit`
  - `delete()` - `mod.empresas.delete`

- `Controllers/Empresas.php`
  - `index()` - `mod.empresas.view`
  - `novo()` - `mod.empresas.create`
  - `editar()` - `mod.empresas.edit`

#### Login
- `Controllers/Login.php`
  - `edit()` - `mod.user.edit`
  - `newUser()` - `mod.user.edit`

- `Controllers/LoginAPI.php`
  - `userList()` - `mod.user.view`
  - `update()` - `mod.user.edit`
  - `delete()` - `mod.user.delete`
  - `createCompany()` - `mod.empresas.create`
  - `createUser()` - `mod.user.create`

#### Services
- `Services/EmpresasService.php`
  - `createEmpresa()` - `mod.empresas.create`
  - `updateEmpresa()` - `mod.empresas.edit`
  - `deleteEmpresa()` - `mod.empresas.delete`

- `Services/UserManagement.php`
  - `createUser()` - `mod.user.create`
  - `updateUser()` - `mod.user.edit`
  - `deleteUser()` - `mod.user.delete`

#### Models
- `Models/EmpresaModel.php`
  - `listForMe()` - `mod.user.company.listall`, `mod.user.company.listme`

#### Routes
- `Config/Routes.php` (Departments) - `mod.departments.view`
- `Config/Routes.php` (Empresas) - `mod.empresas.view`
- `Config/Routes.php` (Login) - `mod.user.view`

#### Views
- `Views/Login/ManageUsers.php` - `mod.user.create` (condicional em 2 lugares)

#### Libraries (Sidebar Menu)
- `Libraries/Menu.php` (Departments) - `mod.departments.view`
- `Libraries/Menu.php` (Empresas) - `mod.empresas.view`
- `Libraries/Menu.php` (Login) - `mod.user.view`

---

## 🔐 Padrão de Validação

### Padrão A: Com Fallback para Superadmin
```php
if (!$model->user_has_permission('mod.recurso.acao') && !$model->user_is_superadmin()) {
    // Denegar acesso
}
```
**Uso**: Proteger operações que superadmins sempre podem fazer

### Padrão B: Apenas Permissão
```php
if (!$model->user_has_permission('mod.recurso.acao')) {
    // Denegar acesso
}
```
**Uso**: Validações de lista/renderização condicional

### Padrão C: Com Superadmin OU Permissão
```php
if ($model->user_has_permission('mod.recurso.acao') || $model->user_is_superadmin()) {
    // Permitir acesso
}
```
**Uso**: Renderização de menus e botões

---

## 📋 Exemplo de Seed de Permissões

```php
// Para inserir no banco de dados:
[
    ['slug' => 'mod.departments.create', 'descricao' => 'Criar departamentos'],
    ['slug' => 'mod.departments.read', 'descricao' => 'Visualizar departamentos'],
    ['slug' => 'mod.departments.update', 'descricao' => 'Editar departamentos'],
    ['slug' => 'mod.departments.delete', 'descricao' => 'Deletar departamentos'],
    
    ['slug' => 'mod.empresas.create', 'descricao' => 'Criar empresas'],
    ['slug' => 'mod.empresas.read', 'descricao' => 'Visualizar empresas'],
    ['slug' => 'mod.empresas.update', 'descricao' => 'Editar empresas'],
    ['slug' => 'mod.empresas.delete', 'descricao' => 'Deletar empresas'],
    
    ['slug' => 'mod.user.create', 'descricao' => 'Criar usuários'],
    ['slug' => 'mod.user.read', 'descricao' => 'Visualizar usuários'],
    ['slug' => 'mod.user.update', 'descricao' => 'Editar usuários'],
    ['slug' => 'mod.user.delete', 'descricao' => 'Deletar usuários'],
    ['slug' => 'mod.user.company.listall', 'descricao' => 'Listar todas as empresas'],
    ['slug' => 'mod.user.company.listme', 'descricao' => 'Listar apenas sua empresa'],
]
```

---

## ✅ Checklist de Verificação

- [x] Todas as permissões seguem o padrão `mod.{recurso}.{acao}`
- [x] Permissões padronizadas em 20+ arquivos
- [x] Controllers validam permissões antes de operações
- [x] Services validam permissões em operações críticas
- [x] Routes validam permissões antes de registrar
- [x] Views renderizam condicionalmente baseado em permissões
- [x] Menus mostram itens baseado em permissões
- [x] Superadmin tem fallback em operações CRUD

---

## 🔄 Referência Rápida

| Módulo | View | Create | Read | Update | Delete |
|--------|------|--------|------|--------|--------|
| **Departments** | ✓ | ✓ | ✓ | ✓ | ✓ |
| **Empresas** | ✓ | ✓ | ✓ | ✓ | ✓ |
| **User** | ✓ | ✓ | ✓ | ✓ | ✓ |

