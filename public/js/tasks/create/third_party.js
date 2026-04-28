// Make sure this function is defined and exposed globally
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

// Expose to global scope
window.addThirdPartyItem = addThirdPartyItem;

// Handle removal of items (if not already present)
document.addEventListener('click', function(e) {
    if (e.target && e.target.classList.contains('remove')) {
        const item = e.target.closest('.third-party-item, .border.rounded.p-2.mb-2');
        if (item) {
            item.remove();
        }
    }
});