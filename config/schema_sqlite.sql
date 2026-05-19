CREATE TABLE IF NOT EXISTS proprietarios (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nome TEXT NOT NULL,
    cpf TEXT NOT NULL UNIQUE,
    telefone TEXT,
    email TEXT
);

CREATE TABLE IF NOT EXISTS corretores (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nome TEXT NOT NULL,
    creci TEXT NOT NULL UNIQUE,
    telefone TEXT,
    email TEXT
);

CREATE TABLE IF NOT EXISTS imoveis (
    id               INTEGER PRIMARY KEY AUTOINCREMENT,
    titulo           TEXT NOT NULL,
    tipo             TEXT NOT NULL CHECK (tipo IN ('casa', 'apartamento', 'terreno', 'comercial')),
    endereco         TEXT NOT NULL,
    valor            NUMERIC NOT NULL,
    status           TEXT NOT NULL DEFAULT 'disponivel' CHECK (status IN ('disponivel', 'alugado', 'vendido')),
    finalidade       TEXT NOT NULL CHECK (finalidade IN ('venda', 'aluguel')),
    metros_quadrados NUMERIC,
    planta_baixa     TEXT,
    id_proprietario  INTEGER NOT NULL,
    FOREIGN KEY (id_proprietario) REFERENCES proprietarios(id)
);

CREATE TABLE IF NOT EXISTS visitas (
    id         INTEGER PRIMARY KEY AUTOINCREMENT,
    id_imovel  INTEGER NOT NULL,
    nome       TEXT NOT NULL,
    email      TEXT NOT NULL,
    celular    TEXT NOT NULL,
    dia_semana TEXT NOT NULL CHECK (dia_semana IN ('segunda', 'terca', 'quarta', 'quinta', 'sexta', 'sabado', 'domingo')),
    periodo    TEXT NOT NULL CHECK (periodo IN ('manha', 'tarde', 'noite')),
    horario_preferencia TEXT,
    FOREIGN KEY (id_imovel) REFERENCES imoveis(id)
);

CREATE TABLE IF NOT EXISTS clientes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nome TEXT NOT NULL,
    cpf TEXT NOT NULL UNIQUE,
    telefone TEXT,
    email TEXT,
    interesse TEXT NOT NULL CHECK (interesse IN ('compra', 'aluguel'))
);

CREATE TABLE IF NOT EXISTS contratos (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    id_imovel INTEGER NOT NULL,
    id_cliente INTEGER NOT NULL,
    id_corretor INTEGER NOT NULL,
    tipo TEXT NOT NULL CHECK (tipo IN ('venda', 'aluguel')),
    valor NUMERIC NOT NULL,
    data_inicio TEXT NOT NULL,
    data_fim TEXT,
    status TEXT NOT NULL DEFAULT 'ativo' CHECK (status IN ('ativo', 'encerrado', 'cancelado')),
    FOREIGN KEY (id_imovel) REFERENCES imoveis(id),
    FOREIGN KEY (id_cliente) REFERENCES clientes(id),
    FOREIGN KEY (id_corretor) REFERENCES corretores(id)
);

CREATE TABLE IF NOT EXISTS usuarios (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nome TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    senha TEXT NOT NULL
);
