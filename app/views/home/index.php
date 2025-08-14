<div class="hero-section">
    <div class="hero-content">
        <h1 class="display-4">A Liberdade de Explorar Começa Aqui</h1>
        <p class="lead">Encontre o carro perfeito para a sua próxima aventura. Simples, rápido e seguro.</p>
        <a href="#frota" class="btn btn-primary btn-lg btn-gradient">Ver a Nossa Frota</a>
    </div>
</div>

<div class="container mt-5" id="frota">
    <div class="text-center mb-5">
        <h2>A Nossa Frota Disponível</h2>
        <p class="lead text-muted">Veículos selecionados para garantir o seu conforto e segurança.</p>
    </div>

    <div class="row">
        <?php if (!empty($carros)): ?>
            <?php foreach ($carros as $carro): ?>
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm h-100 border-0">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?php echo htmlspecialchars($carro['modelo']); ?></h5>
                            <p class="card-text">
                                <strong>Ano:</strong> <?php echo htmlspecialchars($carro['ano']); ?><br>
                                <span class="text-muted">A partir de</span>
                                <strong class="fs-5">R$ <?php echo number_format($carro['preco_diaria'], 2, ',', '.'); ?></strong>
                                <span class="text-muted">/dia</span>
                            </p>
                            <div class="text-center mt-auto">
                                <a href="<?php echo BASE_URL; ?>/user/login" class="btn btn-outline-primary w-100">Reservar Agora</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-warning" role="alert">
                    Nenhum carro disponível para aluguer no momento. Volte mais tarde!
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>