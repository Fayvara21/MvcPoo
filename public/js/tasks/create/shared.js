// remove list item on click
document.addEventListener('click', function (e) {
    if (e.target.classList && e.target.classList.contains('remove')) {
        const itemToRemove = e.target.closest('.border');
        if (itemToRemove) {
            itemToRemove.remove();
        }
    }
});