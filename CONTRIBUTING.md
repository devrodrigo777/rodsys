# 🤝 Guia de Contribuição - RODSYS

Obrigado por considerar contribuir para o RODSYS! Este documento fornece orientações e boas práticas.

---

## 📋 Antes de Começar

1. **Fork o repositório**
2. **Clone seu fork** localmente
3. **Configure o ambiente** conforme `README_RODSYS.md`
4. **Crie uma branch** para sua feature: `git checkout -b feature/sua-feature`

---

## 🏗️ Estrutura de Código

### Padrão MSC (Model-Service-Controller)

Sempre siga este padrão para novas features:

```
Módulo/
├── Models/          # Interação com banco de dados
├── Services/        # Lógica de negócio + validações
├── Controllers/     # Roteamento + permissões
└── Views/           # Renderização (Blade/HTML)
```

### Exemplo: Adicionar Feature "Exportar CSV"

**1. Criar método no Model**
```php
// Models/EmpresasModel.php
public function getForExport() {
    return $this->select('id_empresa, cnpj, razao_social, plano_ativo')
                ->where('id_empresa', session()->get('id_empresa'))
                ->findAll();
}
```

**2. Adicionar lógica na Service**
```php
// Services/EmpresasService.php
public function exportToCSV() {
    if (!$permissionsModel->user_has_permission('empresas.export')) {
        return ['success' => false, 'message' => 'Sem permissão'];
    }
    
    $empresas = (new EmpresasModel())->getForExport();
    // Lógica de export...
    return ['success' => true, 'file' => 'empresas_export.csv'];
}
```

**3. Expor via Controller/API**
```php
// Controllers/API.php
public function export() {
    $result = (new EmpresasService())->exportToCSV();
    if ($result['success']) {
        return $this->respond($result);
    }
    return $this->fail($result['message']);
}
```

---

## ✅ Checklist para Pull Request

Antes de fazer PR, certifique-se de:

- [ ] Código segue PSR-12 (CodeIgniter style)
- [ ] Adicionou comentários em métodos públicos/complexos
- [ ] Validou entrada do usuário
- [ ] Verificou permissões (`user_has_permission()`)
- [ ] Usou transações para operações multi-tabela
- [ ] Testou localmente (form submit, API, etc)
- [ ] Sem `dd()`, `print_r()` ou `var_dump()` no código final
- [ ] Mensagens de erro em português
- [ ] Flashdata com chaves padronizadas: `{module}.feedback.{success|error}`

---

## 🔒 Segurança

### Obrigatório

1. **Sempre verificar permissões no início do método**
   ```php
   if (!$permissionsModel->user_has_permission('recurso.acao')) {
       return $this->fail('Sem permissão', 403);
   }
   ```

2. **Sanitizar entrada**
   ```php
   $nome = strtoupper(trim($this->request->getPost('nome')));
   // ou usar esc() na view
   <input value="<?= esc($data['nome']) ?>" />
   ```

3. **Usar transações para operações críticas**
   ```php
   $db->transStart();
   try {
       // múltiplas operações
       $db->transComplete();
   } catch {
       $db->transRollback();
   }
   ```

4. **Validar relacionamentos de tenant**
   ```php
   // Usuário não-superadmin SÓ vê dados da sua empresa
   $empresa = $this->model->where('id_empresa_donwer', $id_empresa)->find($id);
   ```

5. **Validar que usuário não opera em si mesmo** (delete/editar)
   ```php
   $id_usuario_logado = session()->get('usuario');
   if ($id_usuario_logado == $id_usuario_target) {
       return $this->fail('Você não pode se deletar', 403);
   }
   ```

6. **Usar permissões granulares em operações críticas**
   ```php
   // Criar departamento permite APENAS permissões que o criador possui
   if($permissionsModel->user_is_superadmin()) {
       $data['permissoes'] = $permissionsModel->findAll();
   } else {
       $data['permissoes'] = $permissionsModel->listMyPermissions();
   }
   ```

7. **Filtrar busca por empresa em DataTables**
   ```php
   // LoginAPI::userList() - exemplo correto
   if (! $this->permissionsModel->user_is_superadmin()) {
       $whereClause = "e.id_empresa = " . intval($id_empresa_logada);
   }
   // Busca em múltiplos campos
   $whereClause .= " AND (pessoas.nome_completo LIKE '%$search%' OR ...)";
   ```

---

## 💻 Padrões de Codificação

### Nomenclatura

| Elemento | Padrão | Exemplo |
|----------|--------|---------|
| **Classes** | PascalCase | `EmpresasService`, `CargosModel` |
| **Métodos** | camelCase | `createWithPermissions()`, `listForMe()` |
| **Variáveis** | snake_case | `$id_empresa`, `$nome_completo` |
| **Constantes** | UPPER_SNAKE | `MAX_LOGIN_ATTEMPTS` |
| **Banco de Dados** | snake_case | `id_usuario_login`, `cargos_permissoes` |

