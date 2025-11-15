// CNPJ/CPF Masking and Validation
$(document).ready(function() {
    $('#inputCnpj').on('input', function() {
        let value = $(this).val().replace(/\D/g, '');
        
        if (value.length <= 11) {
            // CPF format: XXX.XXX.XXX-XX
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d{2})$/, '$1-$2');
        } else if (value.length <= 14) {
            // CNPJ format: XX.XXX.XXX/XXXX-XX
            value = value.replace(/(\d{2})(\d)/, '$1.$2');
            value = value.replace(/(\d{5})(\d)/, '$1.$2');
            value = value.replace(/(\d{9})(\d)/, '$1/$2');
            value = value.replace(/(\d{13})(\d)/, '$1-$2');
        }
        
        $(this).val(value);
    });

    // Validate CNPJ/CPF on blur
    $('#inputCnpj').on('blur', function() {
        let value = $(this).val().replace(/\D/g, '');
        
        if (value.length === 11) {
            if (!isValidCPF(value)) {
                $(this).addClass('is-invalid');
                alert('CPF inválido!');
            } else {
                $(this).removeClass('is-invalid');
            }
        } else if (value.length === 14) {
            if (!isValidCNPJ(value)) {
                $(this).addClass('is-invalid');
                alert('CNPJ inválido!');
            } else {
                $(this).removeClass('is-invalid');
            }
        } else if (value.length > 0) {
            $(this).addClass('is-invalid');
            alert('CPF ou CNPJ inválido! Deve ter 11 ou 14 dígitos.');
        }
    });
});

function isValidCPF(cpf) {
    if (cpf.length !== 11 || /^(\d)\1{10}$/.test(cpf)) {
        return false;
    }

    let sum = 0;
    let remainder;

    for (let i = 1; i <= 9; i++) {
        sum += parseInt(cpf.substring(i - 1, i)) * (11 - i);
    }

    remainder = (sum * 10) % 11;
    if (remainder === 10 || remainder === 11) remainder = 0;
    if (remainder !== parseInt(cpf.substring(9, 10))) return false;

    sum = 0;
    for (let i = 1; i <= 10; i++) {
        sum += parseInt(cpf.substring(i - 1, i)) * (12 - i);
    }

    remainder = (sum * 10) % 11;
    if (remainder === 10 || remainder === 11) remainder = 0;
    if (remainder !== parseInt(cpf.substring(10, 11))) return false;

    return true;
}

function isValidCNPJ(cnpj) {
    if (cnpj.length !== 14 || /^(\d)\1{13}$/.test(cnpj)) {
        return false;
    }

    let size = cnpj.length - 2;
    let numbers = cnpj.substring(0, size);
    let digits = cnpj.substring(size);
    let sum = 0;
    let pos = 0;

    for (let i = size - 1; i >= 0; i--) {
        sum += numbers.charAt(i) * Math.pow(2, pos % 8);
        pos++;
    }

    let result = sum % 11 < 2 ? 0 : 11 - sum % 11;
    if (result !== parseInt(digits.charAt(0))) {
        return false;
    }

    size = size - 1;
    numbers = cnpj.substring(0, size);
    sum = 0;
    pos = 0;

    for (let i = size - 1; i >= 0; i--) {
        sum += numbers.charAt(i) * Math.pow(2, pos % 8);
        pos++;
    }

    result = sum % 11 < 2 ? 0 : 11 - sum % 11;
    if (result !== parseInt(digits.charAt(1))) {
        return false;
    }

    return true;
}
