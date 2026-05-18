# Sistema Imobiliaria (PHP + PostgreSQL)

Projeto academico de CRUD para uma imobiliaria, com modulo de:

- Imoveis
- Proprietarios
- Corretores
- Clientes
- Contratos
- Visitas

Arquitetura simples em PHP puro (MVC basico):

- `index.php`: front controller e roteamento por `entidade` + `acao`
- `controller/`: regra de aplicacao
- `dao/`: acesso ao banco via PDO
- `model/`: entidades
- `view/`: formularios e listagens

## Requisitos

- PHP 8.0+
- Extensao PDO + `pdo_pgsql`
- PostgreSQL 12+

## Configuracao do banco

Atualmente a conexao esta fixa em `config/conexao.php`:

- banco: `imobiliaria`
- usuario: `marquin`
- senha: `Senha123!`

Se quiser mudar, edite a linha da conexao PDO em `config/conexao.php`.

## Subindo o banco (zero)

1. Criar role:

```bash
psql -d postgres -c "CREATE ROLE marquin WITH LOGIN PASSWORD 'Senha123!';"
```

2. Criar database:

```bash
psql -d postgres -c "CREATE DATABASE imobiliaria OWNER marquin;"
```

3. Criar tabelas:

```bash
psql -U marquin -d imobiliaria -f "./banco_postgresql.sql"
```

## Rodando migracoes

Se o banco ja existir e voce precisar apenas atualizar estrutura:

```bash
psql -U marquin -d imobiliaria -f "./migracao.sql"
```

## Executando o projeto

Na raiz do projeto:

```bash
php -S localhost:8000
```

Abra no navegador:

`http://localhost:8000/index.php`

## Autenticacao basica

O projeto agora exige login para acessar o `index.php`.

- Tela de login: `http://localhost:8000/login.php`
- Logout: `http://localhost:8000/logout.php`

Usuario inicial (criado no schema/migracao):

- E-mail: `admin@imobiliaria.com`
- Senha: `123456`

> Observacao: por pedido do projeto, a senha esta salva em texto simples (sem hash).

## Rotas principais

Listagem:

- `index.php?entidade=imovel&acao=listar`
- `index.php?entidade=proprietario&acao=listar`
- `index.php?entidade=corretor&acao=listar`
- `index.php?entidade=cliente&acao=listar`
- `index.php?entidade=contrato&acao=listar`
- `index.php?entidade=visita&acao=listar`

Fluxo padrao por entidade:

- Novo: `index.php?entidade=<entidade>&acao=novo`
- Editar: `index.php?entidade=<entidade>&acao=editar&id=<id>`
- Excluir: `index.php?entidade=<entidade>&acao=excluir&id=<id>`

## Observacoes

- O projeto usa Tailwind via CDN no `index.php`.
- O campo de visita usa `horario_preferencia` e o `periodo` e calculado no backend.
- Existe `config/schema_sqlite.sql` apenas como referencia de schema SQLite; o ambiente atual esta em PostgreSQL.

## Estrutura resumida

```text
imobiliaria/
  config/
    conexao.php
    schema_sqlite.sql
  controller/
  dao/
  model/
  view/
  index.php
  banco_postgresql.sql
  migracao.sql
```
# tde03_imobiliaria
