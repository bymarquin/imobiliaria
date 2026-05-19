# TDE03 Imobiliaria

Sistema academico de imobiliaria em PHP + PostgreSQL, com login e CRUD de:
- Imoveis
- Proprietarios
- Corretores
- Clientes
- Contratos
- Visitas

Contexto de uso: sistema interno para rotina do corretor imobiliario.

## Requisitos
- PHP 8+
- PostgreSQL 12+
- Extensao `pdo_pgsql` ativa

## Como executar
1. Criar usuario e banco:
```bash
psql -d postgres -c "CREATE ROLE marquin WITH LOGIN PASSWORD 'Senha123!';"
psql -d postgres -c "CREATE DATABASE imobiliaria OWNER marquin;"
```

2. Criar tabelas:
```bash
psql -U marquin -d imobiliaria -f "./banco_postgresql.sql"
```

3. (Opcional) Aplicar migracao:
```bash
psql -U marquin -d imobiliaria -f "./migracao.sql"
```

4. Iniciar servidor:
```bash
php -S localhost:8000
```

## Acesso
- Login: `http://localhost:8000/login.php`
- Registro: `http://localhost:8000/registro.php`
- Sistema: `http://localhost:8000/index.php`
- Primeiro acesso: crie um usuario na tela de registro

## Testes
```bash
php tests/run.php
```
