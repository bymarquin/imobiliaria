-- Script de migração para bases já existentes.
-- Se for ambiente novo, prefira usar banco_postgresql.sql.

ALTER TABLE imoveis ADD COLUMN IF NOT EXISTS metros_quadrados NUMERIC(8, 2);
ALTER TABLE imoveis ADD COLUMN IF NOT EXISTS planta_baixa VARCHAR(255);

CREATE TABLE IF NOT EXISTS visitas (    
    id         SERIAL PRIMARY KEY,
    id_imovel  INT NOT NULL REFERENCES imoveis(id),
    nome       VARCHAR(100) NOT NULL,
    email      VARCHAR(100) NOT NULL,
    celular    VARCHAR(20)  NOT NULL,
    dia_semana VARCHAR(10)  NOT NULL CHECK (dia_semana IN ('segunda', 'terca', 'quarta', 'quinta', 'sexta', 'sabado', 'domingo')),
    periodo    VARCHAR(10)  NOT NULL CHECK (periodo IN ('manha', 'tarde', 'noite')),
    horario_preferencia TIME
);

ALTER TABLE visitas ADD COLUMN IF NOT EXISTS horario_preferencia TIME;

CREATE TABLE IF NOT EXISTS usuarios (
    id    SERIAL PRIMARY KEY,
    nome  VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(100) NOT NULL
);

INSERT INTO usuarios (nome, email, senha)
SELECT 'Administrador', 'admin@imobiliaria.com', '123456'
WHERE NOT EXISTS (
    SELECT 1 FROM usuarios WHERE email = 'admin@imobiliaria.com'
);
