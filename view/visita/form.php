<?php

$editando = $visita && $visita->getId();

$diasSemana = [
    'segunda' => 'Segunda-feira',
    'terca'   => 'Terça-feira',
    'quarta'  => 'Quarta-feira',
    'quinta'  => 'Quinta-feira',
    'sexta'   => 'Sexta-feira',
    'sabado'  => 'Sábado',
    'domingo' => 'Domingo',
];

?>
<h2 class="text-xl font-semibold text-gray-900 mb-6"><?= $editando ? 'Editar Visita' : 'Agendar Visita' ?></h2>

<form method="post" action="index.php?entidade=visita&acao=salvar" class="bg-white border border-gray-200 rounded-lg p-7 max-w-lg flex flex-col gap-5">

    <?php if ($editando): ?>
        <input type="hidden" name="id" value="<?= $visita->getId() ?>">
    <?php endif; ?>

    <label class="flex flex-col gap-1.5 text-xs font-medium text-gray-500 uppercase tracking-wide">Imovel
        <select name="id_imovel" required class="w-full px-3 py-2 text-sm text-gray-900 bg-white border border-gray-300 rounded-md outline-none focus:border-gray-500 focus:ring-2 focus:ring-gray-200 transition">
            <option value="" disabled <?= empty($visita?->getIdImovel()) ? 'selected' : '' ?>>Selecione o imovel</option>
            <?php foreach ($imoveis as $i): ?>
            <option value="<?= $i->getId() ?>"
                <?= (($visita?->getIdImovel() ?? 0) === $i->getId()) ? 'selected' : '' ?>>
                <?= htmlspecialchars($i->getTitulo()) ?>
                (<?= ucfirst($i->getFinalidade()) ?> — R$ <?= number_format($i->getValor(), 2, ',', '.') ?>)
            </option>
            <?php endforeach; ?>
        </select>
    </label>

    <label class="flex flex-col gap-1.5 text-xs font-medium text-gray-500 uppercase tracking-wide">Seu Nome
        <input type="text" name="nome" required class="w-full px-3 py-2 text-sm text-gray-900 bg-white border border-gray-300 rounded-md outline-none focus:border-gray-500 focus:ring-2 focus:ring-gray-200 transition"
               placeholder="Digite seu nome"
               value="<?= htmlspecialchars($visita?->getNome() ?? '') ?>">
    </label>

    <label class="flex flex-col gap-1.5 text-xs font-medium text-gray-500 uppercase tracking-wide">E-mail
        <input type="email" name="email" required class="w-full px-3 py-2 text-sm text-gray-900 bg-white border border-gray-300 rounded-md outline-none focus:border-gray-500 focus:ring-2 focus:ring-gray-200 transition"
               placeholder="nome@exemplo.com"
               value="<?= htmlspecialchars($visita?->getEmail() ?? '') ?>">
    </label>

    <label class="flex flex-col gap-1.5 text-xs font-medium text-gray-500 uppercase tracking-wide">Celular
        <input type="tel" name="celular" placeholder="(00) 00000-0000" required class="w-full px-3 py-2 text-sm text-gray-900 bg-white border border-gray-300 rounded-md outline-none focus:border-gray-500 focus:ring-2 focus:ring-gray-200 transition"
               value="<?= htmlspecialchars($visita?->getCelular() ?? '') ?>">
    </label>

    <label class="flex flex-col gap-1.5 text-xs font-medium text-gray-500 uppercase tracking-wide">Dia da Semana de Preferencia
        <select name="dia_semana" required class="w-full px-3 py-2 text-sm text-gray-900 bg-white border border-gray-300 rounded-md outline-none focus:border-gray-500 focus:ring-2 focus:ring-gray-200 transition">
            <option value="" disabled <?= empty($visita?->getDiaSemana()) ? 'selected' : '' ?>>Selecione o dia da semana</option>
            <?php foreach ($diasSemana as $valor => $label): ?>
            <option value="<?= $valor ?>"
                <?= (($visita?->getDiaSemana() ?? '') === $valor) ? 'selected' : '' ?>>
                <?= $label ?>
            </option>
            <?php endforeach; ?>
        </select>
    </label>

    <label class="flex flex-col gap-1.5 text-xs font-medium text-gray-500 uppercase tracking-wide">Horario de Preferencia
        <input type="time" name="horario_preferencia" required placeholder="hh:mm" class="w-full px-3 py-2 text-sm text-gray-900 bg-white border border-gray-300 rounded-md outline-none focus:border-gray-500 focus:ring-2 focus:ring-gray-200 transition appearance-none cursor-pointer"
               value="<?= htmlspecialchars($visita?->getHorarioPreferencia() ?? '') ?>">
    </label>

    <button type="submit" class="self-start bg-gray-900 text-white text-xs font-medium px-5 py-2 rounded-md hover:bg-gray-700 transition cursor-pointer">Agendar Visita</button>
    <a href="index.php?entidade=visita&acao=listar" class="self-start text-xs text-gray-400 hover:text-gray-700 hover:underline transition">Cancelar</a>
</form>
