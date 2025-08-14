<style>
    .password-wrapper {
        position: relative;
    }
    .password-criteria-box {
        display: none; /* Escondido por defeito */
        position: absolute;
        top: 0;
        left: 105%;
        width: 280px;
        padding: 15px;
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15);
        z-index: 10;
    }
    .password-criteria-box h6 {
        margin-top: 0;
        margin-bottom: 10px;
    }
    .password-criteria-box ul {
        margin-bottom: 0;
        padding-left: 1.2rem;
    }
</style>

<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white">
                <h3 class="text-center">Crie a sua Conta</h3>
            </div>
            <div class="card-body">
                <form action="<?php echo BASE_URL; ?>/user/register" method="POST" novalidate>
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome Completo</label>
                        <input type="text" class="form-control <?php echo isset($errors['nome']) ? 'is-invalid' : ''; ?>" id="nome" name="nome" value="<?php echo isset($_POST['nome']) ? htmlspecialchars($_POST['nome']) : ''; ?>" required>
                        <?php if (isset($errors['nome'])): ?><div class="invalid-feedback"><?php echo $errors['nome']; ?></div><?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                        <?php if (isset($errors['email'])): ?><div class="invalid-feedback"><?php echo $errors['email']; ?></div><?php endif; ?>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="password-wrapper">
                                <label for="senha" class="form-label">Senha</label>
                                <input type="password" class="form-control <?php echo isset($errors['senha']) ? 'is-invalid' : ''; ?>" id="senha" name="senha" required>
                                <?php if (isset($errors['senha'])): ?><div class="invalid-feedback"><?php echo $errors['senha']; ?></div><?php endif; ?>
                                
                                <div id="password-criteria" class="password-criteria-box">
                                    <h6>A senha deve conter:</h6>
                                    <ul class="list-unstyled small">
                                        <li id="length">&#10007; Mínimo de 8 caracteres</li>
                                        <li id="uppercase">&#10007; Pelo menos 1 letra maiúscula</li>
                                        <li id="lowercase">&#10007; Pelo menos 1 letra minúscula</li>
                                        <li id="number">&#10007; Pelo menos 1 número</li>
                                        <li id="symbol">&#10007; Pelo menos 1 símbolo</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="confirmar_senha" class="form-label">Confirmar Senha</label>
                            <input type="password" class="form-control <?php echo isset($errors['confirmar_senha']) ? 'is-invalid' : ''; ?>" id="confirmar_senha" name="confirmar_senha" required>
                            <?php if (isset($errors['confirmar_senha'])): ?><div class="invalid-feedback"><?php echo $errors['confirmar_senha']; ?></div><?php endif; ?>
                            <div id="confirmar_senha_feedback" class="form-text"></div>
                        </div>
                    </div>
                     <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="is_funcionario" name="is_funcionario" <?php echo isset($_POST['is_funcionario']) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="is_funcionario">Sou funcionário</label>
                    </div>
                    <div class="mb-3" id="codigo_secreto_div" style="<?php echo isset($_POST['is_funcionario']) ? 'display: block;' : 'display: none;'; ?>">
                        <label for="codigo_secreto" class="form-label">Código Secreto</label>
                        <input type="text" class="form-control <?php echo isset($errors['codigo_secreto']) ? 'is-invalid' : ''; ?>" id="codigo_secreto" name="codigo_secreto" placeholder="Digite o código">
                         <?php if (isset($errors['codigo_secreto'])): ?><div class="invalid-feedback"><?php echo $errors['codigo_secreto']; ?></div><?php endif; ?>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Registar</button>
                    </div>
                </form>
            </div>
             <div class="card-footer text-center">
                <small class="text-muted">Já tem uma conta? <a href="<?php echo BASE_URL; ?>/user/login">Faça o login aqui</a></small>
            </div>
        </div>
    </div>
</div>

<script>
    // --- LÓGICA DE VALIDAÇÃO DE SENHA ---
    const senhaInput = document.getElementById('senha');
    const confirmarSenhaInput = document.getElementById('confirmar_senha');
    const feedbackConfirmar = document.getElementById('confirmar_senha_feedback');
    const criteriaBox = document.getElementById('password-criteria');
    const criteria = {
        length: document.getElementById('length'),
        uppercase: document.getElementById('uppercase'),
        lowercase: document.getElementById('lowercase'),
        number: document.getElementById('number'),
        symbol: document.getElementById('symbol')
    };

    senhaInput.addEventListener('focus', function() { criteriaBox.style.display = 'block'; });
    senhaInput.addEventListener('blur', function() { criteriaBox.style.display = 'none'; });
    senhaInput.addEventListener('input', function() { validatePassword(); updateConfirmPassword(); });
    confirmarSenhaInput.addEventListener('input', updateConfirmPassword);
    
    // --- LÓGICA PARA O CAMPO DE FUNCIONÁRIO (RE-ADICIONADA) ---
    const funcionarioCheckbox = document.getElementById('is_funcionario');
    const divCodigo = document.getElementById('codigo_secreto_div');

    funcionarioCheckbox.addEventListener('change', function() {
        if (this.checked) {
            divCodigo.style.display = 'block';
        } else {
            divCodigo.style.display = 'none';
        }
    });

    // --- FUNÇÕES DE VALIDAÇÃO (sem alterações) ---
    function validatePassword() {
        const value = senhaInput.value;
        updateCriterion(criteria.length, value.length >= 8);
        updateCriterion(criteria.uppercase, /[A-Z]/.test(value));
        updateCriterion(criteria.lowercase, /[a-z]/.test(value));
        updateCriterion(criteria.number, /[0-9]/.test(value));
        updateCriterion(criteria.symbol, /[\W]/.test(value));
    }

    function updateConfirmPassword() {
        if (confirmarSenhaInput.value === '' && senhaInput.value === '') {
            feedbackConfirmar.textContent = '';
            return;
        }
        if (senhaInput.value !== '' && senhaInput.value === confirmarSenhaInput.value) {
            feedbackConfirmar.textContent = 'As senhas coincidem!';
            feedbackConfirmar.className = 'form-text text-success';
        } else {
            feedbackConfirmar.textContent = 'As senhas não coincidem.';
            feedbackConfirmar.className = 'form-text text-danger';
        }
    }

    function updateCriterion(element, isValid) {
        const icon = isValid ? '&#10003;' : '&#10007;';
        const text = element.innerText.substring(2);
        element.style.color = isValid ? 'green' : 'red';
        element.innerHTML = `${icon} ${text}`;
    }
</script>