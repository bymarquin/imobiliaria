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
    <title>Busca de Imoveis</title>
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
    <p>Escolha os filtros e veja os resultados da busca.</p>

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
