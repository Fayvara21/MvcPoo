// appro.js
function addApproItem() {
    const index = Date.now();
    const div = document.createElement('div');
    div.className = 'border rounded p-2 mb-2';
    div.innerHTML = `
        <div class="row g-2">
            <div class="col"> 
                <input class="form-control" name="appro[${index}][pn]" placeholder="PN" required>
            </div>
            <div class="col"> 
                <input class="form-control" name="appro[${index}][location]" placeholder="Emplacement">
            </div>
            <div class="col"> 
                <input class="form-control" type="number" name="appro[${index}][nb]" value="1" placeholder="Quantité">
            </div>
            <div class="col-auto"> 
                <button type="button" class="btn btn-danger remove">✕</button> 
            </div>
        </div>
    `;
    document.getElementById('approList').appendChild(div);
}

window.addApproItem = addApproItem;

const addApproBtn = document.getElementById('addAppro');
if (addApproBtn) {
    const newBtn = addApproBtn.cloneNode(true);
    addApproBtn.parentNode.replaceChild(newBtn, addApproBtn);
    newBtn.addEventListener('click', addApproItem);
}