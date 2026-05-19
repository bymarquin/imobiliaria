<?php
session_start();
require_once __DIR__ . '/controller/ImovelController.php';
require_once __DIR__ . '/controller/VisitaController.php';

$idImovel = (int) ($_GET['id_imovel'] ?? 0);
$imovelCtrl = new ImovelController();
$imovel = $imovelCtrl->buscarPorId($idImovel);

if (!$imovel || $imovel->getStatus() !== 'disponivel') {
    header('Location: cliente_busca.php');
    exit;
}

$erro = '';
$sucesso = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $visitaCtrl = new VisitaController();
        $visitaCtrl->salvar($_POST);
        $sucesso = true;
    } catch (RuntimeException $e) {
        $erro = $e->getMessage();
    }
}

$dias = ['segunda', 'terca', 'quarta', 'quinta', 'sexta', 'sabado', 'domingo'];
$diasLabel = [
    'segunda' => 'Segunda-feira', 'terca' => 'Terca-feira', 'quarta' => 'Quarta-feira',
    'quinta'  => 'Quinta-feira',  'sexta' => 'Sexta-feira', 'sabado' => 'Sabado', 'domingo' => 'Domingo',
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendar Visita</title>
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
            Visita agendada com sucesso! Entraremos em contato para confirmar.
        </div>
        <p><a href="cliente_busca.php">Voltar para busca</a></p>
    <?php else: ?>
        <h2>Agendar Visita</h2>
        <p>Imovel: <strong><?= htmlspecialchars($imovel->getTitulo()) ?></strong> &mdash; <?= htmlspecialchars($imovel->getEndereco()) ?></p>

        <?php if ($erro): ?>
            <div class="login-erro"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>

        <form method="post" class="bg-white border border-gray-200 rounded-lg p-7 max-w-lg flex flex-col gap-5">
            <input type="hidden" name="id_imovel" value="<?= $imovel->getId() ?>">

            <label class="flex flex-col gap-1.5 text-xs font-medium text-gray-500 uppercase tracking-wide">Seu nome
                <input type="text" name="nome" required placeholder="Nome completo"
                       class="w-full px-3 py-2 text-sm text-gray-900 bg-white border border-gray-300 rounded-md outline-none focus:border-gray-500 transition">
            </label>

            <label class="flex flex-col gap-1.5 text-xs font-medium text-gray-500 uppercase tracking-wide">E-mail
                <input type="email" name="email" required placeholder="seu@email.com"
                       class="w-full px-3 py-2 text-sm text-gray-900 bg-white border border-gray-300 rounded-md outline-none focus:border-gray-500 transition">
            </label>

            <label class="flex flex-col gap-1.5 text-xs font-medium text-gray-500 uppercase tracking-wide">Celular
                <input type="text" name="celular" required placeholder="(00) 00000-0000"
                       class="w-full px-3 py-2 text-sm text-gray-900 bg-white border border-gray-300 rounded-md outline-none focus:border-gray-500 transition">
            </label>

            <label class="flex flex-col gap-1.5 text-xs font-medium text-gray-500 uppercase tracking-wide">Dia preferido
                <select name="dia_semana" required class="w-full px-3 py-2 text-sm text-gray-900 bg-white border border-gray-300 rounded-md outline-none focus:border-gray-500 transition">
                    <option value="" disabled selected>Selecione o dia</option>
                    <?php foreach ($dias as $d): ?>
                    <option value="<?= $d ?>"><?= $diasLabel[$d] ?></option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label class="flex flex-col gap-1.5 text-xs font-medium text-gray-500 uppercase tracking-wide">Horario preferido
                <input type="time" name="horario_preferencia" required
                       class="w-full px-3 py-2 text-sm text-gray-900 bg-white border border-gray-300 rounded-md outline-none focus:border-gray-500 transition">
            </label>

            <button type="submit" class="self-start bg-gray-900 text-white text-xs font-medium px-5 py-2 rounded-md hover:bg-gray-700 transition cursor-pointer">Confirmar Agendamento</button>
            <a href="javascript:history.back()" class="self-start text-xs text-gray-400 hover:text-gray-700 hover:underline transition">Voltar</a>
        </form>
    <?php endif; ?>
</main>
</body>
</html>
