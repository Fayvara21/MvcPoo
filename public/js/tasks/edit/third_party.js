function addVerifStockItem() {
    const container = document.getElementById('verifStockList');
    const index = Date.now();
    const div = document.createElement('div');
    div.className = 'verif-stock-item border rounded p-2 mb-2';
    div.innerHTML = `
        <div class="d-flex flex-column gap-2">
            <div>
                <label class="form-label fw-medium">PN :</label>
                <input class="form-control" name="verif_stock[${index}][pn]" placeholder="PN" required>
            </div>
            <div>
                <label class="form-label fw-medium">Quantité :</label>
                <input class="form-control" type="number" name="verif_stock[${index}][nb]" value="1">
            </div>
            <div>
                <label class="form-label fw-medium">Libellé :</label>
                <input class="form-control" name="verif_stock[${index}][name]" placeholder="Libellé">
            </div>
            <div>
                <label class="form-label fw-medium">Emplacement :</label>
                <input class="form-control" name="verif_stock[${index}][location]" placeholder="Emplacement">
            </div>
            <div>
                <label class="form-label fw-medium">Remarques :</label>
                <input class="form-control" name="verif_stock[${index}][remarks]" placeholder="Remarques">
            </div>
            <button type="button" class="btn btn-danger btn-sm remove-verif-stock mt-1">✕</button>
        </div>
    `;
    container.appendChild(div);
}

window.addVerifStockItem = addVerifStockItem;

const addVerifStockBtn = document.getElementById('addVerifStock');
if (addVerifStockBtn) {
    const newBtn = addVerifStockBtn.cloneNode(true);
    addVerifStockBtn.parentNode.replaceChild(newBtn, addVerifStockBtn);
    newBtn.addEventListener('click', addVerifStockItem);
}