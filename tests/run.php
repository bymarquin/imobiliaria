<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/conexao.php';
require_once __DIR__ . '/../controller/ImovelController.php';
require_once __DIR__ . '/../controller/ProprietarioController.php';
require_once __DIR__ . '/../controller/CorretorController.php';
require_once __DIR__ . '/../controller/ClienteController.php';
require_once __DIR__ . '/../controller/ContratoController.php';
require_once __DIR__ . '/../controller/VisitaController.php';

final class TestRunner
{
    private int $passed = 0;
    private int $failed = 0;

    public function run(string $name, callable $fn): void
    {
        try {
            $fn();
            $this->passed++;
            echo "{$name} | OK\n";
        } catch (Throwable $e) {
            $this->failed++;
            echo "{$name} | FALHOU\n";
            echo "  -> " . $e->getMessage() . "\n";
        }
    }

    public function assertTrue(bool $condition, string $message): void
    {
        if (!$condition) {
            throw new RuntimeException($message);
        }
    }

    public function assertEquals(mixed $expected, mixed $actual, string $message): void
    {
        if ($expected !== $actual) {
            throw new RuntimeException($message . " (esperado: " . var_export($expected, true) . ", atual: " . var_export($actual, true) . ")");
        }
    }

    public function report(): int
    {
        echo "\nResumo: {$this->passed} passou, {$this->failed} falhou.\n";
        return $this->failed === 0 ? 0 : 1;
    }
}

$t = new TestRunner();

$t->run('Conectar no PostgreSQL', function () use ($t): void {
    $conn = Conexao::getConn();
    $t->assertTrue($conn instanceof PDO, 'Conexao::getConn() nao retornou PDO.');
    $ok = (bool) $conn->query('SELECT 1')->fetchColumn();
    $t->assertTrue($ok, 'Consulta simples ao banco falhou.');
});

$t->run('Validar tabelas principais', function () use ($t): void {
    $conn = Conexao::getConn();
    $required = [
        'usuarios',
        'proprietarios',
        'corretores',
        'clientes',
        'imoveis',
        'contratos',
        'visitas',
    ];

    $stmt = $conn->prepare(
        "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = 'public' AND table_name = ?"
    );

    foreach ($required as $table) {
        $stmt->execute([$table]);
        $count = (int) $stmt->fetchColumn();
        $t->assertEquals(1, $count, "Tabela '{$table}' nao encontrada");
    }
});

$t->run('Criar um usuario de teste', function () use ($t): void {
    $conn = Conexao::getConn();
    $email = 'teste_' . uniqid() . '@imobiliaria.com';

    $insert = $conn->prepare('INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)');
    $insert->execute(['Usuario Teste', $email, '123456']);

    $check = $conn->prepare('SELECT COUNT(*) FROM usuarios WHERE email = ?');
    $check->execute([$email]);
    $count = (int) $check->fetchColumn();
    $t->assertEquals(1, $count, 'Usuario de teste nao foi criado.');

    $delete = $conn->prepare('DELETE FROM usuarios WHERE email = ?');
    $delete->execute([$email]);
});

$conn = Conexao::getConn();
$token = uniqid();

$proprietarioController = new ProprietarioController();
$corretorController = new CorretorController();
$clienteController = new ClienteController();
$imovelController = new ImovelController();
$visitaController = new VisitaController();
$contratoController = new ContratoController();

$ids = [
    'proprietario' => null,
    'corretor' => null,
    'cliente' => null,
    'imovel' => null,
    'visita' => null,
    'contrato' => null,
    'prop_imovel' => null,
    'prop_visita' => null,
    'imovel_visita' => null,
    'prop_contrato' => null,
    'imovel_contrato' => null,
    'cliente_contrato' => null,
    'corretor_contrato' => null,
];

$t->run('criar um proprietario', function () use ($t, $conn, $proprietarioController, &$ids, $token): void {
    $cpf = '9100000' . str_pad((string) random_int(1000, 9999), 4, '0', STR_PAD_LEFT);
    $proprietarioController->salvar([
        'nome' => 'Proprietario Teste ' . $token,
        'cpf' => $cpf,
        'telefone' => '11911111111',
        'email' => 'prop_' . $token . '@teste.com',
    ]);
    $stmt = $conn->prepare('SELECT id FROM proprietarios WHERE cpf = ?');
    $stmt->execute([$cpf]);
    $ids['proprietario'] = (int) $stmt->fetchColumn();
    $t->assertTrue($ids['proprietario'] > 0, 'Proprietario nao foi criado.');
});

