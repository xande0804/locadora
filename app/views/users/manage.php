<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="display-6">Gerenciamento de Usuários</h1>
        <a href="<?php echo BASE_URL; ?>/user/register" class="btn btn-success">Cadastrar Novo Usuário</a>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="<?php echo BASE_URL; ?>/user/manage" method="GET" class="d-flex">
                <input class="form-control me-2" type="search" placeholder="Buscar por nome ou email..." name="search" value="<?php echo isset($searchTerm) ? htmlspecialchars($searchTerm) : ''; ?>">
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
                        <th>Nome</th>
                        <th>Email</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($usuarios)): ?>
                        <?php foreach ($usuarios as $usuario): ?>
                            <tr>
                                <td><?php echo $usuario['id']; ?></td>
                                <td><?php echo htmlspecialchars($usuario['nome']); ?></td>
                                <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                                <td class="text-center">
                                    <?php
                                        // Lógica para determinar o status a ser exibido
                                        if ($usuario['tipo'] === 'funcionario') {
                                            $status = 'Funcionario';
                                            $badge_class = 'bg-success';
                                        } elseif ($usuario['cliente_id'] !== null) {
                                            $status = 'Cliente';
                                            $badge_class = 'bg-primary';
                                        } else {
                                            $status = 'Comum';
                                            $badge_class = 'bg-info';
                                        }
                                    ?>
                                    <span class="badge <?php echo $badge_class; ?>"><?php echo $status; ?></span>
                                </td>
                                <td class="text-center">
                                    <a href="<?php echo BASE_URL; ?>/user/edit/<?php echo $usuario['id']; ?>" class="btn btn-sm btn-warning">Editar</a>
                                    <?php if ($_SESSION['user_id'] !== $usuario['id']): ?>
                                        <button type="button" class="btn btn-sm btn-danger delete-btn" 
                                                data-bs-toggle="modal" data-bs-target="#deleteUserModal"
                                                data-user-id="<?php echo $usuario['id']; ?>">
                                            Excluir
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted">Nenhum usuário encontrado.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <?php if (isset($total_pages) && $total_pages > 1): ?>
    <nav aria-label="Navegação de página de usuários" class="mt-4">
        <ul class="pagination justify-content-center">
            <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                <a class="page-link" href="<?php echo BASE_URL; ?>/user/manage/<?php echo $page - 1; ?>?search=<?php echo isset($searchTerm) ? urlencode($searchTerm) : ''; ?>">Anterior</a>
            </li>
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                    <a class="page-link" href="<?php echo BASE_URL; ?>/user/manage/<?php echo $i; ?>?search=<?php echo isset($searchTerm) ? urlencode($searchTerm) : ''; ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>
            <li class="page-item <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>">
                <a class="page-link" href="<?php echo BASE_URL; ?>/user/manage/<?php echo $page + 1; ?>?search=<?php echo isset($searchTerm) ? urlencode($searchTerm) : ''; ?>">Próximo</a>
            </li>
        </ul>
    </nav>
    <?php endif; ?>
</div>