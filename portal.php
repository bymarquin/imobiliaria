<?php
require_once __DIR__ . '/controller/ImovelController.php';
require_once __DIR__ . '/controller/VisitaController.php';

$imovelController = new ImovelController();
$visitaController = new VisitaController();

$erro = '';
$sucesso = isset($_GET['sucesso']) && $_GET['sucesso'] === '1';
$filtroFinalidade = $_GET['finalidade'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $visitaController->salvar($_POST);
        header('Location: portal.php?sucesso=1#agendar');
        exit;
    } catch (Throwable $e) {
        $erro = 'Nao foi possivel concluir o agendamento. Confira os dados e tente novamente.';
    }
}

$imoveis = $imovelController->listar($filtroFinalidade);
$imoveisDisponiveis = array_values(array_filter($imoveis, static function ($imovel) {
    return $imovel->getStatus() === 'disponivel';
}));

$diasSemana = [
    'segunda' => 'Segunda-feira',
    'terca'   => 'Terca-feira',
    'quarta'  => 'Quarta-feira',
    'quinta'  => 'Quinta-feira',
    'sexta'   => 'Sexta-feira',
    'sabado'  => 'Sabado',
    'domingo' => 'Domingo',
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal de Clientes - Imobiliaria</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header>
    <div class="header-inner">
        <div class="left-menu">
            <h1>Portal de Clientes</h1>
            <nav>
                <a href="portal.php" class="<?= $filtroFinalidade === '' ? 'active' : '' ?>">Todos</a>
                <a href="portal.php?finalidade=venda" class="<?= $filtroFinalidade === 'venda' ? 'active' : '' ?>">Comprar</a>
                <a href="portal.php?finalidade=aluguel" class="<?= $filtroFinalidade === 'aluguel' ? 'active' : '' ?>">Alugar</a>
                <a href="#agendar">Agendar visita</a>
            </nav>
        </div>
        <div class="user-box">
            <a href="login.php">Area administrativa</a>
        </div>
    </div>
</header>

<main>
    <h2>Imoveis disponiveis</h2>
    <p>Explore os imoveis e solicite um agendamento sem precisar fazer login.</p>

    <div class="portal-grid">
        <?php foreach ($imoveisDisponiveis as $imovel): ?>
            <article class="portal-card">
                <h3><?= htmlspecialchars($imovel->getTitulo()) ?></h3>
                <p><strong>Tipo:</strong> <?= htmlspecialchars(ucfirst($imovel->getTipo())) ?></p>
                <p><strong>Endereco:</strong> <?= htmlspecialchars($imovel->getEndereco()) ?></p>
                <p><strong>Finalidade:</strong> <?= htmlspecialchars(ucfirst($imovel->getFinalidade())) ?></p>
                <p><strong>Valor:</strong> R$ <?= number_format($imovel->getValor(), 2, ',', '.') ?></p>
                <p><strong>Area:</strong> <?= $imovel->getMetrosQuadrados() ? number_format($imovel->getMetrosQuadrados(), 0, ',', '.') . ' m2' : 'Nao informado' ?></p>
            </article>
        <?php endforeach; ?>
    </div>

    <?php if (empty($imoveisDisponiveis)): ?>
        <p>Nenhum imovel disponivel no filtro atual.</p>
    <?php endif; ?>

    <hr>

    <h2 id="agendar">Agendar visita</h2>

    <?php if ($sucesso): ?>
        <div class="login-ok">Agendamento enviado com sucesso. Nossa equipe entrara em contato.</div>
    <?php endif; ?>

    <?php if ($erro): ?>
        <div class="login-erro"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>

    <form method="post" action="portal.php#agendar">
        <label>Imovel
            <select name="id_imovel" required>
                <option value="" selected disabled>Selecione um imovel</option>
                <?php foreach ($imoveisDisponiveis as $imovel): ?>
                    <option value="<?= $imovel->getId() ?>">
                        <?= htmlspecialchars($imovel->getTitulo()) ?>
                        (<?= ucfirst($imovel->getFinalidade()) ?> - R$ <?= number_format($imovel->getValor(), 2, ',', '.') ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>Seu nome
            <input type="text" name="nome" required placeholder="Digite seu nome completo">
        </label>

        <label>E-mail
            <input type="email" name="email" required placeholder="nome@exemplo.com">
        </label>

        <label>Celular
            <input type="tel" name="celular" required placeholder="(00) 00000-0000">
        </label>

        <label>Dia da semana
            <select name="dia_semana" required>
                <option value="" selected disabled>Selecione um dia</option>
                <?php foreach ($diasSemana as $valor => $label): ?>
                    <option value="<?= $valor ?>"><?= $label ?></option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>Horario de preferencia
            <input type="time" name="horario_preferencia" required>
        </label>

        <button type="submit">Solicitar agendamento</button>
    </form>
</main>
</body>
</html>
