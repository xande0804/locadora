<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white">
                <h3 class="text-center">Login</h3>
            </div>
            <div class="card-body">
                <?php if (isset($error_message)): ?>
                    <div class="alert alert-danger" role="alert"><?php echo $error_message; ?></div>
                <?php endif; ?>

                <form action="<?php echo BASE_URL; ?>/user/login" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="senha" class="form-label">Senha</label>
                        <input type="password" class="form-control" id="senha" name="senha" required>
                    </div>

                    <div class="text-end mb-3">
                        <a href="#" class="small">Esqueci a minha senha</a>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Entrar</button>
                    </div>
                </form>
            </div>
            <div class="card-footer text-center">
                <small class="text-muted">Não tem uma conta? <a href="<?php echo BASE_URL; ?>/user/register">Registe-se aqui</a></small>
            </div>
        </div>
    </div>
</div>