### Comentários

```php
/**
 * Cria uma nova empresa com validações.
 * 
 * FLUXO:
 * 1. Valida CNPJ
 * 2. Verifica duplicidade
 * 3. Insere com transação
 * 
 * @param string $cnpj CNPJ (14 dígitos)
 * @param string $razao_social Nome da empresa
 * @return array ['success' => bool, 'message' => string, 'id' => int]
 */
public function create($cnpj, $razao_social) {
    // implementação
}
```

### Validação de Input

```php
// ✅ BOM - usar validação nativa
$rules = [
    'inputNome' => 'required|min_length[3]|max_length[255]',
    'inputCnpj' => 'required|regex_match[/^\d{14}$/]',
];

if (!$this->validate($rules)) {
    return $this->fail($this->validator->getErrors());
}

// ❌ RUIM - confiar no cliente
$nome = $_POST['nome'];
```

---

## 🎨 Frontend Guidelines

### HTML/Views

- Use Bootstrap 5 classes
- Sempre escape dados: `<?= esc($data) ?>`
- Use `old('fieldName')` para repopular forms após erro
- Adicione `data-bs-toggle="tooltip"` para ícones com contexto

### JavaScript (jQuery)

- Use `window.BURL` em vez de `base_url()`
- Sempre confirme ações destrutivas com SweetAlert2
- Prefira `$('.selector').on('event', fn)` over `onclick="fn()"`

### CSS

- Use classes Bootstrap (não inline styles)
- Crie CSS custom em `/public/assets/css/custom.css`
- Siga mobile-first approach

---

## 🧪 Testes

### Testes Manuais Esperados

Para um novo CRUD:

```bash
# 1. CREATE
POST /modulo/api/create
→ Deve retornar 201 ou redirect com flashdata success

# 2. READ
GET /dashboard/modulo
→ DataTable deve carregar com dados

# 3. UPDATE
POST /modulo/api/update/:id
→ Deve atualizar e redirecionar

# 4. DELETE
DELETE /modulo/api/delete/:id
→ Deve deletar e retornar sucesso
```

### Permissões

```bash
# Teste com usuário SEM permissão
GET /dashboard/modulo
→ Deve redirecionar ou mostrar erro

# Teste com superadmin
GET /dashboard/modulo
→ Deve permitir acesso
```

---

## 📝 Commits e PRs

### Mensagens de Commit

```bash
# Formato
<tipo>(<escopo>): <descrição>

# Tipos: feat, fix, docs, style, refactor, test, chore
# Exemplos:
git commit -m "feat(empresas): add CNPJ validator"
git commit -m "fix(login): hash password on update"
git commit -m "docs: add contribution guide"
```

### Pull Request

```markdown
## 📝 Descrição
Brevemente descreva o que foi alterado.

## 🎯 Tipo de Mudança
- [ ] Bug fix
- [ ] Nova feature
- [ ] Breaking change
- [ ] Documentação

## ✅ Checklist
- [ ] Testei localmente
- [ ] Sem erros de lint
- [ ] Permissões validadas
- [ ] Transações implementadas
- [ ] Comentários adicionados

## 📸 Screenshots (se aplicável)
[Cole screenshots aqui]
```

---

## 🐛 Reportar Bugs

Use a template:

```markdown
## 🐛 Descrição do Bug
[Descreva o problema]

## 📋 Passos para Reproduzir
1. Faça...
2. Clique em...
3. Observe...

## 🔍 Comportamento Esperado
[O que deveria acontecer]

## 📊 Ambiente
- PHP: 8.1.x
- Browser: Chrome 120
- OS: Windows 10
```

---

## 📚 Referências

- [CodeIgniter 4 Docs](https://codeigniter.com/user_guide/)
- [Bootstrap 5 Docs](https://getbootstrap.com/docs/5.0/)
- [DataTables Docs](https://datatables.net/)
- [PSR-12 Standard](https://www.php-fig.org/psr/psr-12/)

---

## 🎓 Aprendizado Recomendado

- [ ] Ler `README_RODSYS.md` completamente
- [ ] Analisar estrutura de um módulo existente (Empresas)
- [ ] Testar CRUD de usuários (criar, editar, deletar)
- [ ] Entender fluxo de permissões
- [ ] Familiarizar-se com transações MySQL

---

## ❓ Dúvidas?

- Abra uma **Issue** para discussões
- Comente no PR para dúvidas específicas
- Entre em contato via email: [seu-email@domain.com]

---

**Obrigado por contribuir! 🚀**
