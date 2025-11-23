# RODSYS - Sistema de Gestão Modular

## 📋 Descrição

**RODSYS** é um sistema web modular de gestão empresarial desenvolvido com **CodeIgniter 4**, projetado para gerenciar usuários, departamentos, empresas e permissões de forma escalável e segura.

O projeto implementa uma arquitetura baseada em **módulos**, separação clara entre **Model-Service-Controller (MSC)**, validação de permissões em nível de negócio e interface responsiva com **Bootstrap** e **DataTables**.

---

## 🎯 Funcionalidades Principais

### ✅ Módulos Implementados

#### 1. **Login Module** (`Modules/Login/`)
- Autenticação e gerenciamento de usuários
- Duas tabelas integradas: `login` (credenciais) e `pessoas` (dados do usuário)
- CRUD completo para usuários com suporte a:
  - Criação de novos usuários com hash de senha via `Passlib`
  - Edição de usuários (atualização de nome, empresa, cargo, senha opcional)
  - Exclusão de usuários com transações
  - Listagem com DataTables server-side

#### 2. **Departments Module** (`Modules/Departments/`)
- Gerenciamento de departamentos/cargos com permissões granulares
- Atribuição de permissões por departamento (muitos-para-muitos)
- Proteção de departamentos globais contra edição
- Auto-geração de descrições via **Google Gemini AI** (1.5-flash)
- Exclusão cascata: reatribui pessoas para cargo "nenhum" antes de deletar

#### 3. **Empresas Module** (`Modules/Empresas/`)
- Gerenciamento completo de empresas
- Máscara dinâmica para CNPJ/CPF (11 ou 14 dígitos)
- Data de adesão automática
- Filtro de busca inteligente (apenas números)
- Plano de ativação (Ativo/Inativo)

#### 4. **Permissões Module** (`Modules/Permissoes/`)
- Sistema centralizado de permissões
- Verificação de acesso (superadmin, user-specific, company-scoped)
- Permissões: `user.view`, `user.create`, `user.edit`, `user.delete`
- Permissões: `empresas.view`, `empresas.create`, `empresas.edit`, `empresas.delete`
- Permissões: `departments.view`, `departments.create`, `departments.edit`, `departments.delete`

#### 5. **Dashboard Modules** (`Modules/Dashboard/Controllers/Modules.php`)
- Gerenciamento visual de módulos por empresa
- Listagem de módulos disponíveis por empresa
- Visualização de módulos para uma empresa específica
- Integração com Dashboard controller
- Permissão: `mod.modules.view`

---

## 🏗️ Arquitetura

### Padrão de Organização: Model-Service-Controller (MSC)

```
Módulo/
├── Config/
│   └── Routes.php          # Rotas específicas do módulo
├── Controllers/
│   ├── ModuleName.php      # Controller principal (Dashboard)
│   └── API.php             # Endpoints RESTful
├── Models/
│   └── *.php               # Modelos de banco de dados
├── Services/
│   └── *.Service.php       # Lógica de negócio
├── Views/
│   ├── CRUD/
│   │   ├── Read.php        # Listagem com DataTables
│   │   └── CreateEdit.php  # Formulário (create/edit)
│   └── Partials/
└── Libraries/
    └── Menu.php            # Integração com menu lateral
```

### Fluxo de Requisição

```
Usuário → Form → API Controller → Service Layer → Models → Database
                      ↓
              Validação de Permissões
                      ↓
              Transação (se aplicável)
                      ↓
              Redirect com Flashdata
```

---

## 🔐 Segurança

### Validações Implementadas

1. **Autenticação via Permissões**
   - Todas as operações verificam `user_has_permission()`
   - Métodos críticos usam `user_is_superadmin()`

2. **Validação de Propriedade (Tenant-Safe)**
   - Usuários não-superadmin só veem seus próprios registros
   - Queries filtradas por `id_empresa` da sessão
   - Validação em listagens (LoginAPI.userList) e operações críticas

