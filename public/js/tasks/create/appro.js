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
            <button type="button" class="btn btn-danger btn-sm remove-expedition mt-1">✕</button>
        </div>
    `;
    container.appendChild(div);
}