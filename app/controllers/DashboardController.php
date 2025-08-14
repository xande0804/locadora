<?php
class DashboardController {
    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/user/login');
            exit();
        }
        if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'funcionario') {
            $timeout = 1800;
            if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
                session_unset();
                session_destroy();
                header('Location: ' . BASE_URL . '/user/login');
                exit();
            }
            $_SESSION['last_activity'] = time();
        }
    }
    public function index() {
        if ($_SESSION['user_type'] === 'comun') {
            $this->clientDashboard();
        } elseif ($_SESSION['user_type'] === 'funcionario') {
            $this->employeeDashboard();
        } else {
            header('Location: ' . BASE_URL . '/user/logout');
            exit();
        }
    }
    private function clientDashboard() {
        $carModel = new Car();
        $carros = $carModel->findAllAvailable();
        require_once '../app/views/templates/header.php';
        require_once '../app/views/dashboards/client.php';
        require_once '../app/views/templates/footer.php';
    }
    private function employeeDashboard() {
        require_once '../app/views/templates/header.php';
        require_once '../app/views/dashboards/employee.php';
        require_once '../app/views/templates/footer.php';
    }
}
?>