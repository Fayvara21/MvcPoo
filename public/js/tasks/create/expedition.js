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
    // Handle existing compte transport checkboxes
    const compteTriggers = document.querySelectorAll('.compte-trigger');
    
    compteTriggers.forEach(trigger => {
        const compteField = trigger.closest('.expedition-item').querySelector('.compte-field');
        
        // Set initial state
        if (compteField) {
            compteField.style.display = trigger.checked ? 'block' : 'none';
        }
        
        // Add change event
        trigger.addEventListener('change', function() {
            if (compteField) {
                compteField.style.display = this.checked ? 'block' : 'none';
                if (!this.checked) {
                    const compteInput = compteField.querySelector('input');
                    if (compteInput) compteInput.value = '';
                }
            }
        });
    });
});