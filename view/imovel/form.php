<?php

$editando = $imovel && $imovel->getId();
?>
<h2 class="text-xl font-semibold text-gray-900 mb-6"><?= $editando ? 'Editar Imovel' : 'Novo Imovel' ?></h2>

<form method="post" action="index.php?entidade=imovel&acao=salvar" enctype="multipart/form-data" class="bg-white border border-gray-200 rounded-lg p-7 max-w-lg flex flex-col gap-5">

    <?php if ($editando): ?>
        <!-- ID enviado no POST pra o controller saber que é uma edição -->
        <input type="hidden" name="id" value="<?= $imovel->getId() ?>">
        <!-- Guarda o nome do arquivo atual pra não perder a planta se não enviar outro -->
        <input type="hidden" name="planta_baixa_atual" value="<?= htmlspecialchars($imovel->getPlantaBaixa() ?? '') ?>">
    <?php endif; ?>

    <label class="flex flex-col gap-1.5 text-xs font-medium text-gray-500 uppercase tracking-wide">Titulo
        <input type="text" name="titulo" required class="w-full px-3 py-2 text-sm text-gray-900 bg-white border border-gray-300 rounded-md outline-none focus:border-gray-500 focus:ring-2 focus:ring-gray-200 transition"
               placeholder="Digite o titulo do imovel"
               value="<?= htmlspecialchars($imovel?->getTitulo() ?? '') ?>">
    </label>

    <label class="flex flex-col gap-1.5 text-xs font-medium text-gray-500 uppercase tracking-wide">Tipo
        <select name="tipo" required class="w-full px-3 py-2 text-sm text-gray-900 bg-white border border-gray-300 rounded-md outline-none focus:border-gray-500 focus:ring-2 focus:ring-gray-200 transition">
            <option value="" disabled <?= empty($imovel?->getTipo()) ? 'selected' : '' ?>>Selecione o tipo</option>
            <?php foreach (['casa', 'apartamento', 'terreno', 'comercial'] as $op): ?>
            <option value="<?= $op ?>"
                <?= (($imovel?->getTipo() ?? '') === $op) ? 'selected' : '' ?>>
                <?= ucfirst($op) ?>
            </option>
            <?php endforeach; ?>
        </select>
    </label>

    <label class="flex flex-col gap-1.5 text-xs font-medium text-gray-500 uppercase tracking-wide">Endereco
        <input type="text" name="endereco" required class="w-full px-3 py-2 text-sm text-gray-900 bg-white border border-gray-300 rounded-md outline-none focus:border-gray-500 focus:ring-2 focus:ring-gray-200 transition"
               placeholder="Rua, numero, bairro"
               value="<?= htmlspecialchars($imovel?->getEndereco() ?? '') ?>">
    </label>

    <label class="flex flex-col gap-1.5 text-xs font-medium text-gray-500 uppercase tracking-wide">Metros Quadrados
        <!-- Campo opcional — nem todo imóvel tem metragem cadastrada -->
        <input type="number" step="0.01" name="metros_quadrados" class="w-full px-3 py-2 text-sm text-gray-900 bg-white border border-gray-300 rounded-md outline-none focus:border-gray-500 focus:ring-2 focus:ring-gray-200 transition"
               placeholder="Ex.: 85.50"
               value="<?= $imovel?->getMetrosQuadrados() ?? '' ?>">
    </label>

    <label class="flex flex-col gap-1.5 text-xs font-medium text-gray-500 uppercase tracking-wide">Valor (R$)
        <!-- step="0.01" permite valores com centavos -->
        <input type="number" step="0.01" name="valor" required class="w-full px-3 py-2 text-sm text-gray-900 bg-white border border-gray-300 rounded-md outline-none focus:border-gray-500 focus:ring-2 focus:ring-gray-200 transition"
               placeholder="Ex.: 350000.00"
               value="<?= $imovel?->getValor() ?? '' ?>">
    </label>

    <label class="flex flex-col gap-1.5 text-xs font-medium text-gray-500 uppercase tracking-wide">Finalidade
        <select name="finalidade" required class="w-full px-3 py-2 text-sm text-gray-900 bg-white border border-gray-300 rounded-md outline-none focus:border-gray-500 focus:ring-2 focus:ring-gray-200 transition">
            <option value="" disabled <?= empty($imovel?->getFinalidade()) ? 'selected' : '' ?>>Selecione a finalidade</option>
            <?php foreach (['venda', 'aluguel'] as $op): ?>
            <option value="<?= $op ?>"
                <?= (($imovel?->getFinalidade() ?? '') === $op) ? 'selected' : '' ?>>
                <?= ucfirst($op) ?>
            </option>
            <?php endforeach; ?>
        </select>
    </label>

    <label class="flex flex-col gap-1.5 text-xs font-medium text-gray-500 uppercase tracking-wide">Status
        <select name="status" required class="w-full px-3 py-2 text-sm text-gray-900 bg-white border border-gray-300 rounded-md outline-none focus:border-gray-500 focus:ring-2 focus:ring-gray-200 transition">
            <option value="" disabled <?= empty($imovel?->getStatus()) ? 'selected' : '' ?>>Selecione o status</option>
            <?php foreach (['disponivel', 'alugado', 'vendido'] as $op): ?>
            <option value="<?= $op ?>"
                <?= (($imovel?->getStatus() ?? '') === $op) ? 'selected' : '' ?>>
                <?= ucfirst($op) ?>
            </option>
            <?php endforeach; ?>
        </select>
    </label>

    <label class="flex flex-col gap-1.5 text-xs font-medium text-gray-500 uppercase tracking-wide">Planta Baixa
        <?php if ($editando && $imovel->getPlantaBaixa()): ?>
            <?php
            $plantaAtual = basename((string) $imovel->getPlantaBaixa());
            $plantaAtualCaminho = __DIR__ . '/../../uploads/' . $plantaAtual;
            ?>
            <?php if (is_file($plantaAtualCaminho)): ?>
                <p class="text-xs text-gray-400 normal-case tracking-normal">Planta atual: <a href="uploads/<?= rawurlencode($plantaAtual) ?>" target="_blank" class="text-teal-600 underline">ver planta</a></p>
            <?php else: ?>
                <p class="text-xs text-red-500 normal-case tracking-normal">Planta atual nao encontrada em uploads.</p>
            <?php endif; ?>
            <p class="text-xs text-gray-400 normal-case tracking-normal">Enviar nova planta (opcional — substitui a atual):</p>
        <?php endif; ?>
        <input type="file" name="planta_baixa" id="planta_baixa" accept="image/*,.pdf" class="w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-xs file:font-medium file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200">
        <div id="preview_planta" style="display:none; margin-top:6px; border: 2px inset #ccc; padding:4px; background:#f9f9f9;">
            <p style="margin:0 0 4px; font-size:11px; color:#444;">Preview:</p>
            <img id="preview_img" src="" alt="Preview da planta" style="max-width:100%; max-height:200px; display:none;">
            <p id="preview_pdf" style="display:none; font-size:11px; color:#000080; margin:0;">Arquivo PDF selecionado. Preview nao disponivel para PDF.</p>
        </div>
    </label>

