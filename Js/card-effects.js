document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.game-card');
    
    cards.forEach(card => {
        card.addEventListener('mousemove', function(e) {
            // Obter posição do card na tela
            const rect = this.getBoundingClientRect();
            
            // Calcular posição do mouse relativa ao centro do card
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            
            // Calcular ângulos de rotação baseados na posição do mouse
            const rotateY = (x - centerX) / 25;
            const rotateX = (centerY - y) / 25;
            
            // Aplicar transformações
            this.style.transform = `translateY(-20px) rotateX(${rotateX}deg) rotateY(${rotateY}deg)`;
            
            // Mover imagem flutuante
            const floatingImg = this.querySelector('.floating-image');
            const moveX = (x - centerX) / 10;
            const moveY = (y - centerY) / 10;
            
            floatingImg.style.transform = `translate(calc(-50% + ${moveX}px), calc(-50% + ${moveY}px)) translateZ(100px) scale(1.2)`;
            
            // Mover luz
            const light = this.querySelector('.card-light');
            light.style.backgroundPosition = `${x}px ${y}px`;
        });
        
        card.addEventListener('mouseenter', function() {
            this.style.transition = 'transform 0.2s ease';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transition = 'transform 0.8s ease';
            this.style.transform = 'translateY(0) rotateX(0) rotateY(0)';
            
            const floatingImg = this.querySelector('.floating-image');
            floatingImg.style.transform = 'translate(-50%, -50%) translateZ(100px) scale(1.2)';
        });
        
        // Tornar todo o card clicável
        card.addEventListener('click', function() {
            const link = this.querySelector('a');
            if (link) {
                window.location.href = link.href;
            }
        });
    });
});