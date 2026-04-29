function addExpeditionItem() {
    const container = document.getElementById('expeditionList');
    const index = Date.now();
    const div = document.createElement('div');
    div.className = 'expedition-item border rounded p-2 mb-2';
    div.innerHTML = `
        <div class="d-flex flex-column gap-2">
            <div>
                <label class="form-label fw-medium">PN :</label>
                <input class="form-control" name="expedition[${index}][pn]" placeholder="PN">
            </div>
            <div>
                <label class="form-label fw-medium">Nom :</label>
                <input class="form-control" name="expedition[${index}][name]" placeholder="Nom">
            </div>
            <div>
                <label class="form-label fw-medium">Quantité :</label>
                <input class="form-control" type="number" name="expedition[${index}][nb]" value="1">
            </div>
            <div>
                <label class="form-label fw-medium">Emplacement :</label>
                <input class="form-control" name="expedition[${index}][location]" placeholder="Emplacement">
            </div> 
            <div>
                <label class="form-label fw-medium">N° Commande :</label>
                <input class="form-control" name="expedition[${index}][order_nb]" placeholder="N° Commande">
            </div> 
            <div>
                <label class="form-label fw-medium">Destinataire :</label>
                <input class="form-control" name="expedition[${index}][destination]" placeholder="Destinataire">
            </div> 
            <div>
                <label class="form-label fw-medium">Compte :</label>
                <input class="form-control" name="expedition[${index}][account]" placeholder="Compte">
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="expedition[${index}][third_party]" value="1" id="expedition_${index}_third_party">
                <label class="form-check-label" for="expedition_${index}_third_party">3rd Party</label>
            </div>
            <button type="button" class="btn btn-danger btn-sm remove-expedition mt-1">✕</button>
        </div>
    `;
    container.appendChild(div);
}

window.addExpeditionItem = addExpeditionItem;

const addExpeditionBtn = document.getElementById('addExpedition');
if (addExpeditionBtn) {
    const newBtn = addExpeditionBtn.cloneNode(true);
    addExpeditionBtn.parentNode.replaceChild(newBtn, addExpeditionBtn);
    newBtn.addEventListener('click', addExpeditionItem);
}