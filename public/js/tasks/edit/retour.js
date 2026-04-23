function addRetourItem() {
    const container = document.getElementById('retourList');
    const index = Date.now();
    const div = document.createElement('div');
    div.className = 'retour-item border rounded p-2 mb-2';
    div.innerHTML = `
        <div class="d-flex flex-column gap-2">
            <div>
                <label class="form-label fw-medium">PN</label>
                <input class="form-control" name="retour[${index}][PN]" placeholder="PN">
            </div>
            <div>
                <label class="form-label fw-medium">Quantité</label>
                <input class="form-control" type="number" name="retour[${index}][nb]" value="1">
            </div>
            <div>
                <label class="form-label fw-medium">SN</label>
                <input class="form-control" name="retour[${index}][sn]" placeholder="SN">
            </div>
            <div>
                <label class="form-label fw-medium">Certification</label>
                <input class="form-control" name="retour[${index}][certif]" placeholder="Certification">
            </div>
            <button type="button" class="btn btn-danger btn-sm remove-retour mt-1">✕</button>
        </div>
    `;
    container.appendChild(div);
}

window.addRetourItem = addRetourItem;

const addRetourBtn = document.getElementById('addRetour');
if (addRetourBtn) {
    const newBtn = addRetourBtn.cloneNode(true);
    addRetourBtn.parentNode.replaceChild(newBtn, addRetourBtn);
    newBtn.addEventListener('click', addRetourItem);
}