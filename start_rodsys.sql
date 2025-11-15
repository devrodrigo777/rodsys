-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 15/11/2025 às 23:26
-- Versão do servidor: 10.4.28-MariaDB
-- Versão do PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `rodsys`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `cargos`
--

CREATE TABLE `cargos` (
  `id_cargo` bigint(20) NOT NULL,
  `id_empresa` bigint(20) NOT NULL,
  `nome` varchar(128) NOT NULL,
  `descricao` text NOT NULL,
  `superadmin_only` tinyint(4) DEFAULT 0,
  `is_global` tinyint(4) NOT NULL DEFAULT 0,
  `slug` varchar(64) DEFAULT NULL COMMENT 'optional',
  `readonly` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `cargos`
--

INSERT INTO `cargos` (`id_cargo`, `id_empresa`, `nome`, `descricao`, `superadmin_only`, `is_global`, `slug`, `readonly`) VALUES
(1, 21593, 'Superadmin do ERP', 'Cargo SUPERADMIN Global do ERP RodSys. Possui todos os acessos liberados.', 1, 0, 'superadmin', 1),
(2, 21593, 'Nenhum', 'Cargo genérico. Associe este colaborador a um novo cargo!', 0, 1, 'nenhum', 1),
(3, 21593, 'Proprietario', 'Proprietário da empresa ou responsável pelo cadastro no RodSys.', 1, 1, 'proprietario', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `cargos_permissoes`
--

CREATE TABLE `cargos_permissoes` (
  `id_cargo` bigint(20) NOT NULL,
  `id_permissao` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `cargos_permissoes`
--

INSERT INTO `cargos_permissoes` (`id_cargo`, `id_permissao`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6);

-- --------------------------------------------------------

--
-- Estrutura para tabela `empresas`
--

CREATE TABLE `empresas` (
  `id_empresa` bigint(20) NOT NULL,
  `cnpj` varchar(18) NOT NULL,
  `razao_social` varchar(128) NOT NULL,
  `plano_ativo` int(11) DEFAULT NULL,
  `data_adesao` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `empresas`
--

INSERT INTO `empresas` (`id_empresa`, `cnpj`, `razao_social`, `plano_ativo`, `data_adesao`) VALUES
(21592, '33411974000117', 'FOCCO COMUNICACAO VISUAL', NULL, '2025-09-01'),
(21593, '00000000000000', 'RODSYS - ADMIN', 1, '2025-11-01'),
(21594, '04948857505', 'DANY CONFEITARIA', 1, '2025-11-15');

-- --------------------------------------------------------

--
-- Estrutura para tabela `login`
--

CREATE TABLE `login` (
  `id_usuario` bigint(20) NOT NULL,
  `id_empresa` bigint(20) NOT NULL,
  `senha_hash` varchar(256) NOT NULL,
  `ativo` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `usuario` varchar(64) NOT NULL,
  `updated_at` timestamp NULL DEFAULT NULL COMMENT 'data de atualização desse registro'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `login`
--

INSERT INTO `login` (`id_usuario`, `id_empresa`, `senha_hash`, `ativo`, `created_at`, `usuario`, `updated_at`) VALUES
(1, 21592, '$2y$10$XSW3UNdqgLFEvAImBw/fw.XhMVuFybAHcBAGVLEQ0cixiZRw9KECO', 1, '2025-11-09 03:28:05', 'FOCCO.COMUNICACAO', '2025-11-16 01:13:47'),
(2, 21593, '$2y$10$PBxmImQTo2lfyBu1HOT9eOeMzaehc1M1A5EM/UCxDcIzPjSV.31ay', 1, '2025-11-09 19:17:09', 'RODSYS.ADMIN', '2025-11-16 01:14:08'),
(6, 21593, '$2y$10$29DbZAZdkoSnq1G49dp4xe44woFW28gyfsdHEfgTNRXDrJWFPLpva', 1, '2025-11-13 04:56:08', 'STANLEY.RODSYS', '2025-11-16 01:14:33'),
(8, 21593, '$2y$10$7Pkc6958a32AL/ES9TxT9uAypgOCMPkONY46iV/8fYVwvRUopCg8C', 1, '2025-11-13 05:01:55', '123123', '2025-11-13 05:01:55');

-- --------------------------------------------------------

--
-- Estrutura para tabela `modulos`
--

CREATE TABLE `modulos` (
  `id` int(11) NOT NULL,
  `nome` varchar(64) NOT NULL,
  `namespace` varchar(64) NOT NULL,
  `diretorio` varchar(64) NOT NULL,
  `versao` varchar(16) NOT NULL,
  `ativo` tinyint(1) NOT NULL,
  `config` text NOT NULL,
  `order` int(11) NOT NULL DEFAULT 10
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `modulos`
--

INSERT INTO `modulos` (`id`, `nome`, `namespace`, `diretorio`, `versao`, `ativo`, `config`, `order`) VALUES
(1, 'Login', 'Login', 'Login', '0.0.1', 1, '', 1),
(2, 'Painel de Controle', 'Dashboard', 'Dashboard', '0.0.1', 1, '', 0),
(3, 'Departamentos', 'Departments', 'Departments', '0.0.1', 1, '', 10),
(4, 'Empresas', 'Empresas', 'Empresas', '0.0.1 IA', 1, '', 10);

-- --------------------------------------------------------

--
-- Estrutura para tabela `permissoes`
--

CREATE TABLE `permissoes` (
  `id_permissao` bigint(20) NOT NULL,
  `slug` varchar(64) NOT NULL,
  `descricao` varchar(128) DEFAULT NULL,
  `cliente_configuravel` tinyint(4) NOT NULL COMMENT 'True se pode ser marcada/desmarcada pelo cliente, ou False se são de módulos do ERP.',
  `is_superadmin` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `permissoes`
--

INSERT INTO `permissoes` (`id_permissao`, `slug`, `descricao`, `cliente_configuravel`, `is_superadmin`) VALUES
(1, 'system.dashboard.view', 'Renderização da Interface de Administração', 0, 1),
(2, 'system.login.view', 'Renderização da Interface de Login', 0, 1),
(3, 'user.create', 'Criar novo usuário', 1, 0),
(4, 'user.delete', 'Remover usuários', 1, 0),
(5, 'user.edit', 'Editar usuários', 1, 0),
(6, 'superadmin', 'Permissão de Superadmin', 0, 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `pessoas`
--

CREATE TABLE `pessoas` (
  `id_pessoa` bigint(20) NOT NULL,
  `id_empresa` bigint(20) NOT NULL,
  `id_usuario_login` bigint(20) NOT NULL,
  `nome_completo` varchar(255) NOT NULL,
  `id_cargo` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `pessoas`
--

INSERT INTO `pessoas` (`id_pessoa`, `id_empresa`, `id_usuario_login`, `nome_completo`, `id_cargo`) VALUES
(1, 21592, 1, 'RODRIGO LOPES', 2),
(2, 21593, 2, 'RODRIGO', 1),
(4, 21593, 6, 'STANLEY', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `temas`
--

CREATE TABLE `temas` (
  `id` int(11) NOT NULL,
  `nome` varchar(64) NOT NULL,
  `diretorio` varchar(32) NOT NULL,
  `descricao` text NOT NULL,
  `ativo` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `temas`
--

INSERT INTO `temas` (`id`, `nome`, `diretorio`, `descricao`, `ativo`) VALUES
(1, 'Rod Theme - Tema Padrão', 'rod', 'O tema padrão do sistema RodSys.', 1);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `cargos`
--
ALTER TABLE `cargos`
  ADD PRIMARY KEY (`id_cargo`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `id_empresa` (`id_empresa`);

--
-- Índices de tabela `cargos_permissoes`
--
ALTER TABLE `cargos_permissoes`
  ADD KEY `id_cargo` (`id_cargo`),
  ADD KEY `id_permissao` (`id_permissao`);

--
-- Índices de tabela `empresas`
--
ALTER TABLE `empresas`
  ADD PRIMARY KEY (`id_empresa`);

--
-- Índices de tabela `login`
--
ALTER TABLE `login`
  ADD PRIMARY KEY (`id_usuario`),
  ADD KEY `login_empresas_id_empresa` (`id_empresa`);

--
-- Índices de tabela `modulos`
--
ALTER TABLE `modulos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `namespace` (`namespace`);

--
-- Índices de tabela `permissoes`
--
ALTER TABLE `permissoes`
  ADD PRIMARY KEY (`id_permissao`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Índices de tabela `pessoas`
--
ALTER TABLE `pessoas`
  ADD PRIMARY KEY (`id_pessoa`),
  ADD UNIQUE KEY `id_usuario_login` (`id_usuario_login`),
  ADD KEY `id_empresa` (`id_empresa`),
  ADD KEY `id_cargo` (`id_cargo`);

--
-- Índices de tabela `temas`
--
ALTER TABLE `temas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `diretorio` (`diretorio`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `cargos`
--
ALTER TABLE `cargos`
  MODIFY `id_cargo` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de tabela `empresas`
--
ALTER TABLE `empresas`
  MODIFY `id_empresa` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21595;

--
-- AUTO_INCREMENT de tabela `login`
--
ALTER TABLE `login`
  MODIFY `id_usuario` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `modulos`
--
ALTER TABLE `modulos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `permissoes`
--
ALTER TABLE `permissoes`
  MODIFY `id_permissao` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `pessoas`
--
ALTER TABLE `pessoas`
  MODIFY `id_pessoa` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `temas`
--
ALTER TABLE `temas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `cargos`
--
ALTER TABLE `cargos`
  ADD CONSTRAINT `cargos_ibfk_1` FOREIGN KEY (`id_empresa`) REFERENCES `empresas` (`id_empresa`);

--
-- Restrições para tabelas `cargos_permissoes`
--
ALTER TABLE `cargos_permissoes`
  ADD CONSTRAINT `cargos_permissoes_ibfk_1` FOREIGN KEY (`id_cargo`) REFERENCES `cargos` (`id_cargo`),
  ADD CONSTRAINT `cargos_permissoes_ibfk_2` FOREIGN KEY (`id_permissao`) REFERENCES `permissoes` (`id_permissao`);

--
-- Restrições para tabelas `login`
--
ALTER TABLE `login`
  ADD CONSTRAINT `login_empresas_id_empresa` FOREIGN KEY (`id_empresa`) REFERENCES `empresas` (`id_empresa`);

--
-- Restrições para tabelas `pessoas`
--
ALTER TABLE `pessoas`
  ADD CONSTRAINT `pessoas_ibfk_1` FOREIGN KEY (`id_empresa`) REFERENCES `empresas` (`id_empresa`),
  ADD CONSTRAINT `pessoas_ibfk_2` FOREIGN KEY (`id_usuario_login`) REFERENCES `login` (`id_usuario`),
  ADD CONSTRAINT `pessoas_ibfk_3` FOREIGN KEY (`id_cargo`) REFERENCES `cargos` (`id_cargo`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
