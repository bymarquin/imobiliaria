<?php
$finalidade = $_GET['finalidade'] ?? '';
$tipo = $_GET['tipo'] ?? '';
$valorMax = $_GET['valor_max'] ?? '';
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
        </div>
        <div class="user-box">
            <a href="login.php">Area administrativa</a>
        </div>
    </div>
</header>

<main>
    <h2>Encontre seu imovel</h2>
    <p>O que voce esta procurando?</p>

    <div class="portal-opcoes">
        <a href="cliente_resultados.php?finalidade=venda" class="portal-opcao portal-opcao-comprar">
            <span class="portal-opcao-icone">🏠</span>
            <strong>Quero Comprar</strong>
            <span>Ver imoveis a venda</span>
        </a>
        <a href="cliente_resultados.php?finalidade=aluguel" class="portal-opcao portal-opcao-alugar">
            <span class="portal-opcao-icone">🔑</span>
            <strong>Quero Alugar</strong>
            <span>Ver imoveis para aluguel</span>
        </a>
    </div>

    <hr class="portal-divisor">
    <p class="portal-filtro-titulo">Ou use os filtros avancados:</p>

    <form method="get" action="cliente_resultados.php">
        <label>Finalidade
            <select name="finalidade">
                <option value="" <?= $finalidade === '' ? 'selected' : '' ?>>Todas</option>
                <option value="venda" <?= $finalidade === 'venda' ? 'selected' : '' ?>>Comprar</option>
                <option value="aluguel" <?= $finalidade === 'aluguel' ? 'selected' : '' ?>>Alugar</option>
            </select>
        </label>

        <label>Tipo do imovel
            <select name="tipo">
                <option value="" <?= $tipo === '' ? 'selected' : '' ?>>Todos</option>
                <option value="casa" <?= $tipo === 'casa' ? 'selected' : '' ?>>Casa</option>
                <option value="apartamento" <?= $tipo === 'apartamento' ? 'selected' : '' ?>>Apartamento</option>
                <option value="terreno" <?= $tipo === 'terreno' ? 'selected' : '' ?>>Terreno</option>
                <option value="comercial" <?= $tipo === 'comercial' ? 'selected' : '' ?>>Comercial</option>
            </select>
        </label>

        <label>Valor maximo (R$)
            <input type="number" name="valor_max" min="0" step="0.01" value="<?= htmlspecialchars((string) $valorMax) ?>" placeholder="Ex.: 350000">
        </label>

        <button type="submit">Buscar imoveis</button>
    </form>
</main>
</body>
</html>
