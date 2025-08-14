<div class="container">
    <h1 class="display-6 mb-4">Painel Administrativo</h1>
    <p class="lead">
        Bem-vindo, <?php echo htmlspecialchars($_SESSION['user_name']); ?>. Utilize os painéis abaixo para gerenciar o sistema.
    </p>
    <hr>

    <div class="row mt-4">
        <div class="col-md-6 mb-4">
            <div class="card text-center h-100">
                <div class="card-header">
                    <h4>Gerenciar Carros</h4>
                </div>
                <div class="card-body">
                    <p class="card-text">Adicione, edite, liste e remova os veículos da frota.</p>
                    <a href="<?php echo BASE_URL; ?>/car/index" class="btn btn-dark">Acessar Painel de Carros</a>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card text-center h-100">
                <div class="card-header">
                    <h4>Gerenciar Usuários</h4>
                </div>
                <div class="card-body">
                    <p class="card-text">Visualize, edite e remova clientes e outros funcionários.</p>
                    <a href="<?php echo BASE_URL; ?>/user/manage" class="btn btn-dark">Acessar Painel de Usuários</a>
                </div>
            </div>
        </div>
    </div>
</div>