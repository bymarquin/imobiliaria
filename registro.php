<?php
session_start();
require_once __DIR__ . '/config/conexao.php';

if (!empty($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = trim($_POST['senha'] ?? '');
    $confirmarSenha = trim($_POST['confirmar_senha'] ?? '');

    if ($nome === '' || $email === '' || $senha === '' || $confirmarSenha === '') {
        $erro = 'Preencha todos os campos.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'E-mail invalido.';
    } elseif ($senha !== $confirmarSenha) {
        $erro = 'As senhas nao conferem.';
    } else {
        $conn = Conexao::getConn();
        $check = $conn->prepare('SELECT id FROM usuarios WHERE email = ? LIMIT 1');
        $check->execute([$email]);
        $existe = $check->fetch(PDO::FETCH_ASSOC);

        if ($existe) {
            $erro = 'E-mail ja cadastrado.';
        } else {
            $insert = $conn->prepare('INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)');
            $insert->execute([$nome, $email, $senha]);
            $sucesso = 'Cadastro realizado. Acesse o login.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Administrador</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="login-wrap">
        <div class="login-box">
            <h1>Registro de Administrador</h1>
            <p>Crie um usuario com acesso ao painel interno</p>

            <?php if ($erro): ?>
                <div class="login-erro">
                    <?= htmlspecialchars($erro) ?>
                </div>
            <?php endif; ?>

            <?php if ($sucesso): ?>
                <div class="login-ok">
                    <?= htmlspecialchars($sucesso) ?>
                </div>
            <?php endif; ?>

            <form method="post">
                <label>Nome
                    <input type="text" name="nome" required placeholder="Digite seu nome">
                </label>

                <label>E-mail
                    <input type="email" name="email" required placeholder="Digite seu e-mail">
                </label>

                <label>Senha
                    <input type="password" name="senha" required placeholder="Digite sua senha">
                </label>

                <label>Confirmar senha
                    <input type="password" name="confirmar_senha" required placeholder="Repita sua senha">
                </label>

                <button type="submit">Registrar</button>
            </form>
            <p><a href="login.php">Voltar para login</a></p>
            <p><a href="portal.php">Ir para portal de clientes</a></p>
        </div>
    </div>

</body>
</html>
