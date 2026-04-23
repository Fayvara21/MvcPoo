// Shared remove handler for dynamically added items
document.addEventListener('click', function (e) {
    if (e.target.classList && e.target.classList.contains('remove')) {
        const itemToRemove = e.target.closest('.border');
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