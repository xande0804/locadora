<div class="container">
    <h1 class="display-6 mb-4">Editar Usuário</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="<?php echo BASE_URL; ?>/user/update" method="POST">
                <input type="hidden" name="id" value="<?php echo $usuario['id']; ?>">

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nome" class="form-label">Nome Completo</label>
                        <input type="text" class="form-control" id="nome" name="nome" value="<?php echo htmlspecialchars($usuario['nome']); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="tipo" class="form-label">Tipo de Usuário</label>
                        <!-- CORREÇÃO APLICADA AQUI -->
                        <select class="form-select" id="tipo" name="tipo" required <?php echo ($usuario['id'] === $_SESSION['user_id']) ? 'disabled' : ''; ?>>
                            <option value="comun" <?php echo ($usuario['tipo'] === 'comun') ? 'selected' : ''; ?>>Comum</option>
                            <option value="funcionario" <?php echo ($usuario['tipo'] === 'funcionario') ? 'selected' : ''; ?>>Funcionário</option>
                        </select>
                        <?php if ($usuario['id'] === $_SESSION['user_id']): ?>
                            <div class="form-text">Você não pode alterar seu próprio tipo de usuário.</div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                    <a href="<?php echo BASE_URL; ?>/user/manage" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>