$t->run('listar um proprietario', function () use ($t, $proprietarioController, &$ids): void {
    $list = $proprietarioController->listar();
    $found = false;
    foreach ($list as $item) {
        if ($item->getId() === $ids['proprietario']) {
            $found = true;
            break;
        }
    }
    $t->assertTrue($found, 'Proprietario criado nao apareceu na listagem.');
});

$t->run('atualizar um proprietario', function () use ($t, $proprietarioController, &$ids): void {
    $atual = $proprietarioController->buscarPorId((int) $ids['proprietario']);
    $t->assertTrue($atual !== null, 'Proprietario base nao encontrado para atualizar.');

    $proprietarioController->salvar([
        'id' => $ids['proprietario'],
        'nome' => 'Proprietario Atualizado',
        'cpf' => $atual->getCpf(),
        'telefone' => '11922222222',
        'email' => 'prop_atualizado@teste.com',
    ]);
    $item = $proprietarioController->buscarPorId((int) $ids['proprietario']);
    $t->assertTrue($item !== null, 'Proprietario atualizado nao encontrado.');
    $t->assertEquals('Proprietario Atualizado', $item->getNome(), 'Nome do proprietario nao atualizou.');
});

$t->run('deletar um proprietario', function () use ($t, $proprietarioController, &$ids): void {
    $proprietarioController->excluir((int) $ids['proprietario']);
    $item = $proprietarioController->buscarPorId((int) $ids['proprietario']);
    $t->assertTrue($item === null, 'Proprietario nao foi deletado.');
});

$t->run('criar um corretor', function () use ($t, $conn, $corretorController, &$ids, $token): void {
    $creci = 'CRECI-' . $token;
    $corretorController->salvar([
        'nome' => 'Corretor Teste',
        'creci' => $creci,
        'telefone' => '11933333333',
        'email' => 'corretor_' . $token . '@teste.com',
    ]);
    $stmt = $conn->prepare('SELECT id FROM corretores WHERE creci = ?');
    $stmt->execute([$creci]);
    $ids['corretor'] = (int) $stmt->fetchColumn();
    $t->assertTrue($ids['corretor'] > 0, 'Corretor nao foi criado.');
});

$t->run('listar um corretor', function () use ($t, $corretorController, &$ids): void {
    $list = $corretorController->listar();
    $found = false;
    foreach ($list as $item) {
        if ($item->getId() === $ids['corretor']) {
            $found = true;
            break;
        }
    }
    $t->assertTrue($found, 'Corretor criado nao apareceu na listagem.');
});

$t->run('atualizar um corretor', function () use ($t, $corretorController, &$ids): void {
    $corretorController->salvar([
        'id' => $ids['corretor'],
        'nome' => 'Corretor Atualizado',
        'creci' => 'CRECI-ATUALIZADO-' . $ids['corretor'],
        'telefone' => '11944444444',
        'email' => 'corretor_atualizado@teste.com',
    ]);
    $item = $corretorController->buscarPorId((int) $ids['corretor']);
    $t->assertTrue($item !== null, 'Corretor atualizado nao encontrado.');
    $t->assertEquals('Corretor Atualizado', $item->getNome(), 'Nome do corretor nao atualizou.');
});

$t->run('deletar um corretor', function () use ($t, $corretorController, &$ids): void {
    $corretorController->excluir((int) $ids['corretor']);
    $item = $corretorController->buscarPorId((int) $ids['corretor']);
    $t->assertTrue($item === null, 'Corretor nao foi deletado.');
});

$t->run('criar um cliente', function () use ($t, $conn, $clienteController, &$ids, $token): void {
    $cpf = '9200000' . str_pad((string) random_int(1000, 9999), 4, '0', STR_PAD_LEFT);
    $clienteController->salvar([
        'nome' => 'Cliente Teste',
        'cpf' => $cpf,
        'telefone' => '11955555555',
        'email' => 'cliente_' . $token . '@teste.com',
        'interesse' => 'compra',
    ]);
    $stmt = $conn->prepare('SELECT id FROM clientes WHERE cpf = ?');
    $stmt->execute([$cpf]);
    $ids['cliente'] = (int) $stmt->fetchColumn();
    $t->assertTrue($ids['cliente'] > 0, 'Cliente nao foi criado.');
});

