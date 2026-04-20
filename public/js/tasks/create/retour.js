function addRetourItem() {
    const index = Date.now();

    const div = document.createElement('div');
    div.className = 'border rounded p-2 mb-2';

    div.innerHTML = `
        <div class="row g-2">
            <div class="col">
                <input class="form-control" name="retour[${index}][pn]" required>
            </div>
            <div class="col">
                <input class="form-control" type="number" name="retour[${index}][nb]" value="1">
            </div>
            <div class="col-auto">
                <button type="button" class="btn btn-danger remove">✕</button>
            </div>
        </div>
    `;

    document.getElementById('retourList').appendChild(div);
}

document.getElementById('addRetour')
    ?.addEventListener('click', addRetourItem);

// shared remove
document.addEventListener('click', function (e) {
    if (e.target.classList.contains('remove')) {
        e.target.closest('.border').remove();
    }
});