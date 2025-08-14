<?php

class CarController {

    public function __construct() {
        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'funcionario') {
            header('Location: ' . BASE_URL . '/dashboard');
            exit();
        }
        $timeout = 1800;
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
            session_unset(); session_destroy(); header('Location: ' . BASE_URL . '/user/login'); exit();
        }
        $_SESSION['last_activity'] = time();
    }

    public function index($page = 1) {
        $carModel = new Car();
        $searchTerm = isset($_GET['search']) ? trim($_GET['search']) : null;
        $limit = 5;
        $total_carros = $carModel->countAll($searchTerm);
        $total_pages = ceil($total_carros / $limit);
        $offset = ($page - 1) * $limit;
        $carros = $carModel->findPaginated($limit, $offset, $searchTerm);
        require_once '../app/views/templates/header.php';
        require_once '../app/views/cars/manage.php';
        require_once '../app/views/templates/footer.php';
    }

    public function create() {
        require_once '../app/views/templates/header.php';
        require_once '../app/views/cars/create.php';
        require_once '../app/views/templates/footer.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $carModel = new Car();
            $modelo_id = filter_input(INPUT_POST, 'modelo_id', FILTER_VALIDATE_INT);
            $ano = filter_input(INPUT_POST, 'ano', FILTER_VALIDATE_INT);
            $preco_diaria = filter_input(INPUT_POST, 'preco_diaria', FILTER_VALIDATE_FLOAT);
            $preco_semanal = filter_input(INPUT_POST, 'preco_semanal', FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE);
            $preco_mensal = filter_input(INPUT_POST, 'preco_mensal', FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE);
            $quantidade = filter_input(INPUT_POST, 'quantidade_disponivel', FILTER_VALIDATE_INT);

            // Validação simples para garantir que um modelo foi selecionado
            if (empty($modelo_id)) {
                die('Erro: Um modelo de veículo válido deve ser selecionado.');
            }

            if ($carModel->create($modelo_id, $ano, $preco_diaria, $preco_semanal, $preco_mensal, $quantidade)) {
                header('Location: ' . BASE_URL . '/car/index');
            } else {
                die('Erro ao salvar o carro.');
            }
        } else {
            header('Location: ' . BASE_URL . '/car/index');
        }
    }

    public function getModelsByBrand($brandId) {
        $carModel = new Car();
        $models = $carModel->findModelsByBrandId($brandId);
        header('Content-Type: application/json');
        echo json_encode($models);
    }

    public function searchBrands() {
        $term = $_GET['term'] ?? '';
        $carModel = new Car();
        $brands = $carModel->searchBrandsByName($term);
        header('Content-Type: application/json');
        echo json_encode($brands);
    }
    
    public function edit($id) {
        $carModel = new Car();
        // Busca os dados do carro específico que estamos a editar
        $carro = $carModel->findById($id);

        // Busca a lista de TODAS as marcas para preencher o dropdown
        $marcas = $carModel->findAllBrands();

        if ($carro) {
            require_once '../app/views/templates/header.php';
            require_once '../app/views/cars/edit.php';
            require_once '../app/views/templates/footer.php';
        } else {
            header('Location: ' . BASE_URL . '/car/index');
            exit();
        }
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $carModel = new Car();

            $id = $_POST['id'];
            $modelo_id = filter_input(INPUT_POST, 'modelo_id', FILTER_VALIDATE_INT);
            $ano = filter_input(INPUT_POST, 'ano', FILTER_VALIDATE_INT);
            $preco_diaria = filter_input(INPUT_POST, 'preco_diaria', FILTER_VALIDATE_FLOAT);
            $preco_semanal = filter_input(INPUT_POST, 'preco_semanal', FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE);
            $preco_mensal = filter_input(INPUT_POST, 'preco_mensal', FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE);
            $quantidade = filter_input(INPUT_POST, 'quantidade_disponivel', FILTER_VALIDATE_INT);

            if (empty($modelo_id)) {
                die('Erro: Um modelo de veículo válido deve ser selecionado.');
            }

            if ($carModel->update($id, $modelo_id, $ano, $preco_diaria, $preco_semanal, $preco_mensal, $quantidade)) {
                header('Location: ' . BASE_URL . '/car/index');
            } else {
                die('Erro ao atualizar o carro.');
            }
        } else {
            header('Location: ' . BASE_URL . '/car/index');
        }
    }

    public function delete($id) {
        $carModel = new Car();
        if ($carModel->delete($id)) {
            header('Location: ' . BASE_URL . '/car/index');
        } else {
            die('Erro ao excluir o carro.');
        }
    }

    public function searchModels($brandId) {
        $term = $_GET['term'] ?? '';
        $carModel = new Car();
        $models = $carModel->searchModelsByName($brandId, $term);
        header('Content-Type: application/json');
        echo json_encode($models);
    }
}
?>