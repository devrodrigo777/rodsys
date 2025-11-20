# Levantamento de Permissões do Sistema - rodsys

## Resumo Executivo
Todas as permissões verificadas no método `user_has_permission()` durante a navegação da aplicação.

---

## Permissões por Módulo

### 📦 MÓDULO: Departments (Departamentos)

#### Controllers/Departments.php
- **novo()** - Linha 26
  - Permissão: `user.departments.create`
  - ⚠️ Bug: `$this->$permissionsModel` (escrito errado)
  - Método: `user_has_permission()` + `user_is_superadmin()`

- **edit()** - Linha 103
  - Permissão: `user.departments.edit`
  - Método: `user_has_permission()` + `user_is_superadmin()`

- **update()** - Linha 130
  - Permissão: `user.departments.edit`
  - Método: `user_has_permission()` + `user_is_superadmin()`

#### Controllers/API.php
- **list()** - Linhas 257, 263
  - Permissão: `user.edit` (para exibir botão editar)
  - Permissão: `user.delete` (para exibir botão deletar)
  - Contexto: Renderização de ações em DataTable

#### Libraries/Menu.php
- **Sidebar_Menu()** - Linha 25
  - Permissão: `departments.view`
  - Método: `user_has_permission()` + `user_is_superadmin()`

#### Config/Routes.php
- **Verificação de rota** - Linha 15
  - Permissão: `departments.view`
  - Método: `user_has_permission()` + `user_is_superadmin()`

---

### 📦 MÓDULO: Empresas (Companhias)

#### Controllers/API.php
- **create()** - Linha 21
  - Permissão: `user.empresas.create`
  - Método: `user_has_permission()` + `user_is_superadmin()`

- **update()** - Linha 44
  - Permissão: `user.empresas.edit`
  - Método: `user_has_permission()` + `user_is_superadmin()`

- **delete()** - Linha 72
  - Permissão: `user.empresas.delete`
  - Método: `user_has_permission()` + `user_is_superadmin()`

#### Controllers/Empresas.php
- **index()** - Linha 34
  - Permissão: `empresas.view`
  - Método: `user_has_permission()` + `user_is_superadmin()`

- **novo()** - Linha 60
  - Permissão: `empresas.create`
  - Método: `user_has_permission()` + `user_is_superadmin()`

- **editar()** - Linha 87
  - Permissão: `empresas.edit`
  - Método: `user_has_permission()` + `user_is_superadmin()`

#### Services/EmpresasService.php
- **createEmpresa()** - Linha 73
  - Permissão: `empresas.create`
  - Método: `user_has_permission()` + `user_is_superadmin()`

- **updateEmpresa()** - Linha 128
  - Permissão: `empresas.edit`
  - Método: `user_has_permission()` + `user_is_superadmin()`

- **deleteEmpresa()** - Linha 190
  - Permissão: `empresas.delete`
  - Método: `user_has_permission()` + `user_is_superadmin()`

#### Libraries/Menu.php
- **Sidebar_Menu()** - Linha 22
  - Permissão: `company.view`
  - Método: `user_has_permission()` + `user_is_superadmin()`

---

### 📦 MÓDULO: Login

#### Services/UserManagement.php
- **createUser()** - Linha 83
  - Permissão: `user.create`
  - Método: `user_has_permission()`

- **updateUser()** - Linha 160
  - Permissão: `user.edit`
  - Método: `user_has_permission()`

- **deleteUser()** - Linha 238
  - Permissão: `user.delete`
  - Método: `user_has_permission()`

#### Views/Login/ManageUsers.php
- Linha 17 e 47
  - Permissão: `user.create`
  - Contexto: Renderização condicional de botões na view

---

## Consolidação de Permissões Únicas

### 📋 Todas as Permissões Utilizadas (alfabética)

```
1. company.view           [Empresas > Libraries/Menu]
2. departments.view       [Departments > Libraries/Menu, Config/Routes]
3. empresas.create        [Empresas > Controllers/API, Services]
4. empresas.delete        [Empresas > Controllers/API, Services]
5. empresas.edit          [Empresas > Controllers/API, Services]
6. empresas.view          [Empresas > Controllers/Empresas]
7. user.create            [Login > Services/UserManagement, Views]
8. user.delete            [Departments > Controllers/API, Login > Services]
9. user.departments.create [Departments > Controllers/Departments]
10. user.departments.edit  [Departments > Controllers/Departments]
11. user.edit             [Departments > Controllers/API, Login > Services]
12. user.empresas.create  [Empresas > Controllers/API]
13. user.empresas.delete  [Empresas > Controllers/API]
14. user.empresas.edit    [Empresas > Controllers/API]
```

---

## Padrões Identificados

### ✅ Permissões Bem Estruturadas
- Módulo Empresas: Usa prefixo consistente `empresas.*` e `user.empresas.*`
- Verificações com lógica: `!user_has_permission() || !user_is_superadmin()`

### ⚠️ Inconsistências Encontradas

#### 1. **Nomenclatura Mista**
- Algumas permissões usam `departments.` enquanto outras usam `user.departments.`
- Exemplo: 
  - `departments.view` (Menu, Routes)
  - `user.departments.create`, `user.departments.edit` (Departments Controller)

#### 2. **Permissões Genéricas vs Específicas**
- `user.edit` / `user.delete` são genéricas (Departments API)
- `user.departments.edit` são específicas (Departments Controller)
- Pode causar conflito ou duplicação

#### 3. **Duplicação de Verificações**
- Empresas API verifica `user.empresas.*`
- Empresas Controller verifica `empresas.*`
- Deveriam ser unificadas

#### 4. **Bug de Syntax** 
- Departments Controller linha 26: `$this->$permissionsModel` (deveria ser `$this->permissionsModel`)

---

## Recomendações

### 🔧 Correções Necessárias

1. **Unificar nomenclatura de permissões**
   - Defina um padrão: `{modulo}.{acao}` ou `user.{modulo}.{acao}`
   - Suggesto: Usar `departments.{action}` e `empresas.{action}`

2. **Consolidar verificações duplicadas**
   - Remover `user.empresas.*` em favor de `empresas.*`
   - Remover `user.departments.*` em favor de `departments.*`

3. **Corrigir bugs identificados**
   - Departments.php linha 26: `$this->$permissionsModel` → `$this->permissionsModel`

4. **Centralizar validação**
   - Criar middleware para verificar permissões
   - Evitar verificação repetida em Controllers e Services

5. **Documentar Permissões no Banco**
   - Criar tabela/seed com todas as permissões
   - Facilitar auditoria e manutenção

---

## Status da Implementação

- [ ] Corrigir syntax error em Departments.php
- [ ] Unificar nomenclatura de permissões
- [ ] Remover permissões duplicadas
- [ ] Testar todas as verificações
- [ ] Atualizar documentação de permissões

