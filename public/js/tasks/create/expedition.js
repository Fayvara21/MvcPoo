// expedition.js
function addExpeditionItem() {
    const index = Date.now();
    const div = document.createElement('div');
    div.className = 'border rounded p-2 mb-2';
    div.innerHTML = `
        <div class="row g-2">
            <div class="col"> 
                <input class="form-control" name="expedition_items[${index}][pn]" placeholder="PN">
            </div>
            <div class="col"> 
                <input class="form-control" name="expedition_items[${index}][name]" placeholder="Nom">
            </div>
            <div class="col"> 
                <input class="form-control" type="number" name="expedition_items[${index}][nb]" value="1" placeholder="Quantité">
            </div>
            <div class="col-auto"> 
                <button type="button" class="btn btn-danger remove">✕</button> 
            </div>
        </div>
    `;
    document.getElementById('expeditionList').appendChild(div);
}

window.addExpeditionItem = addExpeditionItem;

const addExpeditionBtn = document.getElementById('addExpedition');
if (addExpeditionBtn) {
    const newBtn = addExpeditionBtn.cloneNode(true);
    addExpeditionBtn.parentNode.replaceChild(newBtn, addExpeditionBtn);
    newBtn.addEventListener('click', addExpeditionItem);
}

// Handle remove buttons (event delegation for dynamically added items)
document.getElementById('expeditionList')?.addEventListener('click', function(e) {
    if (e.target.classList.contains('remove')) {
        e.target.closest('.border').remove();
    }
});

document.addEventListener('DOMContentLoaded', function() {
    const expeditionCheckbox = document.getElementById('expedition_third_party');
    const accountField = document.getElementById('expedition_account_field');
    const accountInput = document.querySelector('input[name="expedition_account"]');

    if (expeditionCheckbox) {
        expeditionCheckbox.addEventListener('change', function() {
            if (this.checked) {
                accountField.style.display = 'block';
            } else {
                accountField.style.display = 'none';
                if (accountInput) {
                    accountInput.value = '';
                }
            }
        });
    }
});