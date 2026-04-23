function addApproItem() {
    const container = document.getElementById('approList');
    const index = Date.now();
    const div = document.createElement('div');
    div.className = 'appro-item border rounded p-2 mb-2';
    div.innerHTML = `
        <div class="d-flex flex-column gap-2">
            <div>
                <label class="form-label fw-medium">PN</label>
                <input class="form-control" name="appro[${index}][pn]" placeholder="PN" required>
            </div>
            <div>
                <label class="form-label fw-medium">Quantité</label>
                <input class="form-control" type="number" name="appro[${index}][nb]" value="1">
            </div>
            <div>
                <label class="form-label fw-medium">Désignation</label>
                <input class="form-control" name="appro[${index}][designation]" placeholder="Désignation">
            </div>
            <div>
                <label class="form-label fw-medium">OF</label>
                <input class="form-control" name="appro[${index}][of]" placeholder="OF">
            </div>
            <div>
                <label class="form-label fw-medium">Emplacement</label>
                <input class="form-control" name="appro[${index}][location]" placeholder="Emplacement">
            </div>
            <div>
                <label class="form-label fw-medium">Avion</label>
                <input class="form-control" name="appro[${index}][plane]" placeholder="Avion">
            </div>
            <div>
                <label class="form-label fw-medium">OE</label>
                <input class="form-control" name="appro[${index}][oe]" placeholder="OE">
            </div>
            <button type="button" class="btn btn-danger btn-sm remove-appro mt-1">✕</button>
        </div>
    `;
    container.appendChild(div);
}

window.addApproItem = addApproItem;

const addApproBtn = document.getElementById('addAppro');
if (addApproBtn) {
    const newBtn = addApproBtn.cloneNode(true);
    addApproBtn.parentNode.replaceChild(newBtn, addApproBtn);
    newBtn.addEventListener('click', addApproItem);
}