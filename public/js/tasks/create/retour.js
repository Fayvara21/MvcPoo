// retour.js - identical structure, just different IDs and names
function addRetourItem() {
    const index = Date.now();
    const div = document.createElement('div');
    div.className = 'border rounded p-2 mb-2';
    div.innerHTML = `
        <div class="row g-2">
            <div class="col">
                <input class="form-control" name="retour[${index}][PN]" placeholder="PN" required>
            </div>
            <div class="col">
                <input class="form-control" type="number" name="retour[${index}][nb]" value="1" placeholder="Quantité">
            </div>
            <div class="col-auto">
                <button type="button" class="btn btn-danger remove">✕</button>
            </div>
        </div>
    `;
    document.getElementById('retourList').appendChild(div);
}

window.addRetourItem = addRetourItem;

const addRetourBtn = document.getElementById('addRetour');
if (addRetourBtn) {
    const newBtn = addRetourBtn.cloneNode(true);
    addRetourBtn.parentNode.replaceChild(newBtn, addRetourBtn);
    newBtn.addEventListener('click', addRetourItem);
}