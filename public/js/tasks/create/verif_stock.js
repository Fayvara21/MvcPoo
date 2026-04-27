function addVerifStockItem() {
    const index = Date.now();
    const div = document.createElement('div');
    div.className = 'border rounded p-2 mb-2';
    
    div.innerHTML = `
        <div class="row g-2">
            <div class="col">
                <input class="form-control" name="verif_stock[${index}][pn]" placeholder="PN" required>
            </div>
            <div class="col">
                <input class="form-control" type="number" name="verif_stock[${index}][nb]" value="1" placeholder="Quantité">
            </div>
            <div class="col">
                <input class="form-control" name="verif_stock[${index}][name]" placeholder="Libellé">
            </div>
            <div class="col-auto">
                <button type="button" class="btn btn-danger remove">✕</button>
            </div>
        </div>
    `;
    
    document.getElementById('verifStockList').appendChild(div);
}

window.addVerifStockItem = addVerifStockItem;

const addVerifStockBtn = document.getElementById('addVerifStock');
if (addVerifStockBtn) {
    const newBtn = addVerifStockBtn.cloneNode(true);
    addVerifStockBtn.parentNode.replaceChild(newBtn, addVerifStockBtn);
    newBtn.addEventListener('click', addVerifStockItem);
}