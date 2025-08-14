<?php
class UserController {
    public function register() {
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                die('Erro de validação CSRF!');
            }
            unset($_SESSION['csrf_token']);
            $userModel = new User();
            $nome = $_POST['nome'];
            $email = $_POST['email'];
            $senha = $_POST['senha'];
            $confirmar_senha = $_POST['confirmar_senha'];
            $tipo = 'comun';
            if (strlen($senha) < 8 || !preg_match('/[A-Z]/', $senha) || !preg_match('/[a-z]/', $senha) || !preg_match('/[0-9]/', $senha) || !preg_match('/[\W]/', $senha)) {
                $errors['senha'] = 'A senha não cumpre os requisitos de segurança.';
            }
            if ($senha !== $confirmar_senha) {
                $errors['confirmar_senha'] = 'As senhas não coincidem.';
            }
            if (isset($_POST['is_funcionario'])) {
                if ($_POST['codigo_secreto'] === '1234') {
                    $tipo = 'funcionario';
                } else {
                    $errors['codigo_secreto'] = 'Código secreto de funcionário incorreto!';
                }
            }
            if (empty($errors)) {
                $newUserId = $userModel->create($nome, $email, $senha, $tipo);
                if ($newUserId) {
                    if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'funcionario') {
                        $_SESSION['message'] = ['type' => 'success', 'text' => 'Novo utilizador registado com sucesso!'];
                        header('Location: ' . BASE_URL . '/user/manage');
                    } else {
                        session_regenerate_id(true);
                        $_SESSION['user_id'] = $newUserId;
                        $_SESSION['user_name'] = $nome;
                        $_SESSION['user_type'] = $tipo;
                        $_SESSION['last_activity'] = time();
                        header('Location: ' . BASE_URL);
                    }
                    exit();
                } else {
                    $errors['email'] = 'O e-mail informado já está em uso.';
                }
            }
        }
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        require_once '../app/views/templates/header.php';
        require_once '../app/views/users/register.php';
        require_once '../app/views/templates/footer.php';
    }
    public function login() {
        $error_message = null;
        $lockout_time = 300;
        if (isset($_SESSION['login_attempts']) && $_SESSION['login_attempts'] >= 5 && (time() - ($_SESSION['first_attempt_time'] ?? 0) < $lockout_time)) {
             $error_message = 'Muitas tentativas falhadas. Por favor, aguarde 5 minutos.';
        } elseif (isset($_SESSION['first_attempt_time']) && (time() - $_SESSION['first_attempt_time'] > $lockout_time)) {
            unset($_SESSION['login_attempts'], $_SESSION['first_attempt_time']);
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && $error_message === null) {
            if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                die('Erro de validação CSRF!');
            }
            unset($_SESSION['csrf_token']);
            $userModel = new User();
            $user = $userModel->findByEmail($_POST['email']);
            if ($user && password_verify($_POST['senha'], $user['senha'])) {
                unset($_SESSION['login_attempts'], $_SESSION['first_attempt_time']);
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['nome'];
                $_SESSION['user_type'] = $user['tipo'];
                $_SESSION['last_activity'] = time();
                header('Location: ' . BASE_URL . '/dashboard');
                exit();
            } else {
                if (!isset($_SESSION['login_attempts'])) {
                    $_SESSION['login_attempts'] = 1;
                    $_SESSION['first_attempt_time'] = time();
                } else {
                    $_SESSION['login_attempts']++;
                }
                $error_message = "Email ou senha inválidos. Por favor, tente novamente.";
            }
        }
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        require_once '../app/views/templates/header.php';
        require_once '../app/views/users/login.php';
        require_once '../app/views/templates/footer.php';
    }
    public function logout() {
        session_start();
        session_unset();
        session_destroy();
        header('Location: ' . BASE_URL);
        exit();
    }
    public function manage($page = 1) {
        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'funcionario') {
            header('Location: ' . BASE_URL . '/dashboard');
            exit();
        }
        $userModel = new User();
        $searchTerm = isset($_GET['search']) ? trim($_GET['search']) : null;
        $limit = 5;
        $total_usuarios = $userModel->countAll($searchTerm);
        $total_pages = ceil($total_usuarios / $limit);
        $offset = ($page - 1) * $limit;
        $usuarios = $userModel->findPaginated($limit, $offset, $searchTerm);
        require_once '../app/views/templates/header.php';
        require_once '../app/views/users/manage.php';
        require_once '../app/views/templates/footer.php';
    }
    public function edit($id) {
        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'funcionario') {
            header('Location: ' . BASE_URL . '/dashboard');
            exit();
        }
        $userModel = new User();
        $usuario = $userModel->findById($id);
        if ($usuario) {
            require_once '../app/views/templates/header.php';
            require_once '../app/views/users/edit.php';
            require_once '../app/views/templates/footer.php';
        } else {
            header('Location: ' . BASE_URL . '/user/manage');
            exit();
        }
    }
    public function update() {
        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'funcionario' || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/dashboard');
            exit();
        }
        $userModel = new User();
        $data = [
            'id' => $_POST['id'],
            'nome' => trim($_POST['nome']),
            'email' => trim($_POST['email']),
            'tipo' => $_POST['tipo'] ?? null
        ];
        if ($data['id'] == $_SESSION['user_id']) {
            $data['tipo'] = $_SESSION['user_type'];
        }
        if ($userModel->update($data['id'], $data)) {
            header('Location: ' . BASE_URL . '/user/manage');
            exit();
        } else {
            die('Erro ao atualizar usuário. O e-mail pode já estar em uso por outra conta.');
        }
    }
    public function delete($id) {
        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'funcionario') {
            header('Location: ' . BASE_URL . '/dashboard');
            exit();
        }
        if ($id == $_SESSION['user_id']) {
            $_SESSION['message'] = ['type' => 'danger', 'text' => 'Você não pode excluir sua própria conta.'];
            header('Location: ' . BASE_URL . '/user/manage');
            exit();
        }
        $userModel = new User();
        if ($userModel->delete($id)) {
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Usuário excluído com sucesso!'];
        } else {
            $_SESSION['message'] = ['type' => 'danger', 'text' => 'Erro ao excluir o usuário.'];
        }
        header('Location: ' . BASE_URL . '/user/manage');
        exit();
    }
}
?>