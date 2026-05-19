<?php

session_start();

require_once __DIR__ . '/config/conexao.php';
require_once __DIR__ . '/controller/ImovelController.php';
require_once __DIR__ . '/controller/ProprietarioController.php';
require_once __DIR__ . '/controller/CorretorController.php';
require_once __DIR__ . '/controller/ClienteController.php';
require_once __DIR__ . '/controller/ContratoController.php';
require_once __DIR__ . '/controller/VisitaController.php';

if (empty($_SESSION['usuario_id'])) {
    $tentandoRotaAdmin = isset($_GET['entidade']) || isset($_GET['acao']) || $_SERVER['REQUEST_METHOD'] === 'POST';
    header('Location: ' . ($tentandoRotaAdmin ? 'login.php' : 'cliente_busca.php'));
    exit;
}

$entidade = $_GET['entidade'] ?? 'home';
$acao     = $_GET['acao']     ?? 'listar';

$controllers = [
    'imovel'       => new ImovelController(),
    'proprietario' => new ProprietarioController(),
    'corretor'     => new CorretorController(),
    'cliente'      => new ClienteController(),
    'contrato'     => new ContratoController(),
    'visita'       => new VisitaController(),
];

if ($entidade !== 'home' && !isset($controllers[$entidade])) {
    http_response_code(404);
    exit('Modulo invalido');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $entidade !== 'home' && $acao === 'salvar') {
    try {
        $controllers[$entidade]->salvar($_POST);
    } catch (RuntimeException $e) {
        $_SESSION['form_erro'] = $e->getMessage();
        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        $destino = 'index.php?entidade=' . $entidade . '&acao=' . ($id > 0 ? 'editar&id=' . $id : 'novo');
        header('Location: ' . $destino);
        exit;
    }
    header('Location: index.php?entidade=' . $entidade . '&acao=listar');
    exit;
}

if ($entidade !== 'home' && $acao === 'excluir' && isset($_GET['id'])) {
    $controllers[$entidade]->excluir((int) $_GET['id']);
    header('Location: index.php?entidade=' . $entidade . '&acao=listar');
    exit;
}

