document.addEventListener("DOMContentLoaded", function () {
    const typeSelect = document.getElementById("taskType");
    const approFields = document.getElementById("approFields");
    const retourFields = document.getElementById("retourFields");
    const verifStockFields = document.getElementById("verifStockFields");
    const thirdPartyFields = document.getElementById("thirdPartyFields");
    
    const approList = document.getElementById('approList');
    const retourList = document.getElementById('retourList');
    const verifStockList = document.getElementById('verifStockList');
    const thirdPartyList = document.getElementById('thirdPartyList');

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
        if (verifStockFields) verifStockFields.style.display = "none";
        if (thirdPartyFields) thirdPartyFields.style.display = "none";

        setDisabled(approFields, true);
        setDisabled(retourFields, true);
        setDisabled(verifStockFields, true);
        setDisabled(thirdPartyFields, true);

        if (typeSelect && typeSelect.value === "appro") {
            clearContainer(retourList);
            clearContainer(verifStockList);
            clearContainer(thirdPartyList);
            
            if (approFields) {
                approFields.style.display = "block";
                setDisabled(approFields, false);
            }
            
            if (approList && approList.children.length === 0 && window.addApproItem) {
                window.addApproItem();
            }
        }

        if (typeSelect && typeSelect.value === "retour") {
            clearContainer(approList);
            clearContainer(verifStockList);
            clearContainer(thirdPartyList);
            
            if (retourFields) {
                retourFields.style.display = "block";
                setDisabled(retourFields, false);
            }
            
            if (retourList && retourList.children.length === 0 && window.addRetourItem) {
                window.addRetourItem();
            }
        }

        if (typeSelect && typeSelect.value === "verif_stock") {
            clearContainer(approList);
            clearContainer(retourList);
            clearContainer(thirdPartyList);
            
            if (verifStockFields) {
                verifStockFields.style.display = "block";
                setDisabled(verifStockFields, false);
            }
            
            if (verifStockList && verifStockList.children.length === 0 && window.addVerifStockItem) {
                window.addVerifStockItem();
            }
        }

        if (typeSelect && typeSelect.value === "third_party") {
            clearContainer(approList);
            clearContainer(retourList);
            clearContainer(verifStockList);
            
            if (thirdPartyFields) {
                thirdPartyFields.style.display = "block";
                setDisabled(thirdPartyFields, false);
            }
            
            if (thirdPartyList && thirdPartyList.children.length === 0 && window.addThirdPartyItem) {
                window.addThirdPartyItem();
            }
        }
    }

    if (typeSelect) {
        typeSelect.addEventListener("change", toggleFields);
    }
    
    toggleFields();
});