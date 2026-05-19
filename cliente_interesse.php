<?php
require_once __DIR__ . '/config/conexao.php';
require_once __DIR__ . '/controller/ImovelController.php';

$idImovel = (int) ($_GET['id_imovel'] ?? $_POST['id_imovel'] ?? 0);
$imovelController = new ImovelController();
$imovel = $imovelController->buscarPorId($idImovel);

if (!$imovel || $imovel->getStatus() !== 'disponivel') {
    header('Location: cliente_busca.php');
    exit;
}

$interesse = $_GET['interesse'] ?? $_POST['interesse'] ?? '';
if (!in_array($interesse, ['compra', 'aluguel'], true)) {
    $interesse = $imovel->getFinalidade() === 'aluguel' ? 'aluguel' : 'compra';
}

$erro = '';
$sucesso = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $cpf = trim($_POST['cpf'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if ($nome === '' || $cpf === '') {
        $erro = 'Informe pelo menos nome e CPF.';
    } else {
        $conn = Conexao::getConn();
        $stmt = $conn->prepare(
            'INSERT INTO clientes (nome, cpf, telefone, email, interesse)
             VALUES (?, ?, ?, ?, ?)
             ON CONFLICT(cpf) DO UPDATE SET
                 nome = excluded.nome,
                 telefone = excluded.telefone,
                 email = excluded.email,
                 interesse = excluded.interesse'
        );

        $stmt->execute([$nome, $cpf, $telefone, $email, $interesse]);
        $sucesso = true;
    }
}

$interesseLabel = $interesse === 'aluguel' ? 'alugar' : 'comprar';
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
                <a href="cliente_busca.php">Busca</a>
            </nav>
        </div>
        <div class="user-box">
            <a href="login.php">Area administrativa</a>
        </div>
    </div>
</header>

<main>
    <?php if ($sucesso): ?>
        <div class="login-ok">
            Recebemos seu interesse em <?= htmlspecialchars($interesseLabel) ?> este imovel. Nossa equipe vai entrar em contato.
        </div>
        <p><a href="cliente_resultados.php?finalidade=<?= urlencode($imovel->getFinalidade()) ?>">Voltar aos resultados</a></p>
    <?php else: ?>
        <h2>Quero <?= htmlspecialchars($interesseLabel) ?></h2>
        <p>Imovel: <strong><?= htmlspecialchars($imovel->getTitulo()) ?></strong> - <?= htmlspecialchars($imovel->getEndereco()) ?></p>

        <?php if ($erro): ?>
            <div class="login-erro"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>

        <form method="post">
            <input type="hidden" name="id_imovel" value="<?= $imovel->getId() ?>">
            <input type="hidden" name="interesse" value="<?= htmlspecialchars($interesse) ?>">

            <label>Nome completo
                <input type="text" name="nome" required placeholder="Seu nome">
            </label>

            <label>CPF
                <input type="text" name="cpf" required placeholder="Somente numeros ou formatado">
            </label>

            <label>Telefone
                <input type="text" name="telefone" placeholder="(00) 00000-0000">
            </label>

            <label>E-mail
                <input type="email" name="email" placeholder="seu@email.com">
            </label>

            <button type="submit">Enviar interesse</button>
            &nbsp;<a href="javascript:history.back()">Voltar</a>
        </form>
    <?php endif; ?>
</main>
</body>
</html>
