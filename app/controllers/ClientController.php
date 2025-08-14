<?php
class ClientController {
    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/user/login');
            exit();
        }
    }
    public function complete() {
        require_once '../app/views/templates/header.php';
        require_once '../app/views/clients/complete_profile.php';
        require_once '../app/views/templates/footer.php';
    }
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL);
            exit();
        }
        $clientModel = new Client();
        $data = [
            'usuario_id' => $_SESSION['user_id'],
            'cpf' => $_POST['cpf'],
            'cnh' => $_POST['cnh'],
            'data_nascimento' => $_POST['data_nascimento'],
            'telefone' => $_POST['telefone'],
            'endereco' => $_POST['endereco'],
            'sexo' => $_POST['sexo']
        ];
        if ($clientModel->create($data)) {
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Perfil completo! Agora você já pode alugar veículos.'];
            header('Location: ' . BASE_URL . '/dashboard');
            exit();
        } else {
            $error_message = "Erro ao salvar o perfil. Verifique se o CPF ou CNH já não estão cadastrados.";
            require_once '../app/views/templates/header.php';
            require_once '../app/views/clients/complete_profile.php';
            require_once '../app/views/templates/footer.php';
        }
    }
}
?>