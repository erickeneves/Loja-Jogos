
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            let valid = true;
            
            const inputs = this.querySelectorAll('input[required], select[required], textarea[required]');
            inputs.forEach(input => {
                if (!input.value.trim()) {
                    valid = false;
                    input.classList.add('error');
                } else {
                    input.classList.remove('error');
                }
            });
            
            if (!valid) {
                e.preventDefault();
                alert('Por favor, preencha todos os campos obrigatórios!');
            }
        });
    });
    
    function atualizarTotal() {
        let total = 0;
        document.querySelectorAll('.item-total').forEach(el => {
            total += parseFloat(el.textContent.replace('R$ ', ''));
        });
        document.getElementById('total-geral').textContent = 'R$ ' + total.toFixed(2);
    }
    
    if (document.querySelector('.carrinho')) {
        atualizarTotal();
        
        document.querySelectorAll('.input-dias').forEach(input => {
            input.addEventListener('change', function() {
                const index = this.dataset.index;
                const valorDiaria = parseFloat(this.dataset.diaria);
                const dias = parseInt(this.value);
                const subtotal = dias * valorDiaria;
                
                document.querySelector(`.item-total[data-index="${index}"]`).textContent = 
                    'R$ ' + subtotal.toFixed(2);
                
                atualizarTotal();
            });
        });
    }
});