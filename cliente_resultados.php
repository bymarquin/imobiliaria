<?php
require_once __DIR__ . '/controller/ImovelController.php';

$controller = new ImovelController();

$finalidade = $_GET['finalidade'] ?? '';
$tipo = $_GET['tipo'] ?? '';
$valorMax = isset($_GET['valor_max']) && $_GET['valor_max'] !== '' ? (float) $_GET['valor_max'] : null;

$imoveis = $controller->listar($finalidade);

$imoveisFiltrados = array_values(array_filter($imoveis, static function ($imovel) use ($tipo, $valorMax) {
    if ($imovel->getStatus() !== 'disponivel') {
        return false;
    }

    if ($tipo !== '' && $imovel->getTipo() !== $tipo) {
        return false;
    }

    if ($valorMax !== null && $imovel->getValor() > $valorMax) {
        return false;
    }

    return true;
}));
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Imboliária Ravel</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header>
    <div class="header-inner">
        <div class="left-menu">
            <h1>Portal de Clientes</h1>
            <nav>
                <a href="cliente_busca.php">Nova busca</a>
            </nav>
        </div>
        <div class="user-box">
            <a href="login.php">Area administrativa</a>
        </div>
    </div>
</header>

<main>
    <h2>Imoveis encontrados</h2>
    <p>Resultados com base nos filtros informados.
    <?php if ($finalidade === 'venda'): ?>
        <strong>Comprar</strong>
    <?php elseif ($finalidade === 'aluguel'): ?>
        <strong>Alugar</strong>
    <?php endif; ?>
    </p>

    <div class="portal-grid">
        <?php foreach ($imoveisFiltrados as $imovel): ?>
        <?php
        $finalidadeLabel = $imovel->getFinalidade() === 'venda' ? 'Comprar' : 'Alugar';
        $finalidadeClass = $imovel->getFinalidade() === 'venda' ? 'badge-venda' : 'badge-aluguel';
        ?>
            <article class="portal-card">
                <div class="portal-card-header">
                    <h3><?= htmlspecialchars($imovel->getTitulo()) ?></h3>
                    <span class="portal-badge <?= $finalidadeClass ?>"><?= $finalidadeLabel ?></span>
                </div>
                <p><strong>Tipo:</strong> <?= htmlspecialchars(ucfirst($imovel->getTipo())) ?></p>
                <p><strong>Endereco:</strong> <?= htmlspecialchars($imovel->getEndereco()) ?></p>
                <p><strong>Valor:</strong> R$ <?= number_format($imovel->getValor(), 2, ',', '.') ?></p>
                <p><strong>Area:</strong> <?= $imovel->getMetrosQuadrados() ? number_format($imovel->getMetrosQuadrados(), 0, ',', '.') . ' m²' : 'Nao informado' ?></p>
                <div class="portal-card-footer">
                    <span class="portal-status">Disponivel</span>
                    <a href="cliente_interesse.php?id_imovel=<?= $imovel->getId() ?>&interesse=<?= $imovel->getFinalidade() === 'aluguel' ? 'aluguel' : 'compra' ?>" class="portal-btn-visita">
                        <?= $imovel->getFinalidade() === 'aluguel' ? 'Quero Alugar' : 'Quero Comprar' ?>
                    </a>
                    <a href="cliente_visita.php?id_imovel=<?= $imovel->getId() ?>" class="portal-btn-visita">Agendar Visita</a>
                </div>
            </article>
        <?php endforeach; ?>
    </div>

    <?php if (empty($imoveisFiltrados)): ?>
        <p>Nenhum imovel encontrado para os filtros selecionados.</p>
    <?php endif; ?>
</main>
</body>
</html>
