    </div>
    
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <img src="img/bannerpng.png" alt="GameRent" class="mb-3" width="150">
                    <p>Sua locadora de jogos online com os melhores títulos e preços acessíveis.</p>
                </div>
                
                <div class="col-md-4 mb-3">
                    <h5>Links Rápidos</h5>
                    <ul class="list-unstyled">
                        <li><a href="index.php" class="text-white">Home</a></li>
                        <li><a href="carrinho.php" class="text-white">Carrinho</a></li>
                        <li><a href="login.php" class="text-white">Login</a></li>
                        <li><a href="admin/login_admin.php" class="text-white">Área Admin</a></li>
                    </ul>
                </div>
                
                <div class="col-md-4">
                    <h5>Contato</h5>
                    <p><i class="bi bi-envelope me-2"></i> erickedu211@gmail.com</p>
                    <p><i class="bi bi-whatsapp me-2"></i> (14) 98836-4827</p>
                </div>
            </div>
            
            <hr class="my-4 bg-light">
            
            <div class="text-center">
                <p>&copy; <?= date('Y') ?> GameRent - Todos os direitos reservados</p>
            </div>
        </div>
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php ob_end_flush();?>