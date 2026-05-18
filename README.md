# TDE03 Imobiliaria

<p align="center">
  Sistema web academico para gestao de imobiliaria em <b>PHP + PostgreSQL</b>,
  com CRUD completo e interface retro-classica.
</p>

<p align="center">
  <img alt="PHP" src="https://img.shields.io/badge/PHP-8%2B-777bb4?style=for-the-badge&logo=php&logoColor=white">
  <img alt="PostgreSQL" src="https://img.shields.io/badge/PostgreSQL-12%2B-336791?style=for-the-badge&logo=postgresql&logoColor=white">
  <img alt="Status" src="https://img.shields.io/badge/Status-Ativo-2ea44f?style=for-the-badge">
  <img alt="Licenca" src="https://img.shields.io/badge/Licenca-Academico-555?style=for-the-badge">
</p>

---

## Visao Geral

Este projeto simula o fluxo de uma imobiliaria, cobrindo os modulos principais:

- Imoveis
- Proprietarios
- Corretores
- Clientes
- Contratos
- Visitas

Arquitetura enxuta em PHP puro (MVC basico):

- `index.php`: front controller e roteamento por `entidade` + `acao`
- `controller/`: regra de negocio
- `dao/`: persistencia com PDO
- `model/`: entidades
- `view/`: formularios e listagens

---

## Stack Tecnica

- **Backend:** PHP 8+
- **Banco:** PostgreSQL 12+
- **Acesso a dados:** PDO (`pdo_pgsql`)
- **UI:** CSS proprio (`assets/style.css`) com visual retro

---

## Setup Rapido

### 1) Criar usuario e banco

```bash
psql -d postgres -c "CREATE ROLE marquin WITH LOGIN PASSWORD 'Senha123!';"
psql -d postgres -c "CREATE DATABASE imobiliaria OWNER marquin;"
```

### 2) Criar estrutura de tabelas

```bash
psql -U marquin -d imobiliaria -f "./banco_postgresql.sql"
```

### 3) Rodar migracoes (quando necessario)

```bash
psql -U marquin -d imobiliaria -f "./migracao.sql"
```

### 4) Iniciar servidor local

```bash
php -S localhost:8000
```

Acesse:

- App: `http://localhost:8000/index.php`
- Login: `http://localhost:8000/login.php`

---

## Autenticacao

O sistema exige sessao para acessar o painel principal.

Credencial inicial (seed no schema/migracao):

- **E-mail:** `admin@imobiliaria.com`
- **Senha:** `123456`

> Nota: por requisito da disciplina, a senha esta em texto simples (sem hash).

---

## Rotas

### Listagens

- `index.php?entidade=imovel&acao=listar`
- `index.php?entidade=proprietario&acao=listar`
- `index.php?entidade=corretor&acao=listar`
- `index.php?entidade=cliente&acao=listar`
- `index.php?entidade=contrato&acao=listar`
- `index.php?entidade=visita&acao=listar`

### Fluxo CRUD padrao

- Novo: `index.php?entidade=<entidade>&acao=novo`
- Editar: `index.php?entidade=<entidade>&acao=editar&id=<id>`
- Excluir: `index.php?entidade=<entidade>&acao=excluir&id=<id>`

---

## Estrutura do Projeto

```text
imobiliaria/
├── assets/
│   └── style.css
├── config/
│   ├── conexao.php
│   └── schema_sqlite.sql
├── controller/
├── dao/
├── model/
├── view/
├── banco_postgresql.sql
├── migracao.sql
├── login.php
├── logout.php
└── index.php
```

---

## Diferenciais desta versao

- Interface retro personalizada, sem depender de framework CSS
- Campo de visita com `horario_preferencia`
- Login e sessao integrados ao fluxo CRUD
- Scripts SQL separados para criacao completa e migracao incremental

---

## Aviso Academico

Projeto voltado para estudo e demonstracao de conceitos (MVC simples, CRUD, sessao, SQL). Para producao, recomenda-se:

- hash de senha (`password_hash` / `password_verify`)
- variaveis de ambiente para credenciais
- validacao e sanitizacao mais robustas
- logs e monitoramento
