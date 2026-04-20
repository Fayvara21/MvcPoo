const typeSelect = document.getElementById("taskType");
const approFields = document.getElementById("approFields");
const retourFields = document.getElementById("retourFields");

const approList = document.getElementById('approList');
const retourList = document.getElementById('retourList');

function setDisabled(container, disabled) {
    container.querySelectorAll('input, select, textarea')
        .forEach(el => el.disabled = disabled);
}

function clearContainer(container) {
    container.innerHTML = "";
}

function toggleFields() {
    approFields.style.display = "none";
    retourFields.style.display = "none";

    setDisabled(approFields, true);
    setDisabled(retourFields, true);

    if (typeSelect.value === "appro") {
        clearContainer(retourList);

        approFields.style.display = "block";
        setDisabled(approFields, false);

        if (approList.children.length === 0) {
            addApproItem();
        }
    }

    if (typeSelect.value === "retour") {
        clearContainer(approList);

        retourFields.style.display = "block";
        setDisabled(retourFields, false);

        if (retourList.children.length === 0) {
            addRetourItem();
        }
    }
}

typeSelect.addEventListener("change", toggleFields);
toggleFields();