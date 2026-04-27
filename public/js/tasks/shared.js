// Shared remove handler for dynamically added items
document.addEventListener('click', function (e) {
    // For APPRO items (create form with class 'remove')
    if (e.target.classList && e.target.classList.contains('remove')) {
        const itemToRemove = e.target.closest('.border');
        if (itemToRemove) {
            itemToRemove.remove();
        }
    }
    
    // For APPRO edit items
    if (e.target.classList && e.target.classList.contains('remove-appro')) {
        const itemToRemove = e.target.closest('.appro-item');
        if (itemToRemove) {
            itemToRemove.remove();
        }
    }
    
    // For RETOUR edit items
    if (e.target.classList && e.target.classList.contains('remove-retour')) {
        const itemToRemove = e.target.closest('.retour-item');
        if (itemToRemove) {
            itemToRemove.remove();
        }
    }
    
    // FOR VERIF STOCK
    if (e.target.classList && e.target.classList.contains('remove-verif-stock')) {
        const itemToRemove = e.target.closest('.verif-stock-item');
        if (itemToRemove) {
            itemToRemove.remove();
        }
    }
});

// Shared function to disable/enable inputs in a container
window.setContainerDisabled = function(container, disabled) {
    if (!container) return;
    container.querySelectorAll('input, select, textarea')
        .forEach(el => el.disabled = disabled);
};

// Shared function to clear container
window.clearContainer = function(container) {
    if (container) container.innerHTML = "";
};