3. **Proteção de Usuário Logado**
   - Usuário logado não pode se editar ou deletar a si mesmo
   - Verificação via `session()->get('usuario')` vs `id_usuario_login`
   - Botões de ação desabilitados na listagem para o próprio usuário

4. **Permissões Granulares**
   - Superadmin: acesso a TODAS as permissões
   - Usuário comum: acesso apenas às permissões que ele próprio possui
   - Criação de departamento: só assina permissões que o criador tem

5. **Validação de Empresa**
   - Operações de delete/read respeitam `id_empresa` do usuário logado
   - Proteção em DepartmentService, EmpresasService, LoginAPI

6. **Hash de Senha**
   - `Passlib::hashPassword()` para todas as novas senhas
   - Senha opcional em atualizações (permite reset sem obrigatoriedade)

7. **Proteção CSRF**
   - CodeIgniter gerencia automaticamente tokens

8. **Transações Atômicas**
   - Operações multi-tabela usam `$db->transBegin()`
   - Rollback automático em exceções

---

## 🗄️ Banco de Dados

### Tabelas Principais

| Tabela | Descrição |
|--------|-----------|
| `login` | Credenciais de usuário (id_usuario, usuario, senha_hash, id_empresa) |
| `pessoas` | Dados do usuário (id_pessoa, id_usuario_login, nome_completo, id_cargo, id_empresa) |
| `cargos` | Departamentos (id_cargo, nome, descricao, id_empresa, is_global, readonly) |
| `cargos_permissoes` | Associação cargo-permissão (muitos-para-muitos) |
| `permissoes` | Catálogo de permissões (id_permissao, slug, descricao) |
| `empresas` | Empresas (id_empresa, cnpj, razao_social, plano_ativo, data_adesao) |

### Relacionamentos Chave

```
login (1) ──────── (N) pessoas
  ↓
cargos (1) ──────── (N) pessoas
  ↓
empresas (1) ────── (N) pessoas
           └────── (N) cargos

cargos (N) ────── (M) permissoes (via cargos_permissoes)
```

---

## 🚀 Instalação e Setup

### Pré-requisitos

- PHP 8.1+
- MySQL/MariaDB
- Composer
- XAMPP ou similar (para desenvolvimento local)

### Passos de Instalação

1. **Clone o repositório**
   ```bash
   git clone <seu-repo> rodsys
   cd rodsys
   ```

2. **Instale dependências**
   ```bash
   composer install
   ```

3. **Configure o ambiente (use o .env.example)**
   ```bash
   cp env .env.example
   # Edite .env.example com suas credenciais de banco de dados
   # e API_KEY. Após isto, renomeie para .env
   ```

4. **Importe o banco de dados**
   ```bash
   Use o start_rodsys.sql para pré-configurar.
   mysql -u root -p rodsys < database.sql
   ```

5. **Configure o virtual host (recomendado)**
   ```apache
   <VirtualHost *:80>
       ServerName rodsys.local
       DocumentRoot "C:/xampp/htdocs/rodsys/public"
   </VirtualHost>
   ```

6. **Inicie o servidor**
   ```bash
   php spark serve
   ```
   Acesse: `http://localhost:8080`

---

## 📱 Interface e UX

### Frontend Technologies

- **Framework CSS**: Bootstrap 5
- **Data Tables**: DataTables.js v2.3.4 (server-side)
- **Notificações**: SweetAlert2
- **Ícones**: Font Awesome 5 (fa-solid)
- **Máscara de Entrada**: jQuery com regex
- **Linguagem**: Português (pt-BR) via CDN DataTables

### Componentes Principais

#### Listagens (Read.php)
- Server-side DataTables com busca, paginação e ordenação
- Formatter customizado para CNPJ/CPF (11 ou 14 dígitos)
- Botões de ação (Editar, Deletar) com ícones
- Confirmação de exclusão via SweetAlert