$t->run('listar um cliente', function () use ($t, $clienteController, &$ids): void {
    $list = $clienteController->listar();
    $found = false;
    foreach ($list as $item) {
        if ($item->getId() === $ids['cliente']) {
            $found = true;
            break;
        }
    }
    $t->assertTrue($found, 'Cliente criado nao apareceu na listagem.');
});

$t->run('atualizar um cliente', function () use ($t, $clienteController, &$ids): void {
    $atual = $clienteController->buscarPorId((int) $ids['cliente']);
    $t->assertTrue($atual !== null, 'Cliente base nao encontrado para atualizar.');

    $clienteController->salvar([
        'id' => $ids['cliente'],
        'nome' => 'Cliente Atualizado',
        'cpf' => $atual->getCpf(),
        'telefone' => '11966666666',
        'email' => 'cliente_atualizado@teste.com',
        'interesse' => 'aluguel',
    ]);
    $item = $clienteController->buscarPorId((int) $ids['cliente']);
    $t->assertTrue($item !== null, 'Cliente atualizado nao encontrado.');
    $t->assertEquals('Cliente Atualizado', $item->getNome(), 'Nome do cliente nao atualizou.');
});

$t->run('deletar um cliente', function () use ($t, $clienteController, &$ids): void {
    $clienteController->excluir((int) $ids['cliente']);
    $item = $clienteController->buscarPorId((int) $ids['cliente']);
    $t->assertTrue($item === null, 'Cliente nao foi deletado.');
});

$t->run('criar um imovel', function () use ($t, $conn, $proprietarioController, $imovelController, &$ids, $token): void {
    $cpfProp = '9300000' . str_pad((string) random_int(1000, 9999), 4, '0', STR_PAD_LEFT);
    $proprietarioController->salvar(['nome' => 'Dono Imovel', 'cpf' => $cpfProp, 'telefone' => '', 'email' => '']);
    $stmtProp = $conn->prepare('SELECT id FROM proprietarios WHERE cpf = ?');
    $stmtProp->execute([$cpfProp]);
    $ids['prop_imovel'] = (int) $stmtProp->fetchColumn();

    $titulo = 'Imovel Teste ' . $token;
    $imovelController->salvar([
        'titulo' => $titulo,
        'tipo' => 'casa',
        'endereco' => 'Rua Teste 123',
        'valor' => '350000.00',
        'status' => 'disponivel',
        'finalidade' => 'venda',
        'metros_quadrados' => '120.00',
        'id_proprietario' => $ids['prop_imovel'],
    ]);
    $stmtImovel = $conn->prepare('SELECT id FROM imoveis WHERE titulo = ?');
    $stmtImovel->execute([$titulo]);
    $ids['imovel'] = (int) $stmtImovel->fetchColumn();
    $t->assertTrue($ids['imovel'] > 0, 'Imovel nao foi criado.');
});

$t->run('listar um imovel', function () use ($t, $imovelController, &$ids): void {
    $list = $imovelController->listar();
    $found = false;
    foreach ($list as $item) {
        if ($item->getId() === $ids['imovel']) {
            $found = true;
            break;
        }
    }
    $t->assertTrue($found, 'Imovel criado nao apareceu na listagem.');
});

$t->run('atualizar um imovel', function () use ($t, $imovelController, &$ids): void {
    $imovelController->salvar([
        'id' => $ids['imovel'],
        'titulo' => 'Imovel Atualizado',
        'tipo' => 'apartamento',
        'endereco' => 'Rua Nova 456',
        'valor' => '400000.00',
        'status' => 'disponivel',
        'finalidade' => 'aluguel',
        'metros_quadrados' => '95.00',
        'id_proprietario' => $ids['prop_imovel'],
        'planta_baixa_atual' => '',
    ]);
    $item = $imovelController->buscarPorId((int) $ids['imovel']);
    $t->assertTrue($item !== null, 'Imovel atualizado nao encontrado.');
    $t->assertEquals('Imovel Atualizado', $item->getTitulo(), 'Titulo do imovel nao atualizou.');
});

