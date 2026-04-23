document.addEventListener("DOMContentLoaded", function () {

    const typeSelect = document.getElementById("taskType");
    const approFields = document.getElementById("approFields");
    const retourFields = document.getElementById("retourFields");

    const approList = document.getElementById('approList');
    const retourList = document.getElementById('retourList');

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
            if (approList && approList.children.length === 0 && window.addApproItem) {
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
            if (retourList && retourList.children.length === 0 && window.addRetourItem) {
                window.addRetourItem();
            }
        }
    }

    if (typeSelect) {
        typeSelect.addEventListener("change", toggleFields);
    }

    toggleFields();

});