function maskCpf(input) {
    input.addEventListener('input', function () {
        var v = this.value.replace(/\D/g, '').slice(0, 11);
        if (v.length > 9)      v = v.replace(/(\d{3})(\d{3})(\d{3})(\d{1,2})/, '$1.$2.$3-$4');
        else if (v.length > 6) v = v.replace(/(\d{3})(\d{3})(\d{1,3})/,        '$1.$2.$3');
        else if (v.length > 3) v = v.replace(/(\d{3})(\d{1,3})/,                '$1.$2');
        this.value = v;
    });
    input.addEventListener('keypress', function (e) {
        if (!/\d/.test(e.key) && !['Backspace','Delete','Tab','ArrowLeft','ArrowRight'].includes(e.key)) {
            e.preventDefault();
        }
    });
}

function maskTelefone(input) {
    input.addEventListener('input', function () {
        var v = this.value.replace(/\D/g, '').slice(0, 11);
        if (v.length > 10)     v = v.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
        else if (v.length > 6) v = v.replace(/(\d{2})(\d{4,5})(\d{0,4})/, '($1) $2-$3');
        else if (v.length > 2) v = v.replace(/(\d{2})(\d{0,5})/, '($1) $2');
        else if (v.length > 0) v = v.replace(/(\d{0,2})/, '($1');
        this.value = v;
    });
    input.addEventListener('keypress', function (e) {
        if (!/\d/.test(e.key) && !['Backspace','Delete','Tab','ArrowLeft','ArrowRight'].includes(e.key)) {
            e.preventDefault();
        }
    });
}

function maskApenasNumeros(input) {
    input.addEventListener('keypress', function (e) {
        if (!/\d/.test(e.key) && !['Backspace','Delete','Tab','ArrowLeft','ArrowRight'].includes(e.key)) {
            e.preventDefault();
        }
    });
    input.addEventListener('input', function () {
        this.value = this.value.replace(/\D/g, '');
    });
}

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('input[name="cpf"]').forEach(maskCpf);
    document.querySelectorAll('input[name="telefone"], input[name="celular"]').forEach(maskTelefone);
    document.querySelectorAll('input[name="creci"]').forEach(maskApenasNumeros);
});