<script>
document.getElementById('planta_baixa').addEventListener('change', function () {
    var file = this.files[0];
    var preview = document.getElementById('preview_planta');
    var img = document.getElementById('preview_img');
    var pdf = document.getElementById('preview_pdf');

    if (!file) {
        preview.style.display = 'none';
        return;
    }

    preview.style.display = 'block';

    if (file.type === 'application/pdf') {
        img.style.display = 'none';
        pdf.style.display = 'block';
    } else {
        pdf.style.display = 'none';
        img.style.display = 'block';
        var reader = new FileReader();
        reader.onload = function (e) { img.src = e.target.result; };
        reader.readAsDataURL(file);
    }
});
</script>

    <label class="flex flex-col gap-1.5 text-xs font-medium text-gray-500 uppercase tracking-wide">Proprietario
        <select name="id_proprietario" required class="w-full px-3 py-2 text-sm text-gray-900 bg-white border border-gray-300 rounded-md outline-none focus:border-gray-500 focus:ring-2 focus:ring-gray-200 transition">
            <option value="" disabled <?= empty($imovel?->getIdProprietario()) ? 'selected' : '' ?>>Selecione o proprietario</option>
            <?php foreach ($proprietarios as $p): ?>
            <option value="<?= $p->getId() ?>"
                <?= (($imovel?->getIdProprietario() ?? 0) === $p->getId()) ? 'selected' : '' ?>>
                <?= htmlspecialchars($p->getNome()) ?>
            </option>
            <?php endforeach; ?>
        </select>
    </label>

    <button type="submit" class="self-start bg-gray-900 text-white text-xs font-medium px-5 py-2 rounded-md hover:bg-gray-700 transition cursor-pointer">Salvar</button>
    <a href="index.php?entidade=imovel&acao=listar" class="self-start text-xs text-gray-400 hover:text-gray-700 hover:underline transition">Cancelar</a>
</form>