$t->run('deletar um imovel', function () use ($t, $imovelController, $proprietarioController, &$ids): void {
    $imovelController->excluir((int) $ids['imovel']);
    $item = $imovelController->buscarPorId((int) $ids['imovel']);
    $t->assertTrue($item === null, 'Imovel nao foi deletado.');
    $proprietarioController->excluir((int) $ids['prop_imovel']);
});

$t->run('criar uma visita', function () use ($t, $conn, $proprietarioController, $imovelController, $visitaController, &$ids, $token): void {
    $cpfProp = '9400000' . str_pad((string) random_int(1000, 9999), 4, '0', STR_PAD_LEFT);
    $proprietarioController->salvar(['nome' => 'Dono Visita', 'cpf' => $cpfProp, 'telefone' => '', 'email' => '']);
    $stmtProp = $conn->prepare('SELECT id FROM proprietarios WHERE cpf = ?');
    $stmtProp->execute([$cpfProp]);
    $ids['prop_visita'] = (int) $stmtProp->fetchColumn();

    $titulo = 'Imovel Visita ' . $token;
    $imovelController->salvar([
        'titulo' => $titulo,
        'tipo' => 'casa',
        'endereco' => 'Rua Visita 10',
        'valor' => '210000.00',
        'status' => 'disponivel',
        'finalidade' => 'venda',
        'id_proprietario' => $ids['prop_visita'],
    ]);
    $stmtImovel = $conn->prepare('SELECT id FROM imoveis WHERE titulo = ?');
    $stmtImovel->execute([$titulo]);
    $ids['imovel_visita'] = (int) $stmtImovel->fetchColumn();

    $emailVisita = 'visita_' . $token . '@teste.com';
    $visitaController->salvar([
        'id_imovel' => $ids['imovel_visita'],
        'nome' => 'Visitante Teste',
        'email' => $emailVisita,
        'celular' => '11977777777',
        'dia_semana' => 'segunda',
        'horario_preferencia' => '10:30',
    ]);
    $stmtVisita = $conn->prepare('SELECT id FROM visitas WHERE email = ?');
    $stmtVisita->execute([$emailVisita]);
    $ids['visita'] = (int) $stmtVisita->fetchColumn();
    $t->assertTrue($ids['visita'] > 0, 'Visita nao foi criada.');
});

$t->run('listar uma visita', function () use ($t, $visitaController, &$ids): void {
    $list = $visitaController->listar();
    $found = false;
    foreach ($list as $item) {
        if ($item->getId() === $ids['visita']) {
            $found = true;
            break;
        }
    }
    $t->assertTrue($found, 'Visita criada nao apareceu na listagem.');
});

$t->run('atualizar uma visita', function () use ($t, $visitaController, &$ids): void {
    $visitaController->salvar([
        'id' => $ids['visita'],
        'id_imovel' => $ids['imovel_visita'],
        'nome' => 'Visitante Atualizado',
        'email' => 'visita_atualizada@teste.com',
        'celular' => '11988888888',
        'dia_semana' => 'terca',
        'horario_preferencia' => '15:00',
    ]);
    $item = $visitaController->buscarPorId((int) $ids['visita']);
    $t->assertTrue($item !== null, 'Visita atualizada nao encontrada.');
    $t->assertEquals('Visitante Atualizado', $item->getNome(), 'Nome da visita nao atualizou.');
});

$t->run('deletar uma visita', function () use ($t, $visitaController, $imovelController, $proprietarioController, &$ids): void {
    $visitaController->excluir((int) $ids['visita']);
    $item = $visitaController->buscarPorId((int) $ids['visita']);
    $t->assertTrue($item === null, 'Visita nao foi deletada.');
    $imovelController->excluir((int) $ids['imovel_visita']);
    $proprietarioController->excluir((int) $ids['prop_visita']);
});

