<style>
    /* Estilos para a caixa de resultados do autocomplete */
    .autocomplete-wrapper { position: relative; }
    .autocomplete-results {
        position: absolute; display: none; border: 1px solid #dee2e6;
        border-top: none; max-height: 200px; overflow-y: auto;
        width: 100%; z-index: 1000; background-color: white;
    }
    .autocomplete-results .list-group-item { cursor: pointer; }
    .autocomplete-results .list-group-item:hover { background-color: #f8f9fa; }
</style>

<div class="container">
    <h1 class="display-6 mb-4">Adicionar Novo Carro à Frota</h1>
    <div class="card shadow-sm">
        <div class="card-body">
            <form id="addCarForm" action="<?php echo BASE_URL; ?>/car/store" method="POST">
                <div class="row">
                    <div class="col-md-4 mb-3 autocomplete-wrapper">
                        <label for="marca_search" class="form-label">Marca</label>
                        <input type="text" class="form-control" id="marca_search" placeholder="Digite para buscar..." autocomplete="off" required>
                        <div id="marca_results" class="autocomplete-results"></div>
                        <input type="hidden" id="marca_id" name="marca_id">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="modelo_id" class="form-label">Modelo</label>
                        <select class="form-select" id="modelo_id" name="modelo_id" required disabled>
                            <option value="">Selecione a Marca Primeiro</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="ano" class="form-label">Ano</label>
                        <input type="number" class="form-control" id="ano" name="ano" min="1990" max="<?php echo date('Y') + 1; ?>" required>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="preco_diaria" class="form-label">Preço Diário (R$)</label>
                        <input type="number" class="form-control" id="preco_diaria" name="preco_diaria" placeholder="Ex: 120.50" step="0.01" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="preco_semanal" class="form-label">Preço Semanal (p/ dia)</label>
                        <input type="number" class="form-control" id="preco_semanal" name="preco_semanal" placeholder="Calculado automaticamente" step="0.01">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="preco_mensal" class="form-label">Preço Mensal (p/ dia)</label>
                        <input type="number" class="form-control" id="preco_mensal" name="preco_mensal" placeholder="Calculado automaticamente" step="0.01">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                         <label for="quantidade_disponivel" class="form-label">Quantidade Disponível</label>
                        <input type="number" class="form-control" id="quantidade_disponivel" name="quantidade_disponivel" min="0" required>
                    </div>
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">Salvar Carro</button>
                    <a href="<?php echo BASE_URL; ?>/car/index" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // --- SCRIPT DE CÁLCULO DE PREÇO AUTOMÁTICO ---
    const dailyPriceInput = document.getElementById('preco_diaria');
    const weeklyPriceInput = document.getElementById('preco_semanal');
    const monthlyPriceInput = document.getElementById('preco_mensal');

    dailyPriceInput.addEventListener('input', function() {
        const dailyPrice = parseFloat(this.value);
        if (!isNaN(dailyPrice) && dailyPrice > 0) {
            weeklyPriceInput.value = (dailyPrice * 0.85).toFixed(2);
            monthlyPriceInput.value = (dailyPrice * 0.70).toFixed(2);
        } else {
            weeklyPriceInput.value = '';
            monthlyPriceInput.value = '';
        }
    });

    // --- LÓGICA DO AUTOCOMPLETE E SELETOR DINÂMICO ---
    const marcaSearchInput = document.getElementById('marca_search');
    const marcaResultsDiv = document.getElementById('marca_results');
    const marcaIdInput = document.getElementById('marca_id');
    const modeloSelect = document.getElementById('modelo_id');

    // Função genérica e reutilizável para o Autocomplete (que já tínhamos)
    function setupAutocomplete(input, resultsDiv, hiddenInput, getUrlCallback, onSelectCallback = null) {
        input.addEventListener('focus', () => { if (input.value === '') { fetchAndShowResults(input, resultsDiv, getUrlCallback); } });
        input.addEventListener('input', () => {
            hiddenInput.value = '';
            if (onSelectCallback && input === marcaSearchInput) {
                 modeloSelect.disabled = true; modeloSelect.value = ''; modeloSearchInput.value = ''; modeloIdInput.value = '';
                 modeloSearchInput.placeholder = 'Selecione a marca primeiro...';
            }
            fetchAndShowResults(input, resultsDiv, getUrlCallback);
        });
        resultsDiv.addEventListener('click', (e) => {
            if (e.target && e.target.tagName === 'LI') {
                input.value = e.target.getAttribute('data-nome');
                hiddenInput.value = e.target.getAttribute('data-id');
                resultsDiv.style.display = 'none';
                if (onSelectCallback) { onSelectCallback(e.target); }
            }
        });
        document.addEventListener('click', (e) => { if (e.target !== input) { resultsDiv.style.display = 'none'; } });
    }
    
    // Função auxiliar para fazer a chamada fetch e mostrar os resultados (que já tínhamos)
    function fetchAndShowResults(input, resultsDiv, getUrlCallback) {
        const url = getUrlCallback();
        if (url === null) return;
        if (document.activeElement !== input && input.value === '') { resultsDiv.style.display = 'none'; return; }
        fetch(url)
            .then(response => response.json())
            .then(data => {
                let resultsHTML = '<ul class="list-group list-group-flush">';
                if (data.length > 0) {
                    data.forEach(item => { resultsHTML += `<li class="list-group-item" data-id="${item.id}" data-nome="${item.nome}">${item.nome}</li>`; });
                } else {
                    resultsHTML += '<li class="list-group-item disabled">Nenhum resultado encontrado</li>';
                }
                resultsHTML += '</ul>';
                resultsDiv.innerHTML = resultsHTML;
                resultsDiv.style.display = 'block';
            });
    }

    // Lógica para a busca de MARCAS
    setupAutocomplete(marcaSearchInput, marcaResultsDiv, marcaIdInput, 
        () => `<?php echo BASE_URL; ?>/car/searchBrands?term=${marcaSearchInput.value}`,
        (item) => {
            modeloSelect.disabled = false;
            modeloSelect.innerHTML = '<option value="">Selecione o Modelo</option>';
            fetchModels(item.getAttribute('data-id'));
        }
    );

    // Função para buscar os modelos (que já tínhamos)
    function fetchModels(marcaId) {
        modeloSelect.innerHTML = '<option value="">A carregar...</option>';
        if (marcaId) {
            fetch(`<?php echo BASE_URL; ?>/car/getModelsByBrand/${marcaId}`)
                .then(response => response.json())
                .then(data => {
                    modeloSelect.innerHTML = '<option value="">Selecione o Modelo</option>';
                    data.forEach(modelo => {
                        const option = new Option(modelo.nome, modelo.id);
                        modeloSelect.appendChild(option);
                    });
                });
        }
    }
</script>