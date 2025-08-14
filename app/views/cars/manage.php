<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="display-6">Gerenciamento de Frota</h1>
        <a href="<?php echo BASE_URL; ?>/car/create" class="btn btn-success">Adicionar Novo Carro</a>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="<?php echo BASE_URL; ?>/car/index" method="GET" class="d-flex">
                <input class="form-control me-2" type="search" placeholder="Buscar por marca ou modelo..." name="search" value="<?php echo isset($searchTerm) ? htmlspecialchars($searchTerm) : ''; ?>">
                <button class="btn btn-outline-primary" type="submit">Buscar</button>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Veículo (Marca e Modelo)</th>
                        <th>Ano</th>
                        <th>Preço/Dia</th>
                        <th class="text-center">Qtd. Disponível</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($carros)): ?>
                        <?php foreach ($carros as $carro): ?>
                            <tr>
                                <td><?php echo $carro['id']; ?></td>
                                <td><?php echo htmlspecialchars($carro['marca_nome'] . ' ' . $carro['modelo_nome']); ?></td>
                                <td><?php echo htmlspecialchars($carro['ano']); ?></td>
                                <td>R$ <?php echo number_format($carro['preco_diaria'], 2, ',', '.'); ?></td>
                                <td class="text-center">
                                    <span class="badge bg-<?php echo $carro['quantidade_disponivel'] > 0 ? 'primary' : 'danger'; ?>">
                                        <?php echo $carro['quantidade_disponivel']; ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="<?php echo BASE_URL; ?>/car/edit/<?php echo $carro['id']; ?>" class="btn btn-sm btn-warning">Editar</a>
                                    <a href="<?php echo BASE_URL; ?>/car/delete/<?php echo $carro['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir este carro? A ação não pode ser desfeita.');">Excluir</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted">Nenhum carro encontrado.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if (isset($total_pages) && $total_pages > 1): ?>
    <nav aria-label="Navegação de página" class="mt-4">
        </nav>
    <?php endif; ?>
</div>