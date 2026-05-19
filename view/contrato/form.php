<?php
/**
 * Formulário de cadastro e edição de contratos.
 *
 * Este é o formulário mais complexo do sistema porque cruza três entidades:
 * o imóvel negociado, o cliente interessado e o corretor responsável.
 * Todos os três selects são populados dinamicamente pelo controller.
 *
 * A data fim é opcional: para contratos de venda não há prazo de encerramento,
 * enquanto para aluguéis é prática comum definir a data de término.
 */

$editando = $contrato && $contrato->getId();
$erro = $_SESSION['form_erro'] ?? '';
unset($_SESSION['form_erro']);
?>
<h2 class="text-xl font-semibold text-gray-900 mb-6"><?= $editando ? 'Editar Contrato' : 'Novo Contrato' ?></h2>

<?php if ($erro): ?>
    <div class="login-erro"><?= htmlspecialchars($erro) ?></div>
<?php endif; ?>

<form method="post" action="index.php?entidade=contrato&acao=salvar" class="bg-white border border-gray-200 rounded-lg p-7 max-w-lg flex flex-col gap-5">

    <?php if ($editando): ?>
        <!-- ID necessário para o controller identificar que é uma edição -->
        <input type="hidden" name="id" value="<?= $contrato->getId() ?>">
    <?php endif; ?>

    <label class="flex flex-col gap-1.5 text-xs font-medium text-gray-500 uppercase tracking-wide">Imovel
        <select name="id_imovel" required class="w-full px-3 py-2 text-sm text-gray-900 bg-white border border-gray-300 rounded-md outline-none focus:border-gray-500 focus:ring-2 focus:ring-gray-200 transition">
            <option value="" disabled <?= empty($contrato?->getIdImovel()) ? 'selected' : '' ?>>Selecione o imovel</option>
            <?php foreach ($imoveis as $i): ?>
            <?php
            $isSelecionado = (($contrato?->getIdImovel() ?? 0) === $i->getId());
            $isDisponivel = $i->getStatus() === 'disponivel';
            $isBloqueado = !$isDisponivel && !$isSelecionado;
            ?>
            <option value="<?= $i->getId() ?>"
                <?= $isSelecionado ? 'selected' : '' ?>
                <?= $isBloqueado ? 'disabled' : '' ?>>
                <?= htmlspecialchars($i->getTitulo()) ?><?= $isDisponivel ? '' : ' (indisponivel)' ?>
            </option>
            <?php endforeach; ?>
        </select>
    </label>

    <label class="flex flex-col gap-1.5 text-xs font-medium text-gray-500 uppercase tracking-wide">Cliente
        <select name="id_cliente" required class="w-full px-3 py-2 text-sm text-gray-900 bg-white border border-gray-300 rounded-md outline-none focus:border-gray-500 focus:ring-2 focus:ring-gray-200 transition">
            <option value="" disabled <?= empty($contrato?->getIdCliente()) ? 'selected' : '' ?>>Selecione o cliente</option>
            <?php foreach ($clientes as $c): ?>
            <option value="<?= $c->getId() ?>"
                <?= (($contrato?->getIdCliente() ?? 0) === $c->getId()) ? 'selected' : '' ?>>
                <?= htmlspecialchars($c->getNome()) ?>
            </option>
            <?php endforeach; ?>
        </select>
    </label>

    <label class="flex flex-col gap-1.5 text-xs font-medium text-gray-500 uppercase tracking-wide">Corretor
        <select name="id_corretor" required class="w-full px-3 py-2 text-sm text-gray-900 bg-white border border-gray-300 rounded-md outline-none focus:border-gray-500 focus:ring-2 focus:ring-gray-200 transition">
            <option value="" disabled <?= empty($contrato?->getIdCorretor()) ? 'selected' : '' ?>>Selecione o corretor</option>
            <?php foreach ($corretores as $c): ?>
            <option value="<?= $c->getId() ?>"
                <?= (($contrato?->getIdCorretor() ?? 0) === $c->getId()) ? 'selected' : '' ?>>
                <?= htmlspecialchars($c->getNome()) ?>
            </option>
            <?php endforeach; ?>
        </select>
    </label>

    <label class="flex flex-col gap-1.5 text-xs font-medium text-gray-500 uppercase tracking-wide">Tipo
        <select name="tipo" required class="w-full px-3 py-2 text-sm text-gray-900 bg-white border border-gray-300 rounded-md outline-none focus:border-gray-500 focus:ring-2 focus:ring-gray-200 transition">
            <option value="" disabled <?= empty($contrato?->getTipo()) ? 'selected' : '' ?>>Selecione o tipo</option>
            <?php foreach (['venda', 'aluguel'] as $op): ?>
            <option value="<?= $op ?>"
                <?= (($contrato?->getTipo() ?? '') === $op) ? 'selected' : '' ?>>
                <?= ucfirst($op) ?>
            </option>
            <?php endforeach; ?>
        </select>
    </label>

    <label class="flex flex-col gap-1.5 text-xs font-medium text-gray-500 uppercase tracking-wide">Valor
        <input type="number" step="0.01" name="valor" required class="w-full px-3 py-2 text-sm text-gray-900 bg-white border border-gray-300 rounded-md outline-none focus:border-gray-500 focus:ring-2 focus:ring-gray-200 transition"
               placeholder="Ex.: 1200.00"
               value="<?= $contrato?->getValor() ?? '' ?>">
    </label>

    <label class="flex flex-col gap-1.5 text-xs font-medium text-gray-500 uppercase tracking-wide">Data inicio
        <input type="date" name="data_inicio" required class="w-full px-3 py-2 text-sm text-gray-900 bg-white border border-gray-300 rounded-md outline-none focus:border-gray-500 focus:ring-2 focus:ring-gray-200 transition"
               placeholder="AAAA-MM-DD"
               value="<?= $contrato?->getDataInicio() ?? '' ?>">
    </label>

    <label class="flex flex-col gap-1.5 text-xs font-medium text-gray-500 uppercase tracking-wide">Data fim
        <!-- Campo opcional: deixar em branco é válido para contratos de venda -->
        <input type="date" name="data_fim" class="w-full px-3 py-2 text-sm text-gray-900 bg-white border border-gray-300 rounded-md outline-none focus:border-gray-500 focus:ring-2 focus:ring-gray-200 transition"
               placeholder="AAAA-MM-DD"
               value="<?= $contrato?->getDataFim() ?? '' ?>">
    </label>

    <button type="submit" class="self-start bg-gray-900 text-white text-xs font-medium px-5 py-2 rounded-md hover:bg-gray-700 transition cursor-pointer">Salvar</button>
    <a href="index.php?entidade=contrato&acao=listar" class="self-start text-xs text-gray-400 hover:text-gray-700 hover:underline transition">Cancelar</a>
</form>
