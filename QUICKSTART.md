# ⚡ Quick Start - RODSYS

Guia rápido para colocar o projeto rodando em 5 minutos.

---

## 🚀 Instalação Rápida

### Pré-requisitos
- PHP 8.1+ (com MySQL enabled)
- MySQL/MariaDB
- Composer
- Git

### Passo 1: Clone e Configure
```bash
# Clone o repositório
git clone <seu-repo-url> rodsys
cd rodsys

# Instale dependências
composer install

# Configure variáveis de ambiente
cp env .env
```

### Passo 2: Configure o .env
Edite `/.env` e ajuste:
```env
app.baseURL = 'http://rodsys.local/'
database.default.hostname = localhost
database.default.database = rodsys
database.default.username = root
database.default.password = ''
```

### Passo 3: Banco de Dados
```bash
# Crie o banco
mysql -u root -p -e "CREATE DATABASE rodsys CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Importe schema (se existir)
mysql -u root -p rodsys < database.sql

# Ou execute migrações
php spark migrate
```

### Passo 4: Inicie o Servidor
```bash
php spark serve
```
Acesse: **http://localhost:8080**

---

## 📁 Estrutura Essencial

```
rodsys/
├── app/              # Framework base
├── Modules/          # Seus módulos
│   ├── Login/        # Autenticação
│   ├── Departments/  # Cargos
│   └── Empresas/     # Empresas
├── public/           # Assets (CSS, JS, imagens)
├── writable/         # Logs, cache, uploads
├── env               # Variáveis de ambiente
└── composer.json     # Dependências
```

---

## 🔧 Configuração Recomendada (XAMPP)

### Virtual Host (Windows)
Edite `C:\xampp\apache\conf\extra\httpd-vhosts.conf`:

```apache
<VirtualHost *:80>
    ServerName rodsys.local
    DocumentRoot "C:/xampp/htdocs/rodsys/public"
    <Directory "C:/xampp/htdocs/rodsys/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Edite `C:\Windows\System32\drivers\etc\hosts`:
```
127.0.0.1  rodsys.local
```

Reinicie Apache: Services → Apache2.4 (Restart)

Acesse: **http://rodsys.local**

---

## 👤 Primeiro Login

Padrão de entrada deve estar documentado em `Modules/Login/`:

```
Usuário (Login): admin
Senha: admin123
Empresa: 1
```

---

## 🛠️ Comandos Úteis

```bash
# Gerar controller
php spark make:controller NomeController -m Modules/MeuModulo

# Criar migration
php spark make:migration CreateTabelaTable --table usuarios

# Rodar migrações
php spark migrate

# Reverter última migration
php spark migrate:rollback

# Ver rotas registradas
php spark routes

# Limpar cache
php spark cache:clear

# Testes unitários
composer test
```

---

## 🐛 Troubleshooting Rápido

| Problema | Solução |
|----------|---------|
| **"Não consegue conectar ao banco"** | Verifique credenciais em `.env` |
| **"Class not found"** | Execute `composer dump-autoload` |
| **"Permission denied" em writable/** | `chmod -R 777 writable/` (Linux/Mac) |
| **"Public folder not found"** | Configure Virtual Host apontando para `/public` |
| **DataTable não carrega** | Verifique `/writable/logs/` para erros |

---

## 📚 Próximos Passos

1. **Leia** `README_RODSYS.md` para entender arquitetura
2. **Explore** `Modules/Empresas/` para ver exemplo completo
3. **Teste** criar/editar/deletar empresa
4. **Estude** `Services/EmpresasService.php` para padrão MSC
5. **Contribua** seguindo `CONTRIBUTING.md`

---

## 🔐 Permissões Iniciais

Certifique-se que as permissões abaixo existem no banco:

```sql
INSERT INTO permissoes (slug, descricao) VALUES
('user.view', 'Visualizar Usuários'),
('user.create', 'Criar Usuários'),
('user.edit', 'Editar Usuários'),
('user.delete', 'Deletar Usuários'),
('empresas.view', 'Visualizar Empresas'),
('empresas.create', 'Criar Empresas'),
('empresas.edit', 'Editar Empresas'),
('empresas.delete', 'Deletar Empresas'),
('departments.view', 'Visualizar Departamentos'),
('departments.create', 'Criar Departamentos'),
('departments.edit', 'Editar Departamentos'),
('departments.delete', 'Deletar Departamentos');
```

---

## 💡 Dicas

- Use `CTRL + Shift + Delete` no browser para limpar cache
- Ative xDebug para debug mais fácil
- Use `$db->getLastQuery()` para debug de queries
- Verifique `/writable/logs/` para mensagens de erro

---

**Pronto! Você está com o RODSYS rodando! 🎉**

Para mais detalhes, veja `README_RODSYS.md`
