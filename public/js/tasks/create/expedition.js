// expedition.js
function addExpeditionItem() {
    const container = document.getElementById('expeditionList');
    if (!container) return;
    
    const index = Date.now();
    const div = document.createElement('div');
    div.className = 'expedition-item border rounded p-2 mb-2';
    div.innerHTML = `
        <div class="row g-2">
            <div class="col"> 
                <input class="form-control" name="expedition[${index}][pn]" placeholder="PN">
            </div>
            <div class="col"> 
                <input class="form-control" name="expedition[${index}][name]" placeholder="Libellé">
            </div>
            <div class="col"> 
                <input class="form-control" type="number" name="expedition[${index}][nb]" value="1" placeholder="Quantité">
            </div>
            <div class="col-auto"> 
                <button type="button" class="btn btn-danger btn-sm remove-expedition-item">✕</button> 
            </div>
        </div>
    `;
    container.appendChild(div);
}

window.addExpeditionItem = addExpeditionItem;

// Handle remove buttons (event delegation for dynamically added items)
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-expedition-item')) {
        const item = e.target.closest('.expedition-item');
        if (item) {
            item.remove();
        }
    }
});

// Initialize add button
document.addEventListener('DOMContentLoaded', function() {
    const addExpeditionBtn = document.getElementById('addExpedition');
    if (addExpeditionBtn) {
        // Remove any existing listeners by cloning
        const newBtn = addExpeditionBtn.cloneNode(true);
        addExpeditionBtn.parentNode.replaceChild(newBtn, addExpeditionBtn);
        newBtn.addEventListener('click', addExpeditionItem);
    }
    
    // Handle the shared Compte de transport tiers checkbox in expedition fields
    const expeditionCompteTrigger = document.getElementById('expedition_compte_transport');
    const expeditionCompteField = document.querySelector('.expedition-compte-field');
    
    if (expeditionCompteTrigger && expeditionCompteField) {
        expeditionCompteTrigger.addEventListener('change', function() {
            expeditionCompteField.style.display = this.checked ? 'block' : 'none';
            if (!this.checked) {
                const compteInput = expeditionCompteField.querySelector('input');
                if (compteInput) compteInput.value = '';
            }
        });
        
        // Set initial state
        expeditionCompteField.style.display = expeditionCompteTrigger.checked ? 'block' : 'none';
    }
});