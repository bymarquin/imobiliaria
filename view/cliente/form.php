<?php

$editando = $cliente && $cliente->getId();
?>
<h2 class="text-xl font-semibold text-gray-900 mb-6"><?= $editando ? 'Editar Cliente' : 'Novo Cliente' ?></h2>

<form method="post" action="index.php?entidade=cliente&acao=salvar" class="bg-white border border-gray-200 rounded-lg p-7 max-w-lg flex flex-col gap-5">

    <?php if ($editando): ?>
        <input type="hidden" name="id" value="<?= $cliente->getId() ?>">
    <?php endif; ?>

    <label class="flex flex-col gap-1.5 text-xs font-medium text-gray-500 uppercase tracking-wide">Nome
        <input type="text" name="nome" required class="w-full px-3 py-2 text-sm text-gray-900 bg-white border border-gray-300 rounded-md outline-none focus:border-gray-500 focus:ring-2 focus:ring-gray-200 transition"
               placeholder="Digite o nome"
               value="<?= htmlspecialchars($cliente?->getNome() ?? '') ?>">
    </label>

    <label class="flex flex-col gap-1.5 text-xs font-medium text-gray-500 uppercase tracking-wide">CPF
        <input type="text" name="cpf" required class="w-full px-3 py-2 text-sm text-gray-900 bg-white border border-gray-300 rounded-md outline-none focus:border-gray-500 focus:ring-2 focus:ring-gray-200 transition"
               placeholder="000.000.000-00"
               value="<?= htmlspecialchars($cliente?->getCpf() ?? '') ?>">
    </label>

    <label class="flex flex-col gap-1.5 text-xs font-medium text-gray-500 uppercase tracking-wide">Telefone
        <input type="text" name="telefone" class="w-full px-3 py-2 text-sm text-gray-900 bg-white border border-gray-300 rounded-md outline-none focus:border-gray-500 focus:ring-2 focus:ring-gray-200 transition"
               placeholder="(00) 00000-0000"
               value="<?= htmlspecialchars($cliente?->getTelefone() ?? '') ?>">
    </label>

    <label class="flex flex-col gap-1.5 text-xs font-medium text-gray-500 uppercase tracking-wide">Email
        <input type="email" name="email" class="w-full px-3 py-2 text-sm text-gray-900 bg-white border border-gray-300 rounded-md outline-none focus:border-gray-500 focus:ring-2 focus:ring-gray-200 transition"
               placeholder="nome@exemplo.com"
               value="<?= htmlspecialchars($cliente?->getEmail() ?? '') ?>">
    </label>

    <label class="flex flex-col gap-1.5 text-xs font-medium text-gray-500 uppercase tracking-wide">Interesse
        <select name="interesse" required class="w-full px-3 py-2 text-sm text-gray-900 bg-white border border-gray-300 rounded-md outline-none focus:border-gray-500 focus:ring-2 focus:ring-gray-200 transition">
            <option value="" disabled <?= empty($cliente?->getInteresse()) ? 'selected' : '' ?>>Selecione o interesse</option>
            <?php foreach (['compra', 'aluguel'] as $op): ?>
            <option value="<?= $op ?>"
                <?= (($cliente?->getInteresse() ?? '') === $op) ? 'selected' : '' ?>>
                <?= ucfirst($op) ?>
            </option>
            <?php endforeach; ?>
        </select>
    </label>

    <button type="submit" class="self-start bg-gray-900 text-white text-xs font-medium px-5 py-2 rounded-md hover:bg-gray-700 transition cursor-pointer">Salvar</button>
    <a href="index.php?entidade=cliente&acao=listar" class="self-start text-xs text-gray-400 hover:text-gray-700 hover:underline transition">Cancelar</a>
</form>
