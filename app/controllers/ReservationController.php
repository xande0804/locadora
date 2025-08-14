<?php

class ReservationController {

    public function __construct() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] === 'funcionario') {
            header('Location: ' . BASE_URL);
            exit();
        }
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/dashboard');
            exit();
        }
        $carId = $_POST['car_id'];
        $startDate = $_POST['data_inicio'];
        $endDate = $_POST['data_fim'];
        $userId = $_SESSION['user_id'];
        if (empty($carId) || empty($startDate) || empty($endDate) || $endDate <= $startDate) {
            $_SESSION['message'] = ['type' => 'danger', 'text' => 'Datas inválidas. Por favor, tente novamente.'];
            header('Location: ' . BASE_URL . '/dashboard');
            exit();
        }
        $clientModel = new Client();
        if (!$clientModel->findByUserId($userId)) {
            header('Location: ' . BASE_URL . '/client/complete');
            exit();
        }
        $reservationModel = new Reservation();
        if (!$reservationModel->isCarAvailable($carId, $startDate, $endDate)) {
            $_SESSION['message'] = ['type' => 'danger', 'text' => 'Desculpe, este veículo não está disponível para o período selecionado.'];
            header('Location: ' . BASE_URL . '/dashboard');
            exit();
        }
        $carModel = new Car();
        $car = $carModel->findById($carId);
        $totalPrice = $this->calculateTotalPrice($startDate, $endDate, $car);
        $data = [
            'id_cliente' => $userId,
            'id_carro' => $carId,
            'data_inicio' => $startDate,
            'data_fim' => $endDate,
            'preco_total' => $totalPrice
        ];
        if ($reservationModel->create($data)) {
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Reserva confirmada com sucesso!'];
        } else {
            $_SESSION['message'] = ['type' => 'danger', 'text' => 'Ocorreu um erro ao processar a sua reserva. Tente novamente.'];
        }
        header('Location: ' . BASE_URL . '/dashboard');
        exit();
    }

    /**
     * Exibe a página com o histórico de reservas do cliente. (NOVO)
     */
    public function history() {
        $reservationModel = new Reservation();
        $reservas = $reservationModel->findHistoryByClient($_SESSION['user_id']);

        require_once '../app/views/templates/header.php';
        require_once '../app/views/reservation/history.php'; // Apontando para o novo local
        require_once '../app/views/templates/footer.php';
    }

    /**
     * Processa o cancelamento de uma reserva. (NOVO)
     */
    public function cancel($reservationId) {
        $reservationModel = new Reservation();
        if ($reservationModel->cancelById($reservationId, $_SESSION['user_id'])) {
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Reserva cancelada com sucesso!'];
        } else {
            $_SESSION['message'] = ['type' => 'danger', 'text' => 'Não foi possível cancelar a reserva.'];
        }
        header('Location: ' . BASE_URL . '/reservation/history');
        exit();
    }

    private function calculateTotalPrice($startDate, $endDate, $car) {
        $start = new DateTime($startDate);
        $end = new DateTime($endDate);
        $today = new DateTime('today');

        // Calcula a diferença de dias entre o início da reserva e hoje
        $diff_to_start = $today->diff($start)->days;

        // Calcula a duração da reserva
        $end->modify('+1 day');
        $interval = $start->diff($end);
        $days = $interval->days == 0 ? 1 : $interval->days;

        // Calcula o preço base por dia com base na duração
        $pricePerDay = $car['preco_diaria'];
        if ($days >= 30 && !empty($car['preco_mensal'])) {
            $pricePerDay = $car['preco_mensal'];
        } elseif ($days >= 7 && !empty($car['preco_semanal'])) {
            $pricePerDay = $car['preco_semanal'];
        }

        $baseTotalPrice = $days * $pricePerDay;
        $finalTotalPrice = $baseTotalPrice;

        // Aplica as regras de preço dinâmico
        if ($diff_to_start < 2) {
            // Taxa de Urgência de +15%
            $finalTotalPrice *= 1.15;
        } elseif ($diff_to_start >= 30) {
            // Desconto de Antecedência de -10%
            $finalTotalPrice *= 0.90;
        }

        // Arredonda para 2 casas decimais
        return round($finalTotalPrice, 2);
    }
}
?>