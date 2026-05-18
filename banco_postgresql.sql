CREATE TABLE IF NOT EXISTS proprietarios (
    id       SERIAL PRIMARY KEY,
    nome     VARCHAR(100) NOT NULL,
    cpf      VARCHAR(14)  NOT NULL UNIQUE,
    telefone VARCHAR(20),
    email    VARCHAR(100)
);

CREATE TABLE IF NOT EXISTS corretores (
    id       SERIAL PRIMARY KEY,
    nome     VARCHAR(100) NOT NULL,
    creci    VARCHAR(20)  NOT NULL UNIQUE,
    telefone VARCHAR(20),
    email    VARCHAR(100)
);

CREATE TABLE IF NOT EXISTS imoveis (
    id               SERIAL PRIMARY KEY,
    titulo           VARCHAR(150) NOT NULL,
    tipo             VARCHAR(20)  NOT NULL CHECK (tipo IN ('casa', 'apartamento', 'terreno', 'comercial')),
    endereco         VARCHAR(255) NOT NULL,
    valor            NUMERIC(12, 2) NOT NULL,
    status           VARCHAR(20)  NOT NULL DEFAULT 'disponivel' CHECK (status IN ('disponivel', 'alugado', 'vendido')),
    finalidade       VARCHAR(10)  NOT NULL CHECK (finalidade IN ('venda', 'aluguel')),
    metros_quadrados NUMERIC(8, 2),
    planta_baixa     VARCHAR(255),
    id_proprietario  INT NOT NULL REFERENCES proprietarios(id)
);

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

CREATE TABLE IF NOT EXISTS clientes (
    id        SERIAL PRIMARY KEY,
    nome      VARCHAR(100) NOT NULL,
    cpf       VARCHAR(14)  NOT NULL UNIQUE,
    telefone  VARCHAR(20),
    email     VARCHAR(100),
    interesse VARCHAR(10)  NOT NULL CHECK (interesse IN ('compra', 'aluguel'))
);

CREATE TABLE IF NOT EXISTS contratos (
    id          SERIAL PRIMARY KEY,
    id_imovel   INT NOT NULL REFERENCES imoveis(id),
    id_cliente  INT NOT NULL REFERENCES clientes(id),
    id_corretor INT NOT NULL REFERENCES corretores(id),
    tipo        VARCHAR(10)    NOT NULL CHECK (tipo IN ('venda', 'aluguel')),
    valor       NUMERIC(12, 2) NOT NULL,
    data_inicio DATE NOT NULL,
    data_fim    DATE
);

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