$t->run('criar um contrato', function () use ($t, $conn, $proprietarioController, $imovelController, $clienteController, $corretorController, $contratoController, &$ids, $token): void {
    $cpfProp = '9500000' . str_pad((string) random_int(1000, 9999), 4, '0', STR_PAD_LEFT);
    $proprietarioController->salvar(['nome' => 'Dono Contrato', 'cpf' => $cpfProp, 'telefone' => '', 'email' => '']);
    $stmtProp = $conn->prepare('SELECT id FROM proprietarios WHERE cpf = ?');
    $stmtProp->execute([$cpfProp]);
    $ids['prop_contrato'] = (int) $stmtProp->fetchColumn();

    $titulo = 'Imovel Contrato ' . $token;
    $imovelController->salvar([
        'titulo' => $titulo,
        'tipo' => 'casa',
        'endereco' => 'Rua Contrato 1',
        'valor' => '500000.00',
        'status' => 'disponivel',
        'finalidade' => 'venda',
        'id_proprietario' => $ids['prop_contrato'],
    ]);
    $stmtImovel = $conn->prepare('SELECT id FROM imoveis WHERE titulo = ?');
    $stmtImovel->execute([$titulo]);
    $ids['imovel_contrato'] = (int) $stmtImovel->fetchColumn();

    $cpfCliente = '9600000' . str_pad((string) random_int(1000, 9999), 4, '0', STR_PAD_LEFT);
    $clienteController->salvar(['nome' => 'Cliente Contrato', 'cpf' => $cpfCliente, 'telefone' => '', 'email' => '', 'interesse' => 'compra']);
    $stmtCliente = $conn->prepare('SELECT id FROM clientes WHERE cpf = ?');
    $stmtCliente->execute([$cpfCliente]);
    $ids['cliente_contrato'] = (int) $stmtCliente->fetchColumn();

    $creci = 'CR' . $token;
    $corretorController->salvar(['nome' => 'Corretor Contrato', 'creci' => $creci, 'telefone' => '', 'email' => '']);
    $stmtCorretor = $conn->prepare('SELECT id FROM corretores WHERE creci = ?');
    $stmtCorretor->execute([$creci]);
    $ids['corretor_contrato'] = (int) $stmtCorretor->fetchColumn();

    $contratoController->salvar([
        'id_imovel' => $ids['imovel_contrato'],
        'id_cliente' => $ids['cliente_contrato'],
        'id_corretor' => $ids['corretor_contrato'],
        'tipo' => 'venda',
        'valor' => '500000.00',
        'data_inicio' => '2026-01-01',
        'data_fim' => '',
    ]);
    $stmtContrato = $conn->prepare('SELECT id FROM contratos WHERE id_imovel = ? AND id_cliente = ? AND id_corretor = ? ORDER BY id DESC LIMIT 1');
    $stmtContrato->execute([$ids['imovel_contrato'], $ids['cliente_contrato'], $ids['corretor_contrato']]);
    $ids['contrato'] = (int) $stmtContrato->fetchColumn();
    $t->assertTrue($ids['contrato'] > 0, 'Contrato nao foi criado.');
});

$t->run('listar um contrato', function () use ($t, $contratoController, &$ids): void {
    $list = $contratoController->listar();
    $found = false;
    foreach ($list as $item) {
        if ($item->getId() === $ids['contrato']) {
            $found = true;
            break;
        }
    }
    $t->assertTrue($found, 'Contrato criado nao apareceu na listagem.');
});

$t->run('atualizar um contrato', function () use ($t, $contratoController, &$ids): void {
    $contratoController->salvar([
        'id' => $ids['contrato'],
        'id_imovel' => $ids['imovel_contrato'],
        'id_cliente' => $ids['cliente_contrato'],
        'id_corretor' => $ids['corretor_contrato'],
        'tipo' => 'aluguel',
        'valor' => '2500.00',
        'data_inicio' => '2026-02-01',
        'data_fim' => '2027-02-01',
    ]);
    $item = $contratoController->buscarPorId((int) $ids['contrato']);
    $t->assertTrue($item !== null, 'Contrato atualizado nao encontrado.');
    $t->assertEquals('aluguel', $item->getTipo(), 'Tipo do contrato nao atualizou.');
});

$t->run('deletar um contrato', function () use ($t, $contratoController, $imovelController, $proprietarioController, $clienteController, $corretorController, &$ids): void {
    $contratoController->excluir((int) $ids['contrato']);
    $item = $contratoController->buscarPorId((int) $ids['contrato']);
    $t->assertTrue($item === null, 'Contrato nao foi deletado.');

    $imovelController->excluir((int) $ids['imovel_contrato']);
    $proprietarioController->excluir((int) $ids['prop_contrato']);
    $clienteController->excluir((int) $ids['cliente_contrato']);
    $corretorController->excluir((int) $ids['corretor_contrato']);
});

exit($t->report());
