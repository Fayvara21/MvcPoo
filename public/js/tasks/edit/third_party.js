function addThirdPartyItem() {
    const container = document.getElementById('thirdPartyList');
    const index = Date.now();
    const div = document.createElement('div');
    div.className = 'third-party-item border rounded p-2 mb-2';
    div.innerHTML = `
        <div class="d-flex flex-column gap-2">
            <div>
                <label class="form-label fw-medium">BP :</label>
                <input class="form-control" name="third_party[${index}][bp]" placeholder="BP">
            </div>
            <div>
                <label class="form-label fw-medium">Équipement :</label>
                <input class="form-control" name="third_party[${index}][equipement]" placeholder="Équipement">
            </div>
            <div>
                <label class="form-label fw-medium">Quantité :</label>
                <input class="form-control" type="number" name="third_party[${index}][nb]" value="1">
            </div>
            <div>
                <label class="form-label fw-medium">Destination :</label>
                <input class="form-control" name="third_party[${index}][destination]" placeholder="Destination">
            </div>
            <div>
                <label class="form-label fw-medium">N° Ordre :</label>
                <input class="form-control" name="third_party[${index}][order_nb]" placeholder="N° Ordre">
            </div>
            <button type="button" class="btn btn-danger btn-sm remove-third-party mt-1">✕</button>
        </div>
    `;
    container.appendChild(div);
}

window.addThirdPartyItem = addThirdPartyItem;

const addThirdPartyBtn = document.getElementById('addThirdParty');
if (addThirdPartyBtn) {
    const newBtn = addThirdPartyBtn.cloneNode(true);
    addThirdPartyBtn.parentNode.replaceChild(newBtn, addThirdPartyBtn);
    newBtn.addEventListener('click', addThirdPartyItem);
}