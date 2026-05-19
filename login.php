<?php
session_start();
require_once __DIR__ . '/config/conexao.php';

if (!empty($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = trim($_POST['senha'] ?? '');

    if ($email !== '' && $senha !== '') {
        $conn = Conexao::getConn();

        $stmt = $conn->prepare('SELECT id, nome FROM usuarios WHERE email = ? AND senha = ? LIMIT 1');
        $stmt->execute([$email, $senha]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {
            $_SESSION['usuario_id']   = (int) $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            header('Location: index.php');
            exit;
        }
    }

    $erro = 'E-mail ou senha invalidos.';
}
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
                <input type="email" name="email" required placeholder="Digite seu e-mail">
            </label>

            <label>Senha
                <input type="password" name="senha" required placeholder="Digite sua senha">
            </label>

            <button type="submit">Entrar</button>
        </form>
        <p><a href="cliente_busca.php">Acessar portal de clientes</a></p>
        </div>
    </div>

</body>
</html>