#### Formulários (CreateEdit.php)
- Modo create/edit detectado via `$is_editing`
- Preenchimento automático com `old('field')` após erro
- Campos readonly em edit (ex: login)
- Senha opcional em edit ("deixe vazio para manter a atual")
- Validação de entrada em tempo real (client + server)

---

## 💾 Funcionalidades Avançadas

### 1. Busca Inteligente
- **Empresas**: Busca filtra apenas números/pontos, extrai CPF/CNPJ válidos
- **Departamentos**: Busca por nome/descrição
- **Usuários**: Busca por nome/login

### 2. AI Integration (Gemini)
- Auto-geração de descrição de departamento ao focar no campo
- Modelo: `gemini-1.5-flash`
- Header: `x-goog-api-key`

### 3. Validação de Documentos
- **CPF**: 11 dígitos + validação de check-digit (algoritmo modulo 11)
- **CNPJ**: 14 dígitos + validação de check-digit (algoritmo modulo 11)
- Máscara dinâmica: `XXX.XXX.XXX-XX` (CPF) ou `XX.XXX.XXX/XXXX-XX` (CNPJ)

### 4. Transações Multi-Tabela
```php
// Exemplo: Criar departamento com permissões
$db->transStart();
  → Insert `cargos`
  → Batch insert `cargos_permissoes`
$db->transComplete();
```

### 5. Menu Dinâmico
- Menu sidebar gerado conforme permissões do usuário
- Integração via `Libraries/Menu.php` por módulo
- Ícones Font Awesome personalizados

---

## 🔄 Fluxo de CRUD

### CREATE (Criar Usuário)

```
GET /dashboard/acessos/usuarios/novo
  ↓
UserManagement::renderCreateEditUser()
  ↓
Exibe: Login/CreateEdit.php (is_editing=false)
  ↓
POST /login/api/usuarios (form submit)
  ↓
LoginAPI::create() → UserManagement::createUser()
  ↓
✅ Redirect + flashdata success → /dashboard/acessos/usuarios
❌ Redirect + flashdata error → back com form values
```

### READ (Listar Usuários)

```
GET /dashboard/acessos/usuarios
  ↓
UserManagement::renderManageUsers()
  ↓
Exibe: Login/ManageUsers.php (DataTable vazio)
  ↓
DataTables AJAX → /login/api/usuarios/list
  ↓
LoginAPI::userList() → SSP::complex() (server-side)
  ↓
Retorna: JSON com dados + formatadores (ícones, ações)
```

### UPDATE (Editar Usuário)

```
GET /dashboard/acessos/usuarios/:id
  ↓
UserManagement::renderCreateEditUser($id)
  ↓
Exibe: Login/CreateEdit.php (is_editing=true, prefilled)
  ↓
POST /login/api/usuarios/:id (form submit)
  ↓
LoginAPI::update($id) → UserManagement::updateUser()
  ↓
✅ Redirect + flashdata success → /dashboard/acessos/usuarios
❌ Redirect + flashdata error + withInput() → back com form values
```

### DELETE (Deletar Usuário)

```
Click delete button (DataTable)
  ↓
SweetAlert confirmation
  ↓
AJAX DELETE /login/api/usuarios/:id
  ↓
LoginAPI::deleteUser($id) → UserManagement::deleteUser()
  ↓
Validação: usuário não pode se deletar a si mesmo
  ↓
✅ SweetAlert success → Reload page
❌ SweetAlert error → Show message
```

### UPDATE (Editar Departamento)

```
GET /dashboard/departamentos/:id
  ↓
DepartmentService::renderCreateEditDepartment($id)
  ↓
Validação: usuário pode ver apenas permissões que possui (ou todas se superadmin)
  ↓
Exibe: Departments/CreateEdit.php (is_editing=true, prefilled)
  ↓
POST /departments/api/update/:id (form submit)
  ↓
DepartmentService::updateDepartment()
  ↓
Validação: departamento pertence à empresa do usuário logado
Validação: não é um departamento global ou readonly
  ↓
✅ Redirect + flashdata success
❌ Redirect + flashdata error
```

