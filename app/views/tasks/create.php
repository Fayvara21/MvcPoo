<?php include __DIR__ . '/../../../public/navbar.php'; ?>

<div class="container py-4">

    <form method="POST">
        <div class="row g-4">

            <!-- LEFT -->
            <div class="col-10 col-lg-5">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">

                        <input class="form-control mb-3" name="title" placeholder="Titre" required>

                        <textarea class="form-control mb-3" name="desc" placeholder="Description"></textarea>

                        <input type="datetime-local" class="form-control mb-3" name="dueDate">

                        <select class="form-select mb-3" name="type" id="taskType">
                            <option value="">Type</option>
                            <option value="appro">APPRO</option>
                            <option value="retour">RETOUR</option>
                        </select>

                        <button class="btn btn-primary">Créer</button>
                    </div>
                </div>
            </div>

            <!-- RIGHT -->
            <div class="col-12 col-lg-5">

                <!-- APPRO -->
                <div id="approFields" style="display:none;">
                    <div class="card p-3 mb-3">

                        <button type="button" id="addAppro" class="btn btn-sm btn-primary mb-2">+ Add PN</button>

                        <div id="approList"></div>

                        <hr>

                        <input class="form-control mb-2" name="appro_designation" placeholder="Designation">
                        <input class="form-control mb-2" name="appro_of" placeholder="OF">
                        <input class="form-control mb-2" name="appro_location" placeholder="Location">
                        <input class="form-control mb-2" name="appro_plane" placeholder="Plane">
                        <input class="form-control" name="appro_oe" placeholder="OE">
                    </div>
                </div>

                <!-- RETOUR -->
                <div id="retourFields" style="display:none;">
                    <div class="card p-3">

                        <button type="button" id="addRetour" class="btn btn-sm btn-warning mb-2">+ Add</button>

                        <div id="retourList"></div>

                        <hr>

                        <input class="form-control mb-2" name="retour_sn" placeholder="SN">
                        <input class="form-control" name="retour_certif" placeholder="Certification">
                    </div>
                </div>

            </div>

        </div>
    </form>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const typeSelect = document.getElementById("taskType");
    const approFields = document.getElementById("approFields");
    const retourFields = document.getElementById("retourFields");

    function updateFields() {
        approFields.style.display = "none";
        retourFields.style.display = "none";

        if (typeSelect.value === "appro") {
            approFields.style.display = "block";
            if (document.getElementById('approList').children.length === 0) {
                document.getElementById('addAppro').click();
            }
        }

        if (typeSelect.value === "retour") {
            retourFields.style.display = "block";
            if (document.getElementById('retourList').children.length === 0) {
                document.getElementById('addRetour').click();
            }
        }
    }

    typeSelect.addEventListener("change", updateFields);

    // === APPRO ===
    document.getElementById('addAppro').addEventListener('click', function () {
        const container = document.getElementById('approList');
        const index = container.children.length;

        const div = document.createElement('div');
        div.className = 'border rounded p-2 mb-2';

        div.innerHTML = `
            <div class="row g-2">
                <div class="col">
                    <input class="form-control" name="appro[${index}][pn]" placeholder="PN" required>
                </div>
                <div class="col">
                    <input class="form-control" type="number" name="appro[${index}][nb]" value="1">
                </div>
                <div class="col-auto">
                    <button type="button" class="btn btn-danger remove">✕</button>
                </div>
            </div>
        `;

        container.appendChild(div);
    });

    // === RETOUR ===
    document.getElementById('addRetour').addEventListener('click', function () {
        const container = document.getElementById('retourList');
        const index = container.children.length;

        const div = document.createElement('div');
        div.className = 'border rounded p-2 mb-2';

        div.innerHTML = `
            <div class="row g-2">
                <div class="col">
                    <input class="form-control" name="retour[${index}][PN]" placeholder="PN" required>
                </div>
                <div class="col">
                    <input class="form-control" type="number" name="retour[${index}][nb]" value="1">
                </div>
                <div class="col-auto">
                    <button type="button" class="btn btn-danger remove">✕</button>
                </div>
            </div>
        `;

        container.appendChild(div);
    });

    // REMOVE
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove')) {
            e.target.closest('.border').remove();
        }
    });

});
</script>