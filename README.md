# TDE03 Imobiliaria

Documentacao tecnica principal do sistema.

## 1) O Que Foi Desenvolvido

### 1.1 Autenticacao e sessao (area administrativa)
- Tela de login (`login.php`) para entrada no painel interno.
- Tela de registro (`registro.php`) para criacao de usuarios administradores.
- Validacao de credenciais usando tabela `usuarios`.
- Controle de sessao para impedir acesso nao autenticado ao painel.
- Tela de logout (`logout.php`) para encerramento da sessao.

### 1.2 Portal publico para clientes (sem autenticacao)
- Tela `cliente_busca.php` para busca de imoveis.
- Tela `cliente_resultados.php` para exibicao dos imoveis encontrados.
- Filtros publicos por finalidade, tipo e valor maximo.

### 1.3 Roteamento centralizado
- Todas as acoes principais passam por `index.php`.
- Rota baseada em dois parametros:
  - `entidade` (modulo)
  - `acao` (operacao)
- Operacoes padrao implementadas por entidade:
  - listar
  - novo
  - editar
  - salvar
  - excluir

### 1.4 Modulos implementados
- Proprietarios.
- Corretores.
- Clientes.
- Imoveis.
- Visitas.
- Contratos.

### 1.5 Regras e recursos relevantes
- Upload de planta baixa no modulo de imoveis.
- Bloqueio de novo contrato para imovel indisponivel.
- Atualizacao automatica do status do imovel apos salvar contrato.
- Calculo automatico de periodo da visita (manha/tarde/noite)
  com base no horario informado.

### 1.6 Persistencia e integridade de dados
- Persistencia com PDO e SQLite (`database.sqlite`).
- Uso de chaves estrangeiras para garantir relacionamentos validos.
- Uso de restricoes `CHECK` no schema para valores de dominio.

## 2) Telas Desenvolvidas

### 2.1 Tela de login administrativo
- Arquivo: `login.php`
- Objetivo: autenticar usuario para liberar acesso ao painel interno.
- Entradas: e-mail e senha.
- Saida esperada: sessao ativa e redirecionamento para `index.php`.

### 2.2 Tela de registro de administrador
- Arquivo: `registro.php`
- Objetivo: cadastrar novo usuario administrador na tabela `usuarios`.
- Entradas: nome, e-mail, senha, confirmar senha.
- Validacoes implementadas:
  - campos obrigatorios
  - formato valido de e-mail
  - senha igual a confirmacao
  - e-mail nao duplicado
- Saida esperada: mensagem de sucesso e orientacao para login.

### 2.3 Tela principal / painel
- Arquivo: `index.php` (estado home, quando `entidade=home`).
- Objetivo: ser a pagina central de navegacao do sistema.
- Conteudo:
  - menu de modulos
  - atalhos de acao
  - indicadores gerais (totais por entidade)

### 2.4 Portal publico de clientes
- Arquivos: `cliente_busca.php` e `cliente_resultados.php`
- Objetivo: permitir busca de imoveis sem login.
- Entradas: filtros de finalidade, tipo e valor maximo.
- Saida esperada: listagem de imoveis disponiveis conforme filtros.

### 2.5 Formulario de proprietario
- Arquivo: `view/proprietario/form.php`
- Campos: nome, cpf, telefone, email.
- Objetivo: criar/editar proprietarios.

### 2.6 Formulario de corretor
- Arquivo: `view/corretor/form.php`
- Campos: nome, creci, telefone, email.
- Objetivo: criar/editar corretores.

### 2.7 Formulario de cliente
- Arquivo: `view/cliente/form.php`
- Campos: nome, cpf, telefone, email, interesse.
- Objetivo: criar/editar clientes.

### 2.8 Formulario de imovel
- Arquivo: `view/imovel/form.php`
- Campos: titulo, tipo, endereco, metros_quadrados, valor, finalidade, status,
  planta_baixa, id_proprietario.
- Objetivo: criar/editar imoveis.

### 2.9 Formulario de visita
- Arquivo: `view/visita/form.php`
- Campos: id_imovel, nome, email, celular, dia_semana, horario_preferencia.
- Objetivo: agendar/editar visitas.