---

## 🔒 Multi-Tenant e Isolamento de Dados

### Estratégia de Isolamento por Empresa

O RODSYS implementa isolamento de dados em nível de aplicação:

1. **Cada usuário tem um `id_empresa` na sessão**
   ```php
   $id_empresa = session()->get('id_empresa');
   ```

2. **Queries filtram automaticamente por empresa**
   ```php
   // Listar usuários apenas da empresa do usuário logado
   $usuarios = $usuarioModel->where('id_empresa', $id_empresa)->findAll();
   ```

3. **Superadmin pode visualizar todas as empresas**
   ```php
   if (!$permissionsModel->user_is_superadmin()) {
       $whereClause = "e.id_empresa = " . intval($id_empresa);
   }
   ```

4. **Departamentos são isolados por empresa**
   - `cargos.id_empresa` define a propriedade
   - Departamentos globais (`is_global=1`) visíveis por todos
   - Readonly departments não podem ser editados

5. **Operações críticas validam propriedade**
   - Delete de departamento: valida se pertence à empresa do usuário
   - Update de usuário: valida se está na mesma empresa
   - Reatribuição de pessoas: usa `WHERE id_empresa`

### Proteção de Usuário Logado

Implementação adicional:

1. **Usuário não pode deletar a si mesmo**
   ```php
   if ($id_usuario_logado != $row['id_usuario_login']) {
       // Mostrar botão delete
   }
   ```

2. **Usuário não pode editar a si mesmo** (opcional, implementado em validação)
   - Verificação antes de mostrar botão "Editar"

3. **Permissões segmentadas por empresa**
   - `mod.user.company.listall` = permite listar usuários de outras empresas
   - Sem essa permissão, vê apenas da sua empresa

---

## 🛡️ Permissões Granulares do Desenvolvedor

### Criação de Departamento com Permissões Restritas

Quando um usuário cria um departamento, ele só pode atribuir permissões que ele próprio possui:

```php
// No DepartmentService::renderCreateEditDepartment()
if($permissionsModel->user_is_superadmin()) {
    $data['permissoes'] = $permissionsModel->findAll(); // TODAS
} else {
    $data['permissoes'] = $permissionsModel->listMyPermissions(); // Apenas dele
}
```

### Busca em DataTables com Validação de Empresa

```php
// No LoginAPI::userList()
// Filtro automático por empresa
if (! $this->permissionsModel->user_is_superadmin() && 
    !$this->permissionsModel->user_has_permission('mod.user.company.listall')) {
    $whereClause = "e.id_empresa = " . intval($id_empresa_logada);
}

// Busca em múltiplos campos
$whereClause .= " AND (
    pessoas.nome_completo LIKE '%$search%' OR
    c.nome LIKE '%$search%' OR
    e.razao_social LIKE '%$search%'
)";
```

---

## 🛠️ Desenvolvimento

### Adicionar Novo Módulo

1. **Criar estrutura de pastas**
   ```bash
   mkdir -p Modules/NovoModulo/{Config,Controllers,Models,Services,Views/CRUD,Libraries}
   ```

2. **Criar rota** (`Config/Routes.php`)
   ```php
   $routes->group('dashboard', ['namespace' => 'Modules\NovoModulo\Controllers'], function($routes) {
       $routes->get('novo-modulo', 'NovoModulo::index');
       $routes->get('novo-modulo/(:num)', 'NovoModulo::editar/$1');
   });
   ```

3. **Criar Service** (padrão MSC)
   ```php
   namespace Modules\NovoModulo\Services;
   
   class NovoModuloService {
       protected function renderManage($data = []) { }
       protected function renderCreateEdit($data = []) { }
       public function create($params) { }
   }
   ```

