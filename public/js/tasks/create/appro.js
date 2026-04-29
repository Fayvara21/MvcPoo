function addApproItem() {
    const container = document.getElementById('approList');
    const index = Date.now();
    const div = document.createElement('div');
    div.className = 'expedition-item border rounded p-2 mb-2';
    div.innerHTML = `
        <div class="d-flex flex-row gap-2">
            <div>
                <input class="form-control" name="appro[${index}][pn]" placeholder="PN">
            </div>
            <div>
                <input class="form-control" name="appro[${index}][location]" placeholder="Emplacement">
            </div>
            <div>
                <input class="form-control" type="number" name="appro[${index}][nb]" value="1">
            </div>
            <button type="button" class="btn btn-danger btn-sm remove-expedition mt-1">✕</button>
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