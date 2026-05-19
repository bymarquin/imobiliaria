<?php
session_start();

$adminEmail = 'admin@imobiliaria.local';
$adminSenha = 'admin123';
$adminNome = 'Administrador';

if (!empty($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = trim($_POST['senha'] ?? '');

    if ($email === $adminEmail && $senha === $adminSenha) {
        $_SESSION['usuario_id']   = 1;
        $_SESSION['usuario_nome'] = $adminNome;
        header('Location: index.php');
        exit;
    }

    $erro = 'E-mail ou senha invalidos.';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Painel Administrativo</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="login-wrap">
        <div class="login-box">
        <h1>Login Administrativo</h1>
        <p>Acesso restrito ao painel interno da imobiliaria</p>

        <?php if ($erro): ?>
            <!-- Mostra o erro quando o login falha -->
            <div class="login-erro">
                <?= htmlspecialchars($erro) ?>
            </div>
        <?php endif; ?>

        <form method="post">
            <label>E-mail
                <input type="email" name="email" required placeholder="Digite seu e-mail" value="<?= htmlspecialchars($adminEmail) ?>">
            </label>

            <label>Senha
                <input type="password" name="senha" required placeholder="Digite sua senha">
            </label>

            <button type="submit">Entrar</button>
        </form>
        <p class="text-xs text-gray-500">Usuario fixo: <?= htmlspecialchars($adminEmail) ?></p>
        <p><a href="cliente_busca.php">Acessar portal de clientes</a></p>
        </div>
    </div>

</body>
</html>
