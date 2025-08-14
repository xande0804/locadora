<?php
class HomeController {
    public function index() {
        $carModel = new Car();
        $carros = $carModel->findAllAvailable();
        require_once '../app/views/templates/header.php';
        require_once '../app/views/home/index.php';
        require_once '../app/views/templates/footer.php';
    }
}
?>