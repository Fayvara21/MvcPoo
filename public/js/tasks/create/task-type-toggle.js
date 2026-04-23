document.addEventListener("DOMContentLoaded", function () {

    const typeSelect = document.getElementById("taskType");
    const approFields = document.getElementById("approFields");
    const retourFields = document.getElementById("retourFields");

    const approList = document.getElementById('approList');
    const retourList = document.getElementById('retourList');

    // Define the add functions globally so they can be called by buttons
    window.addApproItem = function() {
        const timestamp = Date.now();
        const div = document.createElement('div');
        div.className = 'border rounded p-2 mb-2';
        div.innerHTML = `
            <div class="row g-2">
                <div class="col">
                    <input class="form-control" name="appro[${timestamp}][pn]" required>
                </div>
                <div class="col">
                    <input class="form-control" name="appro[${timestamp}][location]">
                </div>
                <div class="col">
                    <input class="form-control" type="number" name="appro[${timestamp}][nb]" value="1">
                </div>
                <div class="col-auto">
                    <button type="button" class="btn btn-danger remove" onclick="this.closest('.border').remove()">✕</button>
                </div>
            </div>
        `;
        approList.appendChild(div);
    };

    window.addRetourItem = function() {
        const timestamp = Date.now();
        const div = document.createElement('div');
        div.className = 'border rounded p-2 mb-2';
        div.innerHTML = `
            <div class="row g-2">
                <div class="col">
                    <input class="form-control" name="retour[${timestamp}][pn]" required>
                </div>
                <div class="col">
                    <input class="form-control" name="retour[${timestamp}][location]">
                </div>
                <div class="col">
                    <input class="form-control" type="number" name="retour[${timestamp}][nb]" value="1">
                </div>
                <div class="col-auto">
                    <button type="button" class="btn btn-danger remove" onclick="this.closest('.border').remove()">✕</button>
                </div>
            </div>
        `;
        retourList.appendChild(div);
    };

    function setDisabled(container, disabled) {
        if (!container) return;
        container.querySelectorAll('input, select, textarea')
            .forEach(el => el.disabled = disabled);
    }

    function clearContainer(container) {
        if (container) container.innerHTML = "";
    }

    function toggleFields() {
        if (approFields) approFields.style.display = "none";
        if (retourFields) retourFields.style.display = "none";

        setDisabled(approFields, true);
        setDisabled(retourFields, true);

        if (typeSelect && typeSelect.value === "appro") {
            clearContainer(retourList);

            if (approFields) {
                approFields.style.display = "block";
                setDisabled(approFields, false);
            }

            // Add default item if list is empty
            if (approList && approList.children.length === 0) {
                window.addApproItem();
            }
        }

        if (typeSelect && typeSelect.value === "retour") {
            clearContainer(approList);

            if (retourFields) {
                retourFields.style.display = "block";
                setDisabled(retourFields, false);
            }

            // Add default item if list is empty
            if (retourList && retourList.children.length === 0) {
                window.addRetourItem();
            }
        }
    }

    if (typeSelect) {
        typeSelect.addEventListener("change", toggleFields);
    }
    
    toggleFields();

});