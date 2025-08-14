<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">
                    <h3 class="text-center">Complete seu Perfil para Alugar</h3>
                </div>
                <div class="card-body">
                    <p class="text-center text-muted">Para realizar seu primeiro aluguel, precisamos de mais algumas informações.</p>
                    
                    <?php if (isset($error_message)): ?>
                        <div class="alert alert-danger"><?php echo $error_message; ?></div>
                    <?php endif; ?>

                    <form action="<?php echo BASE_URL; ?>/client/store" method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="cpf" class="form-label">CPF</label>
                                <input type="text" class="form-control" id="cpf" name="cpf" placeholder="000.000.000-00" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="cnh" class="form-label">Nº da CNH</label>
                                <input type="text" class="form-control" id="cnh" name="cnh" required>
                            </div>
                        </div>
                        <div class="row">
                             <div class="col-md-6 mb-3">
                                <label for="data_nascimento" class="form-label">Data de Nascimento</label>
                                <input type="date" class="form-control" id="data_nascimento" name="data_nascimento" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="telefone" class="form-label">Telefone / Celular</label>
                                <input type="text" class="form-control" id="telefone" name="telefone" placeholder="(00) 90000-0000" required>
                            </div>
                        </div>
                         <div class="mb-3">
                            <label for="sexo" class="form-label">Sexo</label>
                            <select class="form-select" id="sexo" name="sexo" required>
                                <option value="Masculino">Masculino</option>
                                <option value="Feminino">Feminino</option>
                                <option value="Outro">Outro</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="endereco" class="form-label">Endereço Completo</label>
                            <textarea class="form-control" id="endereco" name="endereco" rows="3" placeholder="Ex: Rua das Flores, 123, Bairro Centro, Cidade - UF, CEP 00000-000" required></textarea>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Salvar Perfil e Continuar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>