<?php

$editando = $contrato && $contrato->getId();
$erro = $_SESSION['form_erro'] ?? '';
unset($_SESSION['form_erro']);
$tipoAtual = $contrato?->getTipo() ?? '';
?>
<h2 class="text-xl font-semibold text-gray-900 mb-6"><?= $editando ? 'Editar Contrato' : 'Novo Contrato' ?></h2>

<?php if ($erro): ?>
    <div class="login-erro"><?= htmlspecialchars($erro) ?></div>
<?php endif; ?>

<form method="post" action="index.php?entidade=contrato&acao=salvar" class="bg-white border border-gray-200 rounded-lg p-7 max-w-lg flex flex-col gap-5">

    <?php if ($editando): ?>
        <input type="hidden" name="id" value="<?= $contrato->getId() ?>">
    <?php endif; ?>

    <label class="flex flex-col gap-1.5 text-xs font-medium text-gray-500 uppercase tracking-wide">Tipo
        <select name="tipo" id="tipo" required onchange="filtrarImoveis()" class="w-full px-3 py-2 text-sm text-gray-900 bg-white border border-gray-300 rounded-md outline-none focus:border-gray-500 focus:ring-2 focus:ring-gray-200 transition">
            <option value="" disabled <?= empty($tipoAtual) ? 'selected' : '' ?>>Selecione o tipo</option>
            <?php foreach (['venda', 'aluguel'] as $op): ?>
            <option value="<?= $op ?>" <?= $tipoAtual === $op ? 'selected' : '' ?>><?= ucfirst($op) ?></option>
            <?php endforeach; ?>
        </select>
    </label>

    <label class="flex flex-col gap-1.5 text-xs font-medium text-gray-500 uppercase tracking-wide">Imovel
        <select name="id_imovel" id="id_imovel" required class="w-full px-3 py-2 text-sm text-gray-900 bg-white border border-gray-300 rounded-md outline-none focus:border-gray-500 focus:ring-2 focus:ring-gray-200 transition">
            <option value="" disabled selected>Selecione o tipo primeiro</option>
            <?php foreach ($imoveis as $i): ?>
            <?php
            $isSelecionado = (($contrato?->getIdImovel() ?? 0) === $i->getId());
            $isDisponivel  = $i->getStatus() === 'disponivel';
            ?>
            <option value="<?= $i->getId() ?>"
                data-finalidade="<?= $i->getFinalidade() ?>"
                <?= $isSelecionado ? 'selected' : '' ?>
                <?= (!$isDisponivel && !$isSelecionado) ? 'disabled' : '' ?>>
                <?= htmlspecialchars($i->getTitulo()) ?> (<?= $i->getFinalidade() ?>)<?= $isDisponivel ? '' : ' - indisponivel' ?>
            </option>
            <?php endforeach; ?>
        </select>
    </label>

    <label class="flex flex-col gap-1.5 text-xs font-medium text-gray-500 uppercase tracking-wide">Cliente
        <select name="id_cliente" required class="w-full px-3 py-2 text-sm text-gray-900 bg-white border border-gray-300 rounded-md outline-none focus:border-gray-500 focus:ring-2 focus:ring-gray-200 transition">
            <option value="" disabled <?= empty($contrato?->getIdCliente()) ? 'selected' : '' ?>>Selecione o cliente</option>
            <?php foreach ($clientes as $c): ?>
            <option value="<?= $c->getId() ?>" <?= (($contrato?->getIdCliente() ?? 0) === $c->getId()) ? 'selected' : '' ?>>
                <?= htmlspecialchars($c->getNome()) ?>
            </option>
            <?php endforeach; ?>
        </select>
    </label>

    <label class="flex flex-col gap-1.5 text-xs font-medium text-gray-500 uppercase tracking-wide">Corretor
        <select name="id_corretor" required class="w-full px-3 py-2 text-sm text-gray-900 bg-white border border-gray-300 rounded-md outline-none focus:border-gray-500 focus:ring-2 focus:ring-gray-200 transition">
            <option value="" disabled <?= empty($contrato?->getIdCorretor()) ? 'selected' : '' ?>>Selecione o corretor</option>
            <?php foreach ($corretores as $c): ?>
            <option value="<?= $c->getId() ?>" <?= (($contrato?->getIdCorretor() ?? 0) === $c->getId()) ? 'selected' : '' ?>>
                <?= htmlspecialchars($c->getNome()) ?>
            </option>
            <?php endforeach; ?>
        </select>
    </label>

    <label class="flex flex-col gap-1.5 text-xs font-medium text-gray-500 uppercase tracking-wide">Valor (R$)
        <input type="number" step="0.01" min="0" name="valor" required
               class="w-full px-3 py-2 text-sm text-gray-900 bg-white border border-gray-300 rounded-md outline-none focus:border-gray-500 focus:ring-2 focus:ring-gray-200 transition"
               placeholder="Ex.: 1200.00"
               value="<?= $contrato?->getValor() ?? '' ?>">
    </label>

    <label class="flex flex-col gap-1.5 text-xs font-medium text-gray-500 uppercase tracking-wide">Data inicio
        <input type="date" name="data_inicio" required
               class="w-full px-3 py-2 text-sm text-gray-900 bg-white border border-gray-300 rounded-md outline-none focus:border-gray-500 focus:ring-2 focus:ring-gray-200 transition"
               value="<?= $contrato?->getDataInicio() ?? '' ?>">
    </label>

    <label id="label_data_fim" class="flex flex-col gap-1.5 text-xs font-medium text-gray-500 uppercase tracking-wide">
        Data fim <span id="data_fim_obs" class="text-gray-400 normal-case font-normal"></span>
        <input type="date" name="data_fim" id="data_fim"
               class="w-full px-3 py-2 text-sm text-gray-900 bg-white border border-gray-300 rounded-md outline-none focus:border-gray-500 focus:ring-2 focus:ring-gray-200 transition"
               value="<?= $contrato?->getDataFim() ?? '' ?>">
    </label>

    <button type="submit" class="self-start bg-gray-900 text-white text-xs font-medium px-5 py-2 rounded-md hover:bg-gray-700 transition cursor-pointer">Salvar</button>
    <a href="index.php?entidade=contrato&acao=listar" class="self-start text-xs text-gray-400 hover:text-gray-700 hover:underline transition">Cancelar</a>
</form>

<script>
function filtrarImoveis() {
    const tipo = document.getElementById('tipo').value;
    const select = document.getElementById('id_imovel');
    const dataFim = document.getElementById('data_fim');
    const obs = document.getElementById('data_fim_obs');

    Array.from(select.options).forEach(opt => {
        if (!opt.value) return;
        const finalidade = opt.dataset.finalidade;
        opt.hidden = finalidade !== tipo;
    });

    const selectedOpt = select.options[select.selectedIndex];
    if (selectedOpt && selectedOpt.dataset.finalidade !== tipo) {
        select.value = '';
    }

    if (!select.querySelector('option[value=""]:not([disabled])')) {
        select.options[0].textContent = tipo ? 'Selecione o imovel' : 'Selecione o tipo primeiro';
    }

    if (tipo === 'aluguel') {
        dataFim.required = true;
        obs.textContent = '(obrigatorio para aluguel)';
    } else {
        dataFim.required = false;
        obs.textContent = '(opcional para venda)';
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const tipo = document.getElementById('tipo').value;
    if (tipo) filtrarImoveis();
});
</script>
