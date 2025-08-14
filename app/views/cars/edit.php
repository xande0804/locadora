<style>
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
    <h1 class="display-6 mb-4">Editar Informações do Carro</h1>
    <div class="card shadow-sm">
        <div class="card-body">
            <form action="<?php echo BASE_URL; ?>/car/update" method="POST">
                <input type="hidden" name="id" value="<?php echo $carro['id']; ?>">
                <div class="row">
                    <div class="col-md-4 mb-3 autocomplete-wrapper">
                        <label for="marca_search" class="form-label">Marca</label>
                        <input type="text" class="form-control" id="marca_search" value="<?php echo htmlspecialchars($carro['marca_nome']); ?>" autocomplete="off" required>
                        <div id="marca_results" class="autocomplete-results"></div>
                        <input type="hidden" id="marca_id" name="marca_id" value="<?php echo $carro['marca_id']; ?>">
                    </div>
                    <div class="col-md-4 mb-3 autocomplete-wrapper">
                        <label for="modelo_search" class="form-label">Modelo</label>
                        <input type="text" class="form-control" id="modelo_search" value="<?php echo htmlspecialchars($carro['modelo_nome']); ?>" autocomplete="off" required disabled>
                        <div id="modelo_results" class="autocomplete-results"></div>
                        <input type="hidden" id="modelo_id" name="modelo_id" value="<?php echo $carro['modelo_id']; ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="ano" class="form-label">Ano</label>
                        <input type="number" class="form-control" id="ano" name="ano" min="1990" max="<?php echo date('Y') + 1; ?>" value="<?php echo $carro['ano']; ?>" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="preco_diaria" class="form-label">Preço Diário (R$)</label>
                        <input type="number" class="form-control" id="preco_diaria" name="preco_diaria" value="<?php echo $carro['preco_diaria']; ?>" step="0.01" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="preco_semanal" class="form-label">Preço Semanal (p/ dia)</label>
                        <input type="number" class="form-control" id="preco_semanal" name="preco_semanal" value="<?php echo $carro['preco_semanal']; ?>" placeholder="Opcional" step="0.01">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="preco_mensal" class="form-label">Preço Mensal (p/ dia)</label>
                        <input type="number" class="form-control" id="preco_mensal" name="preco_mensal" value="<?php echo $carro['preco_mensal']; ?>" placeholder="Opcional" step="0.01">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="quantidade_disponivel" class="form-label">Quantidade Disponível</label>
                        <input type="number" class="form-control" id="quantidade_disponivel" name="quantidade_disponivel" min="0" value="<?php echo $carro['quantidade_disponivel']; ?>" required>
                    </div>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                    <a href="<?php echo BASE_URL; ?>/car/index" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    
    // --- LÓGICA DE CÁLCULO DE PREÇO AUTOMÁTICO ---
    const dailyPriceInput = document.getElementById('preco_diaria');
    dailyPriceInput.addEventListener('input', function() {
        const dailyPrice = parseFloat(this.value);
        const weeklyPriceInput = document.getElementById('preco_semanal');
        const monthlyPriceInput = document.getElementById('preco_mensal');
        
        if (!isNaN(dailyPrice) && dailyPrice > 0) {
            weeklyPriceInput.value = (dailyPrice * 0.85).toFixed(2);
            monthlyPriceInput.value = (dailyPrice * 0.70).toFixed(2);
        } else {
            weeklyPriceInput.value = '';
            monthlyPriceInput.value = '';
        }
    });

    // --- LÓGICA DE AUTOCOMPLETE E SELEÇÃO DINÂMICA ---
    const initialMarcaId = document.getElementById('marca_id').value;
    if (initialMarcaId) {
        document.getElementById('modelo_search').disabled = false;
    }

    // Configura o campo de Marcas
    setupAutocomplete(
        document.getElementById('marca_search'),
        document.getElementById('marca_results'),
        document.getElementById('marca_id'),
        (term) => `<?php echo BASE_URL; ?>/car/searchBrands?term=${term}`,
        (selectedItem) => {
            const modeloSearchInput = document.getElementById('modelo_search');
            modeloSearchInput.disabled = false;
            modeloSearchInput.value = '';
            modeloSearchInput.placeholder = 'Clique ou digite para buscar...';
            document.getElementById('modelo_id').value = '';
        }
    );

    // Configura o campo de Modelos
    setupAutocomplete(
        document.getElementById('modelo_search'),
        document.getElementById('modelo_results'),
        document.getElementById('modelo_id'),
        (term) => {
            const marcaId = document.getElementById('marca_id').value;
            return marcaId ? `<?php echo BASE_URL; ?>/car/searchModels/${marcaId}?term=${term}` : null;
        }
    );
    
    // --- FUNÇÕES GÉNICAS DE APOIO ---
    
    /**
     * Configura um campo de input para ter a funcionalidade de autocomplete.
     */
    function setupAutocomplete(input, resultsDiv, hiddenInput, getUrlCallback, onSelectCallback = null) {
        const fetchAndShow = () => {
            // Apenas busca se o campo estiver focado pelo utilizador
            if (document.activeElement === input) {
                fetchAndShowResults(input, resultsDiv, getUrlCallback);
            }
        };
        
        // Adiciona os "escutadores" de eventos
        input.addEventListener('focus', fetchAndShow); // Quando o utilizador clica no campo
        input.addEventListener('input', fetchAndShow); // Quando o utilizador digita

        // Lida com o clique num item da lista de resultados
        resultsDiv.addEventListener('click', (e) => {
            if (e.target && e.target.tagName === 'LI') {
                input.value = e.target.getAttribute('data-nome');
                hiddenInput.value = e.target.getAttribute('data-id');
                resultsDiv.style.display = 'none'; // Esconde a lista de resultados
                if (onSelectCallback) {
                    onSelectCallback(e.target); // Executa a ação extra (ex: carregar modelos)
                }
            }
        });
    }

    /**
     * Busca os dados na API e exibe os resultados na div apropriada.
     */
    function fetchAndShowResults(input, resultsDiv, getUrlCallback) {
        const url = getUrlCallback(input.value); // Pega a URL (pode depender do que foi digitado)
        if (url === null) return; // Não faz nada se a URL for nula (ex: modelo sem marca selecionada)
        
        fetch(url)
            .then(response => response.json())
            .then(data => {
                let resultsHTML = '<ul class="list-group list-group-flush">';
                if (data.length > 0) {
                    // Cria uma linha (<li>) para cada resultado
                    data.forEach(item => {
                        resultsHTML += `<li class="list-group-item" data-id="${item.id}" data-nome="${item.nome}">${item.nome}</li>`;
                    });
                } else {
                    resultsHTML += '<li class="list-group-item disabled">Nenhum resultado encontrado</li>';
                }
                resultsHTML += '</ul>';
                resultsDiv.innerHTML = resultsHTML;
                resultsDiv.style.display = 'block'; // Mostra a lista de resultados
            });
    }

    // Evento global para esconder os resultados da busca ao clicar fora do componente.
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.autocomplete-wrapper')) {
            document.getElementById('marca_results').style.display = 'none';
            document.getElementById('modelo_results').style.display = 'none';
        }
    });
});
</script>