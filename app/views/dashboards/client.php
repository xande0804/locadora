<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="display-6">Painel do Cliente</h1>
        <a href="<?php echo BASE_URL; ?>/reservation/history" class="btn btn-outline-secondary">Ver Histórico de Reservas</a>
    </div>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?php echo $_SESSION['message']['type']; ?> alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['message']['text']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <p class="lead">
        Bem-vindo, <?php echo htmlspecialchars($_SESSION['user_name']); ?>! Escolha um veículo e as datas para fazer a sua reserva.
    </p>
    <hr>

    <h3>Nossa Frota Disponível</h3>
    <div class="row mt-4">
        <?php if (!empty($carros)): ?>
            <?php foreach ($carros as $carro): ?>
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?php echo htmlspecialchars($carro['modelo']); ?></h5>
                            <p class="card-text">
                                <strong>Ano:</strong> <?php echo htmlspecialchars($carro['ano']); ?><br>
                                <span class="text-muted">A partir de</span>
                                <strong class="fs-5">R$ <?php echo number_format($carro['preco_diaria'], 2, ',', '.'); ?></strong>
                                <span class="text-muted">/dia</span>
                            </p>
                            <button type="button" class="btn btn-primary mt-auto reservation-btn" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#reservationModal"
                                    data-car-id="<?php echo $carro['id']; ?>"
                                    data-car-model="<?php echo htmlspecialchars($carro['modelo']); ?>"
                                    data-price-daily="<?php echo $carro['preco_diaria']; ?>"
                                    data-price-weekly="<?php echo $carro['preco_semanal']; ?>"
                                    data-price-monthly="<?php echo $carro['preco_mensal']; ?>">
                                Reservar
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12"><div class="alert alert-warning">Nenhum carro disponível no momento.</div></div>
        <?php endif; ?>
    </div>
</div>

<div class="modal fade" id="reservationModal" tabindex="-1" aria-labelledby="reservationModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="reservationModalLabel">Fazer Reserva</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="reservationForm" action="<?php echo BASE_URL; ?>/reservation/create" method="POST">
          <div class="modal-body">
            <h6 id="modalCarModel" class="mb-3"></h6>
            <input type="hidden" name="car_id" id="modalCarId">
            
            <div class="mb-3">
                <label for="date_range" class="form-label">Período da Reserva</label>
                <input type="text" class="form-control" id="date_range" placeholder="Selecione o período..." required>
            </div>

            <input type="hidden" name="data_inicio" id="data_inicio">
            <input type="hidden" name="data_fim" id="data_fim">
            
            <div id="price-calculation" class="mt-3 text-center"></div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary" id="confirmReservationBtn" disabled>Confirmar Reserva</button>
          </div>
      </form>
    </div>
  </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const reservationModal = document.getElementById('reservationModal');
        if (!reservationModal) return;

        const priceCalculationDiv = document.getElementById('price-calculation');
        const confirmBtn = document.getElementById('confirmReservationBtn');
        const startDateInput = document.getElementById('data_inicio');
        const endDateInput = document.getElementById('data_fim');
        let prices = {};
        let flatpickrInstance;

        flatpickrInstance = flatpickr("#date_range", {
            mode: "range",
            dateFormat: "d/m/Y",
            minDate: "today",
            locale: { firstDayOfWeek: 1 },
            onChange: function(selectedDates) {
                if (selectedDates.length === 2) {
                    startDateInput.value = formatDateForInput(selectedDates[0]);
                    endDateInput.value = formatDateForInput(selectedDates[1]);
                    calculatePrice(selectedDates[0], selectedDates[1]);
                } else {
                    priceCalculationDiv.innerHTML = '';
                    confirmBtn.disabled = true;
                }
            }
        });

        reservationModal.addEventListener('show.bs.modal', event => {
            const button = event.relatedTarget;
            prices = {
                daily: parseFloat(button.getAttribute('data-price-daily')),
                weekly: parseFloat(button.getAttribute('data-price-weekly')),
                monthly: parseFloat(button.getAttribute('data-price-monthly'))
            };
            
            const carId = button.getAttribute('data-car-id');
            const carModel = button.getAttribute('data-car-model');

            reservationModal.querySelector('#reservationModalLabel').textContent = `Reservar: ${carModel}`;
            reservationModal.querySelector('#modalCarModel').textContent = `Veículo: ${carModel}`;
            reservationModal.querySelector('#modalCarId').value = carId;

            flatpickrInstance.clear();
            priceCalculationDiv.innerHTML = '';
            confirmBtn.disabled = true;
        });

        function calculatePrice(startDate, endDate) {
            const timeDiff = endDate.getTime() - startDate.getTime();
            const days = Math.ceil(timeDiff / (1000 * 3600 * 24)) + 1; // +1 to include the last day
            
            if (days > 0) {
                let pricePerDay = prices.daily;
                let priceType = "diário";

                if (days >= 30 && prices.monthly > 0) {
                    pricePerDay = prices.monthly;
                    priceType = "mensal";
                } else if (days >= 7 && prices.weekly > 0) {
                    pricePerDay = prices.weekly;
                    priceType = "semanal";
                }

                const totalPrice = days * pricePerDay;
                priceCalculationDiv.innerHTML = `
                    <p class="mb-1"><strong>Período:</strong> ${days} dia(s)</p>
                    <p class="mb-1 text-success"><small>Preço ${priceType} aplicado: R$ ${pricePerDay.toFixed(2).replace('.', ',')} por dia</small></p>
                    <h5 class="mb-0"><strong>Preço Total: R$ ${totalPrice.toFixed(2).replace('.', ',')}</strong></h5>
                `;
                confirmBtn.disabled = false;
            }
        }

        function formatDateForInput(date) {
            const d = new Date(date);
            const year = d.getFullYear();
            const month = ('0' + (d.getMonth() + 1)).slice(-2);
            const day = ('0' + d.getDate()).slice(-2);
            return `${year}-${month}-${day}`;
        }
    });
</script>