### 2.10 Formulario de contrato
- Arquivo: `view/contrato/form.php`
- Campos: id_imovel, id_cliente, id_corretor, tipo, valor, data_inicio, data_fim.
- Objetivo: criar/editar contratos.

## 3) Arquitetura e Fluxo Tecnico

```text
[Navegador]
    |
    v
[login.php / registro.php]
    |
    +--> [index.php / Front Controller]
               |
               v
        [Controller da Entidade]
               |
               v
            [Model]
               |
               v
            [DAO] ---- PDO ----> [SQLite]
               |
               v
             [View]
```

Fluxo padrao:
1. Usuario interage com tela (formulario/listagem).
2. Requisicao chega no `index.php` com `entidade` e `acao`.
3. Controller organiza e valida entrada.
4. Model representa dados no dominio.
5. DAO persiste/consulta no banco.
6. View renderiza resposta.

## 4) Controllers

- `controller/ImovelController.php`: CRUD de imoveis, upload de planta, carga de proprietarios.
- `controller/ProprietarioController.php`: CRUD de proprietarios.
- `controller/CorretorController.php`: CRUD de corretores.
- `controller/ClienteController.php`: CRUD de clientes.
- `controller/VisitaController.php`: CRUD de visitas e calculo de periodo por horario.
- `controller/ContratoController.php`: CRUD de contratos, validacao de disponibilidade e atualizacao do status do imovel.

## 5) DAOs

Arquivos:
- `dao/ImovelDAO.php`
- `dao/ProprietarioDAO.php`
- `dao/CorretorDAO.php`
- `dao/ClienteDAO.php`
- `dao/VisitaDAO.php`
- `dao/ContratoDAO.php`

Responsabilidades:
- `listar()`, `buscarPorId($id)`, `salvar($objeto)`, `excluir($id)`.
- SQL isolado da camada de controller.

## 6) Models

Arquivos:
- `model/Imovel.php`
- `model/Proprietario.php`
- `model/Corretor.php`
- `model/Cliente.php`
- `model/Visita.php`
- `model/Contrato.php`

Papel:
- Estruturar dados de dominio e servir de contrato entre controller e DAO.

## 7) Conexao e Banco de Dados

### 7.1 Conexao
- Arquivo: `config/conexao.php`
- Singleton via `Conexao::getConn()`.
- Banco: SQLite (`database.sqlite`).

### 7.2 Schema
- Arquivo: `config/schema_sqlite.sql`
- Tabelas: usuarios, proprietarios, corretores, clientes, imoveis, visitas, contratos.

### 7.3 Relacionamentos
- `imoveis.id_proprietario -> proprietarios.id`
- `visitas.id_imovel -> imoveis.id`
- `contratos.id_imovel -> imoveis.id`
- `contratos.id_cliente -> clientes.id`
- `contratos.id_corretor -> corretores.id`

## 8) Setup e Execucao

Pre-requisitos:
- PHP 8+
- Extensao PDO com SQLite
- SQLite3 CLI (para criar o schema via terminal)

No diretorio raiz do projeto, crie o banco e as tabelas:
```bash
sqlite3 database.sqlite < config/schema_sqlite.sql
```

Rodar local:
```bash
php -S localhost:8000
```

Acesso:
- Entrada principal: `http://localhost:8000/index.php` (redireciona para a busca publica quando nao autenticado)
- Busca de clientes: `http://localhost:8000/cliente_busca.php`
- Resultado da busca: `http://localhost:8000/cliente_resultados.php`
- Registro: `http://localhost:8000/registro.php`
- Login: `http://localhost:8000/login.php`
- Painel administrativo: `http://localhost:8000/index.php`

## 9) Glossario

- CRUD: Create, Read, Update, Delete.
- MVC: Model, View, Controller.
- DAO: Data Access Object.
- PDO: API de acesso a banco no PHP.
- Front Controller: ponto unico de entrada (`index.php`).
- Sessao: dados temporarios do usuario no servidor.
- PRG: Post/Redirect/Get.
- PK: Primary Key.
- FK: Foreign Key.
- CRECI: registro profissional do corretor de imoveis.
