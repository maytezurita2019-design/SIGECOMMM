document.addEventListener('DOMContentLoaded', function () {
    const litros = document.getElementById('cantidad_litros');
    const precio = document.getElementById('precio_unitario');
    const total = document.getElementById('costo_total') || document.getElementById('total_visual');
    if (!litros || !precio || !total) return;

    function decimal(campo, precision, positivo) {
        const valor = campo.value.trim().replace(',', '.');
        campo.setCustomValidity('');
        if (valor === '') return null;
        const patron = new RegExp('^\\d{1,8}(?:\\.\\d{1,' + precision + '})?$');
        if (!patron.test(valor)) {
            campo.setCustomValidity('Ingrese un número sin separador de miles, con hasta ' + precision + ' decimales. Puede usar coma o punto.');
            return null;
        }
        const partes = valor.split('.');
        const digitos = BigInt(partes.join(''));
        if (positivo && digitos === 0n) {
            campo.setCustomValidity('La cantidad debe ser mayor que cero.');
            return null;
        }
        return { digitos, escala: (partes[1] || '').length };
    }

    function calcular() {
        const cantidad = decimal(litros, 13, true);
        const unitario = decimal(precio, 4, false);
        total.value = '';
        if (!cantidad || !unitario) return;
        const producto = cantidad.digitos * unitario.digitos;
        const escala = cantidad.escala + unitario.escala;
        const divisor = 10n ** BigInt(Math.max(0, escala - 2));
        const centavos = escala <= 2 ? producto * 10n ** BigInt(2 - escala)
            : (producto + divisor / 2n) / divisor;
        if (centavos > 99999999999999n) {
            litros.setCustomValidity('El costo total supera el máximo permitido.');
            return;
        }
        const texto = centavos.toString().padStart(3, '0');
        total.value = texto.slice(0, -2).replace(/\B(?=(\d{3})+(?!\d))/g, '.') + ',' + texto.slice(-2);
    }
    [litros, precio].forEach(campo => {
        campo.addEventListener('input', calcular);
        campo.addEventListener('change', calcular);
    });
    calcular();
});
