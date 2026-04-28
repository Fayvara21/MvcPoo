function addThirdPartyItem() {
    const index = Date.now();
    const div = document.createElement('div');
    div.className = 'border rounded p-2 mb-2 third-party-item';
    
    div.innerHTML = `
        <div class="row g-2">
            <div class="col">
                <input class="form-control" name="third_party[${index}][bp]" placeholder="BP">
            </div>
            <div class="col">
                <input class="form-control" name="third_party[${index}][equipement]" placeholder="Équipement">
            </div>
            <div class="col">
                <input class="form-control" type="number" name="third_party[${index}][nb]" value="1" placeholder="Quantité">
            </div>
            <div class="col">
                <input class="form-control" name="third_party[${index}][destination]" placeholder="Destination">
            </div>
            <div class="col">
                <input class="form-control" name="third_party[${index}][order_nb]" placeholder="N° Ordre">
            </div>
            <div class="col-auto">
                <button type="button" class="btn btn-danger remove">✕</button>
            </div>
        </div>
    `;
    
    document.getElementById('thirdPartyList').appendChild(div);
}

window.addThirdPartyItem = addThirdPartyItem;

const addThirdPartyBtn = document.getElementById('addThirdParty');
if (addThirdPartyBtn) {
    const newBtn = addThirdPartyBtn.cloneNode(true);
    addThirdPartyBtn.parentNode.replaceChild(newBtn, addThirdPartyBtn);
    newBtn.addEventListener('click', addThirdPartyItem);
}