if ($entidade === 'contrato' && $acao === 'encerrar' && isset($_GET['id'])) {
    try {
        $controllers['contrato']->encerrar((int) $_GET['id']);
    } catch (RuntimeException $e) {
        $_SESSION['form_erro'] = $e->getMessage();
    }
    header('Location: index.php?entidade=contrato&acao=listar');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Imboliária Ravel</title>
    <link rel="stylesheet" href="assets/style.css">
    <script src="assets/masks.js" defer></script>
</head>
<body>

<header>
    <div class="header-inner">
        <div class="left-menu">
        <h1>Imobiliaria</h1>
        <!-- Menu de navegação — aparece em todas as páginas -->
        <nav>
            <a href="index.php" class="<?= $entidade === 'home' ? 'active' : '' ?>">Painel</a>
            <a href="index.php?entidade=imovel&acao=listar" class="<?= $entidade === 'imovel' ? 'active' : '' ?>">Imóveis</a>
            <a href="index.php?entidade=proprietario&acao=listar" class="<?= $entidade === 'proprietario' ? 'active' : '' ?>">Proprietários</a>
            <a href="index.php?entidade=corretor&acao=listar" class="<?= $entidade === 'corretor' ? 'active' : '' ?>">Corretores</a>
            <a href="index.php?entidade=cliente&acao=listar" class="<?= $entidade === 'cliente' ? 'active' : '' ?>">Clientes</a>
            <a href="index.php?entidade=contrato&acao=listar" class="<?= $entidade === 'contrato' ? 'active' : '' ?>">Contratos</a>
            <a href="index.php?entidade=visita&acao=listar" class="<?= $entidade === 'visita' ? 'active' : '' ?>">Visitas</a>
        </nav>
        </div>
        <div class="user-box">
            <a href="cliente_busca.php" target="_blank">Portal publico</a>
            <span>|</span>
            <span><?= htmlspecialchars((string) ($_SESSION['usuario_nome'] ?? '')) ?></span>
            <a href="logout.php">Sair</a>
        </div>
    </div>
</header>

<main>

    <?php if ($entidade === 'home'): ?>
        <!-- Home orientada ao fluxo administrativo -->
        <?php
        $conn = Conexao::getConn();
        $totalImoveis = (int) $conn->query('SELECT COUNT(*) FROM imoveis')->fetchColumn();
        $totalClientes = (int) $conn->query('SELECT COUNT(*) FROM clientes')->fetchColumn();
        $totalVisitas = (int) $conn->query('SELECT COUNT(*) FROM visitas')->fetchColumn();
        $totalContratos = (int) $conn->query('SELECT COUNT(*) FROM contratos')->fetchColumn();
        ?>

        <h2>Painel Administrativo</h2>
        <p>Area restrita para gestao da imobiliaria e operacao comercial.</p>

        <div class="home-grid">
            <div class="home-card">
                <strong>Imoveis ativos</strong>
                <p><?= $totalImoveis ?></p>
                <a href="index.php?entidade=imovel&acao=listar">Ver imoveis</a>
            </div>
            <div class="home-card">
                <strong>Clientes cadastrados</strong>
                <p><?= $totalClientes ?></p>
                <a href="index.php?entidade=cliente&acao=listar">Ver clientes</a>
            </div>
            <div class="home-card">
                <strong>Visitas registradas</strong>
                <p><?= $totalVisitas ?></p>
                <a href="index.php?entidade=visita&acao=listar">Ver visitas</a>
            </div>
            <div class="home-card">
                <strong>Contratos fechados</strong>
                <p><?= $totalContratos ?></p>
                <a href="index.php?entidade=contrato&acao=listar">Ver contratos</a>
            </div>
        </div>

        <h2>Atalhos rapidos</h2>
        <p>
            <a href="index.php?entidade=cliente&acao=novo">Novo cliente</a> |
            <a href="index.php?entidade=imovel&acao=novo">Novo imovel</a> |
            <a href="index.php?entidade=visita&acao=novo">Nova visita</a> |
            <a href="index.php?entidade=contrato&acao=novo">Novo contrato</a>
        </p>

    <?php elseif ($acao === 'novo' || $acao === 'editar'): ?>
        <?php
        if ($entidade === 'imovel'):
            $controller    = $controllers['imovel'];
            $imovel        = $acao === 'editar' ? $controller->buscarPorId((int) ($_GET['id'] ?? 0)) : null;
            $proprietarios = $controller->listarProprietarios();
            require __DIR__ . '/view/imovel/form.php';

        elseif ($entidade === 'proprietario'):
            $controller  = $controllers['proprietario'];
            $proprietario = $acao === 'editar' ? $controller->buscarPorId((int) ($_GET['id'] ?? 0)) : null;
            require __DIR__ . '/view/proprietario/form.php';

        elseif ($entidade === 'corretor'):
            $controller = $controllers['corretor'];
            $corretor   = $acao === 'editar' ? $controller->buscarPorId((int) ($_GET['id'] ?? 0)) : null;
            require __DIR__ . '/view/corretor/form.php';

        elseif ($entidade === 'cliente'):
            $controller = $controllers['cliente'];
            $cliente    = $acao === 'editar' ? $controller->buscarPorId((int) ($_GET['id'] ?? 0)) : null;
            require __DIR__ . '/view/cliente/form.php';

        elseif ($entidade === 'contrato'):
            $controller = $controllers['contrato'];
            $contrato   = $acao === 'editar' ? $controller->buscarPorId((int) ($_GET['id'] ?? 0)) : null;
            $imoveis    = $controller->listarImoveis();
            $clientes   = $controller->listarClientes();
            $corretores = $controller->listarCorretores();
            require __DIR__ . '/view/contrato/form.php';

        elseif ($entidade === 'visita'):
            $controller = $controllers['visita'];
            $visita     = $acao === 'editar' ? $controller->buscarPorId((int) ($_GET['id'] ?? 0)) : null;
            $imoveis    = $controller->listarImoveis();
            require __DIR__ . '/view/visita/form.php';
        endif;
        ?>

    <?php else: ?>
        <?php
        $filtroFinalidade = ($entidade === 'imovel') ? ($_GET['finalidade'] ?? '') : '';
        $itens = ($entidade === 'imovel')
            ? $controllers[$entidade]->listar($filtroFinalidade)
            : $controllers[$entidade]->listar();
        ?>
        <h2 class="text-xl font-semibold text-gray-900 mb-1"><?= ucfirst($entidade) ?></h2>
        <p class="mb-4"><a class="bg-gray-900 text-white text-xs font-medium px-3 py-1.5 rounded hover:bg-gray-700 transition inline-flex items-center" href="index.php?entidade=<?= $entidade ?>&acao=novo">+ Novo</a></p>

        <?php if ($entidade === 'proprietario'): ?>
            <table class="w-full border border-gray-200 rounded-lg overflow-hidden text-sm">
                <tr class="hover:bg-gray-50">
                    <th class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-2.5 text-left border-b border-gray-200">ID</th><th class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-2.5 text-left border-b border-gray-200">Nome</th><th class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-2.5 text-left border-b border-gray-200">CPF</th>
                    <th class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-2.5 text-left border-b border-gray-200">Telefone</th><th class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-2.5 text-left border-b border-gray-200">Email</th><th class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-2.5 text-left border-b border-gray-200">Acoes</th>
                </tr>
                <?php foreach ($itens as $r): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-gray-700 border-b border-gray-100"><?= $r->getId() ?></td>
                    <td class="px-4 py-3 text-gray-700 border-b border-gray-100"><?= htmlspecialchars($r->getNome()) ?></td>
                    <td class="px-4 py-3 text-gray-700 border-b border-gray-100"><?= htmlspecialchars($r->getCpf()) ?></td>
                    <td class="px-4 py-3 text-gray-700 border-b border-gray-100"><?= htmlspecialchars($r->getTelefone()) ?></td>
                    <td class="px-4 py-3 text-gray-700 border-b border-gray-100"><?= htmlspecialchars($r->getEmail()) ?></td>
                    <td class="px-4 py-3 text-gray-700 border-b border-gray-100">
                        <a class="text-xs text-gray-500 hover:text-gray-900 px-2 py-0.5 rounded hover:bg-gray-100 transition" href="index.php?entidade=proprietario&acao=editar&id=<?= $r->getId() ?>">Editar</a>
                        <!-- confirm() pede confirmação antes de excluir — evita exclusão por engano -->
                        <a class="text-xs text-red-500 hover:text-red-700 px-2 py-0.5 rounded hover:bg-red-50 transition" href="index.php?entidade=proprietario&acao=excluir&id=<?= $r->getId() ?>"
                           onclick="return confirm('Excluir registro?')">Excluir</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>

        <?php elseif ($entidade === 'corretor'): ?>
            <table class="w-full border border-gray-200 rounded-lg overflow-hidden text-sm">
                <tr class="hover:bg-gray-50">
                    <th class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-2.5 text-left border-b border-gray-200">ID</th><th class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-2.5 text-left border-b border-gray-200">Nome</th><th class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-2.5 text-left border-b border-gray-200">CRECI</th>
                    <th class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-2.5 text-left border-b border-gray-200">Telefone</th><th class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-2.5 text-left border-b border-gray-200">Email</th><th class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-2.5 text-left border-b border-gray-200">Acoes</th>
                </tr>
                <?php foreach ($itens as $r): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-gray-700 border-b border-gray-100"><?= $r->getId() ?></td>
                    <td class="px-4 py-3 text-gray-700 border-b border-gray-100"><?= htmlspecialchars($r->getNome()) ?></td>
                    <td class="px-4 py-3 text-gray-700 border-b border-gray-100"><?= htmlspecialchars($r->getCreci()) ?></td>
                    <td class="px-4 py-3 text-gray-700 border-b border-gray-100"><?= htmlspecialchars($r->getTelefone()) ?></td>
                    <td class="px-4 py-3 text-gray-700 border-b border-gray-100"><?= htmlspecialchars($r->getEmail()) ?></td>
                    <td class="px-4 py-3 text-gray-700 border-b border-gray-100">
                        <a class="text-xs text-gray-500 hover:text-gray-900 px-2 py-0.5 rounded hover:bg-gray-100 transition" href="index.php?entidade=corretor&acao=editar&id=<?= $r->getId() ?>">Editar</a>
                        <a class="text-xs text-red-500 hover:text-red-700 px-2 py-0.5 rounded hover:bg-red-50 transition" href="index.php?entidade=corretor&acao=excluir&id=<?= $r->getId() ?>"
                           onclick="return confirm('Excluir registro?')">Excluir</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>

        <?php elseif ($entidade === 'cliente'): ?>
            <table class="w-full border border-gray-200 rounded-lg overflow-hidden text-sm">
                <tr class="hover:bg-gray-50">
                    <th class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-2.5 text-left border-b border-gray-200">ID</th><th class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-2.5 text-left border-b border-gray-200">Nome</th><th class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-2.5 text-left border-b border-gray-200">CPF</th>
                    <th class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-2.5 text-left border-b border-gray-200">Telefone</th><th class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-2.5 text-left border-b border-gray-200">Email</th><th class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-2.5 text-left border-b border-gray-200">Interesse</th><th class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-2.5 text-left border-b border-gray-200">Acoes</th>
                </tr>
                <?php foreach ($itens as $r): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-gray-700 border-b border-gray-100"><?= $r->getId() ?></td>
                    <td class="px-4 py-3 text-gray-700 border-b border-gray-100"><?= htmlspecialchars($r->getNome()) ?></td>
                    <td class="px-4 py-3 text-gray-700 border-b border-gray-100"><?= htmlspecialchars($r->getCpf()) ?></td>
                    <td class="px-4 py-3 text-gray-700 border-b border-gray-100"><?= htmlspecialchars($r->getTelefone()) ?></td>
                    <td class="px-4 py-3 text-gray-700 border-b border-gray-100"><?= htmlspecialchars($r->getEmail()) ?></td>
                    <td class="px-4 py-3 text-gray-700 border-b border-gray-100"><?= htmlspecialchars($r->getInteresse()) ?></td>
                    <td class="px-4 py-3 text-gray-700 border-b border-gray-100">
                        <a class="text-xs text-gray-500 hover:text-gray-900 px-2 py-0.5 rounded hover:bg-gray-100 transition" href="index.php?entidade=cliente&acao=editar&id=<?= $r->getId() ?>">Editar</a>
                        <a class="text-xs text-red-500 hover:text-red-700 px-2 py-0.5 rounded hover:bg-red-50 transition" href="index.php?entidade=cliente&acao=excluir&id=<?= $r->getId() ?>"
                           onclick="return confirm('Excluir registro?')">Excluir</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>

        <?php elseif ($entidade === 'imovel'): ?>
            <!-- Filtro de finalidade: mostra só os imóveis pra venda ou só pra aluguel -->
            <div class="flex items-center gap-2 mb-3">
                <a class="border border-gray-200 rounded-full text-xs text-gray-500 hover:text-gray-900 px-3 py-1 <?= $filtroFinalidade === '' ? 'bg-gray-100 text-gray-900' : '' ?>" href="index.php?entidade=imovel&acao=listar">Todos</a>
                <a class="border border-gray-200 rounded-full text-xs text-gray-500 hover:text-gray-900 px-3 py-1 <?= $filtroFinalidade === 'venda' ? 'bg-gray-100 text-gray-900' : '' ?>" href="index.php?entidade=imovel&acao=listar&finalidade=venda">Comprar</a>
                <a class="border border-gray-200 rounded-full text-xs text-gray-500 hover:text-gray-900 px-3 py-1 <?= $filtroFinalidade === 'aluguel' ? 'bg-gray-100 text-gray-900' : '' ?>" href="index.php?entidade=imovel&acao=listar&finalidade=aluguel">Alugar</a>
                <?php if ($filtroFinalidade): ?>
                    <span class="text-xs text-gray-500">mostrando apenas: <strong class="text-gray-900"><?= ucfirst($filtroFinalidade) ?></strong></span>
                <?php endif; ?>
            </div>
            <table class="w-full border border-gray-200 rounded-lg overflow-hidden text-sm">
                <tr class="hover:bg-gray-50">
                    <th class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-2.5 text-left border-b border-gray-200">ID</th><th class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-2.5 text-left border-b border-gray-200">Titulo</th><th class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-2.5 text-left border-b border-gray-200">Tipo</th><th class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-2.5 text-left border-b border-gray-200">Endereco</th>
                    <th class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-2.5 text-left border-b border-gray-200">Metros²</th><th class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-2.5 text-left border-b border-gray-200">Valor</th><th class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-2.5 text-left border-b border-gray-200">Finalidade</th>
                    <th class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-2.5 text-left border-b border-gray-200">Status</th><th class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-2.5 text-left border-b border-gray-200">Proprietario</th><th class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-2.5 text-left border-b border-gray-200">Planta</th><th class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-2.5 text-left border-b border-gray-200">Acoes</th>
                </tr>
                <?php foreach ($itens as $r): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-gray-700 border-b border-gray-100"><?= $r->getId() ?></td>
                    <td class="px-4 py-3 text-gray-700 border-b border-gray-100"><?= htmlspecialchars($r->getTitulo()) ?></td>
                    <td class="px-4 py-3 text-gray-700 border-b border-gray-100"><?= htmlspecialchars($r->getTipo()) ?></td>
                    <td class="px-4 py-3 text-gray-700 border-b border-gray-100"><?= htmlspecialchars($r->getEndereco()) ?></td>
                    <td class="px-4 py-3 text-gray-700 border-b border-gray-100"><?= $r->getMetrosQuadrados() ? number_format($r->getMetrosQuadrados(), 0, ',', '.') . ' m²' : '—' ?></td>
                    <!-- number_format transforma o número em formato de moeda brasileira -->
                    <td class="px-4 py-3 text-gray-700 border-b border-gray-100">R$ <?= number_format($r->getValor(), 2, ',', '.') ?></td>
                    <td class="px-4 py-3 text-gray-700 border-b border-gray-100"><?= htmlspecialchars($r->getFinalidade()) ?></td>
                    <td class="px-4 py-3 text-gray-700 border-b border-gray-100"><?= htmlspecialchars($r->getStatus()) ?></td>
                    <td class="px-4 py-3 text-gray-700 border-b border-gray-100"><?= htmlspecialchars($r->getNomeProprietario()) ?></td>
                    <td class="px-4 py-3 text-gray-700 border-b border-gray-100">
                        <?php if ($r->getPlantaBaixa()): ?>
                            <?php
                            $plantaArquivo = basename((string) $r->getPlantaBaixa());
                            $plantaUrl     = 'uploads/' . rawurlencode($plantaArquivo);
                            $plantaCaminho = __DIR__ . '/uploads/' . $plantaArquivo;
                            $isPdf         = strtolower(pathinfo($plantaArquivo, PATHINFO_EXTENSION)) === 'pdf';
                            ?>
                            <?php if (is_file($plantaCaminho)): ?>
                                <a class="text-xs text-teal-600 bg-teal-50 px-2 py-0.5 rounded border border-teal-100" href="<?= $plantaUrl ?>" target="_blank">Ver planta</a>
                                <?php if (!$isPdf): ?>
                                    <a href="<?= $plantaUrl ?>" target="_blank" style="margin-top:6px;display:inline-block;">
                                        <img src="<?= $plantaUrl ?>" alt="Planta" style="width:60px;height:60px;object-fit:cover;border:1px solid #999;display:block;">
                                    </a>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="text-xs text-red-500">arquivo nao encontrado</span>
                            <?php endif; ?>
                        <?php else: ?>
                            <a class="text-xs text-amber-700 bg-amber-50 px-2 py-0.5 rounded border border-amber-200" href="index.php?entidade=imovel&acao=editar&id=<?= $r->getId() ?>">Adicionar planta</a>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3 text-gray-700 border-b border-gray-100">
                        <a class="text-xs text-gray-500 hover:text-gray-900 px-2 py-0.5 rounded hover:bg-gray-100 transition" href="index.php?entidade=imovel&acao=editar&id=<?= $r->getId() ?>">Editar</a>
                        <a class="text-xs text-red-500 hover:text-red-700 px-2 py-0.5 rounded hover:bg-red-50 transition" href="index.php?entidade=imovel&acao=excluir&id=<?= $r->getId() ?>"
                           onclick="return confirm('Excluir registro?')">Excluir</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>

        <?php elseif ($entidade === 'contrato'): ?>
            <?php
            $erroContrato = $_SESSION['form_erro'] ?? '';
            unset($_SESSION['form_erro']);
            if ($erroContrato): ?>
                <div class="login-erro"><?= htmlspecialchars($erroContrato) ?></div>
            <?php endif; ?>
            <table class="w-full border border-gray-200 rounded-lg overflow-hidden text-sm">
                <tr class="hover:bg-gray-50">
                    <th class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-2.5 text-left border-b border-gray-200">ID</th>
                    <th class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-2.5 text-left border-b border-gray-200">Imovel</th>
                    <th class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-2.5 text-left border-b border-gray-200">Cliente</th>
                    <th class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-2.5 text-left border-b border-gray-200">Corretor</th>
                    <th class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-2.5 text-left border-b border-gray-200">Tipo</th>
                    <th class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-2.5 text-left border-b border-gray-200">Valor</th>
                    <th class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-2.5 text-left border-b border-gray-200">Inicio</th>
                    <th class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-2.5 text-left border-b border-gray-200">Fim</th>
                    <th class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-2.5 text-left border-b border-gray-200">Status</th>
                    <th class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-2.5 text-left border-b border-gray-200">Acoes</th>
                </tr>
                <?php foreach ($itens as $r): ?>
                <?php
                $statusClasses = [
                    'ativo'     => 'text-green-700 bg-green-50 border-green-200',
                    'encerrado' => 'text-gray-500 bg-gray-100 border-gray-200',
                    'cancelado' => 'text-red-600 bg-red-50 border-red-200',
                ];
                $statusClass = $statusClasses[$r->getStatus()] ?? 'text-gray-500';
                ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-gray-700 border-b border-gray-100"><?= $r->getId() ?></td>
                    <td class="px-4 py-3 text-gray-700 border-b border-gray-100"><?= htmlspecialchars($r->getImovelTitulo()) ?></td>
                    <td class="px-4 py-3 text-gray-700 border-b border-gray-100"><?= htmlspecialchars($r->getClienteNome()) ?></td>
                    <td class="px-4 py-3 text-gray-700 border-b border-gray-100"><?= htmlspecialchars($r->getCorretorNome()) ?></td>
                    <td class="px-4 py-3 text-gray-700 border-b border-gray-100"><?= ucfirst(htmlspecialchars($r->getTipo())) ?></td>
                    <td class="px-4 py-3 text-gray-700 border-b border-gray-100">R$ <?= number_format($r->getValor(), 2, ',', '.') ?></td>
                    <td class="px-4 py-3 text-gray-700 border-b border-gray-100"><?= htmlspecialchars($r->getDataInicio()) ?></td>
                    <td class="px-4 py-3 text-gray-700 border-b border-gray-100"><?= $r->getDataFim() ? htmlspecialchars($r->getDataFim()) : '—' ?></td>
                    <td class="px-4 py-3 border-b border-gray-100">
                        <span class="text-xs font-medium px-2 py-0.5 rounded border <?= $statusClass ?>"><?= ucfirst($r->getStatus()) ?></span>
                    </td>
                    <td class="px-4 py-3 text-gray-700 border-b border-gray-100 flex gap-1 flex-wrap">
                        <a class="text-xs text-gray-500 hover:text-gray-900 px-2 py-0.5 rounded hover:bg-gray-100 transition" href="index.php?entidade=contrato&acao=editar&id=<?= $r->getId() ?>">Editar</a>
                        <?php if ($r->getStatus() === 'ativo'): ?>
                        <a class="text-xs text-amber-600 hover:text-amber-800 px-2 py-0.5 rounded hover:bg-amber-50 transition" href="index.php?entidade=contrato&acao=encerrar&id=<?= $r->getId() ?>"
                           onclick="return confirm('Encerrar este contrato? Para aluguel, o imovel voltara a ficar disponivel.')">Encerrar</a>
                        <?php endif; ?>
                        <a class="text-xs text-red-500 hover:text-red-700 px-2 py-0.5 rounded hover:bg-red-50 transition" href="index.php?entidade=contrato&acao=excluir&id=<?= $r->getId() ?>"
                           onclick="return confirm('Excluir registro?')">Excluir</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        <?php elseif ($entidade === 'visita'): ?>
            <table class="w-full border border-gray-200 rounded-lg overflow-hidden text-sm">
                <tr class="hover:bg-gray-50">
                    <th class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-2.5 text-left border-b border-gray-200">ID</th><th class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-2.5 text-left border-b border-gray-200">Imovel</th><th class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-2.5 text-left border-b border-gray-200">Nome</th><th class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-2.5 text-left border-b border-gray-200">E-mail</th>
                    <th class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-2.5 text-left border-b border-gray-200">Celular</th><th class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-2.5 text-left border-b border-gray-200">Dia</th><th class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-2.5 text-left border-b border-gray-200">Periodo</th><th class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500 px-4 py-2.5 text-left border-b border-gray-200">Acoes</th>
                </tr>
                <?php
                $diasLabel = [
                    'segunda' => 'Segunda', 'terca' => 'Terça', 'quarta' => 'Quarta',
                    'quinta'  => 'Quinta',  'sexta' => 'Sexta', 'sabado' => 'Sábado',
                    'domingo' => 'Domingo',
                ];
                $periodosLabel = ['manha' => 'Manhã', 'tarde' => 'Tarde', 'noite' => 'Noite'];
                foreach ($itens as $r):
                ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-gray-700 border-b border-gray-100"><?= $r->getId() ?></td>
                    <td class="px-4 py-3 text-gray-700 border-b border-gray-100"><?= htmlspecialchars($r->getImovelTitulo()) ?></td>
                    <td class="px-4 py-3 text-gray-700 border-b border-gray-100"><?= htmlspecialchars($r->getNome()) ?></td>
                    <td class="px-4 py-3 text-gray-700 border-b border-gray-100"><?= htmlspecialchars($r->getEmail()) ?></td>
                    <td class="px-4 py-3 text-gray-700 border-b border-gray-100"><?= htmlspecialchars($r->getCelular()) ?></td>
                    <td class="px-4 py-3 text-gray-700 border-b border-gray-100"><?= $diasLabel[$r->getDiaSemana()] ?? $r->getDiaSemana() ?></td>
                    <td class="px-4 py-3 text-gray-700 border-b border-gray-100"><?= htmlspecialchars($r->getHorarioPreferencia() ?? $periodosLabel[$r->getPeriodo()] ?? $r->getPeriodo()) ?></td>
                    <td class="px-4 py-3 text-gray-700 border-b border-gray-100">
                        <a class="text-xs text-gray-500 hover:text-gray-900 px-2 py-0.5 rounded hover:bg-gray-100 transition" href="index.php?entidade=visita&acao=editar&id=<?= $r->getId() ?>">Editar</a>
                        <a class="text-xs text-red-500 hover:text-red-700 px-2 py-0.5 rounded hover:bg-red-50 transition" href="index.php?entidade=visita&acao=excluir&id=<?= $r->getId() ?>"
                           onclick="return confirm('Excluir registro?')">Excluir</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    <?php endif; ?>

</main>
</body>
</html>