4. **Criar Menu** (`Libraries/Menu.php`)
   ```php
   $menu->addMenuItem('Label', 'rota', 'fa-solid fa-icon', 'Parent', 'active-pattern');
   ```

---

## 📚 Documentação de Código

### Model Example
```php
// Modules/Empresas/Models/EmpresasModel.php

namespace Modules\Empresas\Models;

class EmpresasModel extends Model {
    // Retorna apenas empresas do usuário logado (multi-tenant)
    public function listForMe() {
        $id_empresa = session()->get('id_empresa');
        return $this->where('id_empresa', $id_empresa)->findAll();
    }
}
```

### Service Example
```php
// Modules\Empresas\Services\EmpresasService

public function createEmpresa($cnpj, $razao_social, $plano_ativo = 0) {
    $db->transStart();
    try {
        // Validar permissão
        // Inserir com transação
        $db->transComplete();
        return ['success' => true, 'message' => '...'];
    } catch (\Exception $e) {
        $db->transRollback();
        return ['success' => false, 'message' => $e->getMessage()];
    }
}
```

### View Example (CreateEdit.php Pattern)
```php
<?php if (!empty($is_editing)): ?>
    <input type="hidden" name="is_editing" value="1" />
    <input type="hidden" name="id_registro" value="<?= esc($registro['id']) ?>" />
<?php endif; ?>

<input class="form-control" name="inputNome" 
       value="<?= esc(old('inputNome', $registro['nome'] ?? '')) ?>" 
       <?= (!empty($is_editing) && isset($registro['readonly'])) ? 'readonly' : '' ?> />
```

---

## 🧪 Testes

### Rodar Testes Unitários
```bash
composer test
```

### Testes Manuais Recomendados

- [ ] Criar usuário com todas as validações
- [ ] Editar usuário (nome, cargo, empresa, senha)
- [ ] Deletar usuário (cascata)
- [ ] Verificar permissões bloqueiam ações
- [ ] Testar busca em DataTables
- [ ] Validar máscara CNPJ/CPF
- [ ] Confirmar flashdata em success/error

---

## 🔍 Troubleshooting

### "Unknown method getVar()"
**Solução**: Usar `$this->request->getPost('field')` ou `$this->request->getGet('field')` conforme o tipo de requisição.

### "User not found" ao editar
**Causa**: ID do usuário inválido ou usuário deletado.
**Solução**: Verificar se `id_usuario_login` está correto na URL.

### DataTable não carrega
**Causa**: AJAX retorna erro 500 ou permissão negada.
**Solução**: Verificar logs em `/writable/logs/`, validar permissão `user.view`.

### Senha não atualiza
**Causa**: Campo `inputSenha` vazio em modo edição (esperado).
**Solução**: Preencher com nova senha ou deixar em branco para manter atual.

---

## 📦 Dependências Principais

```json
{
  "codeigniter4/framework": "^4.0",
  "jquery": "^3.7.1",
  "bootstrap": "^5.x",
  "datatables": "^2.3.4",
  "sweetalert2": "^latest",
  "fontawesome": "^5.x"
}
```

---

## 📄 Licença

MIT License - veja arquivo `LICENSE` para detalhes.

---

## 👥 Contribuidores

- Desenvolvedor Principal: Rodrigo Lopes @RodrigoLCA
- Data de Início: 15/11/2025
- Status: ✅ Em Produção / 🔧 Em Desenvolvimento

---

## 📞 Suporte

Para dúvidas ou reportar bugs, abra uma **Issue** no GitHub ou entre em contato pelo email: [rodrigolca@gmail.com] com o assunto

---

## 🚧 Roadmap Futuro

- [ ] Autenticação 2FA
- [ ] Integração com SSO (LDAP/OAuth2)
- [ ] Auditoria de ações (log de mudanças)
- [ ] Relatórios (PDF/Excel)
- [ ] Dashboard com gráficos
- [ ] API pública com rate-limiting

---

**Última atualização**: 15 de Novembro, 2025
