<div class="container">
    <h1 class="display-6 mb-4">Minhas Reservas</h1>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?php echo $_SESSION['message']['type']; ?> alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['message']['text']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Carro</th>
                        <th>Período</th>
                        <th>Preço Total</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($reservas)): ?>
                        <?php foreach ($reservas as $reserva): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($reserva['modelo']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($reserva['data_inicio'])); ?> a <?php echo date('d/m/Y', strtotime($reserva['data_fim'])); ?></td>
                                <td>R$ <?php echo number_format($reserva['preco_total'], 2, ',', '.'); ?></td>
                                <td class="text-center">
                                    <?php
                                        $status_text = ucfirst($reserva['status']);
                                        $badge_class = 'bg-secondary';
                                        if ($reserva['status'] === 'confirmada') $badge_class = 'bg-success';
                                        if ($reserva['status'] === 'cancelada') $badge_class = 'bg-danger';
                                        if ($reserva['status'] === 'concluida') $badge_class = 'bg-info';
                                    ?>
                                    <span class="badge <?php echo $badge_class; ?>"><?php echo $status_text; ?></span>
                                </td>
                                <td class="text-center">
                                    <?php if ($reserva['status'] === 'confirmada'): ?>
                                        <a href="<?php echo BASE_URL; ?>/reservation/cancel/<?php echo $reserva['id']; ?>" class="btn btn-sm btn-danger">Cancelar</a>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted">Você ainda não fez nenhuma reserva.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-4">
        <a href="<?php echo BASE_URL; ?>/dashboard" class="btn btn-secondary">Voltar ao Painel</a>
    </div>
</div>