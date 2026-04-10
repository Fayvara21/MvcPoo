<?php include __DIR__ . '/../../../public/navbar.php'; ?>

<div class="container py-4">

    <div class="px-lg-4">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/projects" class="text-decoration-none">Projets</a></li>
                <li class="breadcrumb-item"><a href="/projects/<?= $_GET['project_id'] ?? '' ?>"
                        class="text-decoration-none">Projet</a></li>
                <li class="breadcrumb-item active">Nouvelle tâche</li>
            </ol>
        </nav>
    </div>

    <form method="POST">

        <div class="row g-4">

            <!-- LEFT -->
            <div class="col-10 col-lg-5">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">

                        <input class="form-control mb-3" type="text" name="title" required placeholder="Titre">
                        <textarea class="form-control mb-3" name="desc"></textarea>

                        <select class="form-select mb-3" name="type" id="taskType">
                            <option value="">-- Choisir --</option>
                            <option value="appro">APPRO</option>
                            <option value="retour">RETOUR</option>
                        </select>

                        <button class="btn btn-primary">Ajouter</button>
                    </div>
                </div>
            </div>

            <!-- RIGHT -->
            <div class="col-12 col-lg-4">

                <!-- APPRO -->
                <div id="approFields" style="display:none;">
                    <button type="button" id="addAppro">+ Ajouter PN</button>

                    <div id="approList">
                        <div class="border p-2 mb-2">
                            <input class="form-control" name="appro[0][pn]" placeholder="PN" required>
                            <input class="form-control" name="appro[0][of]" placeholder="OF" required>
                        </div>
                    </div>
                </div>

                <!-- RETOUR -->
                <div id="retourFields" style="display:none;">
                    <button type="button" id="addRetour">+ Ajouter retour</button>

                    <div id="retourList">
                        <div class="border p-2 mb-2">
                            <input class="form-control" name="retour[0][pn]" placeholder="PN" required>
                            <input class="form-control" type="number" name="retour[0][nb]" value="1">
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </form>
</div>

<script>
const typeSelect = document.getElementById("taskType");
const approFields = document.getElementById("approFields");
const retourFields = document.getElementById("retourFields");

function toggleFields(type) {
    // Hide all
    approFields.style.display = "none";
    retourFields.style.display = "none";

    // Disable all inputs
    document.querySelectorAll('#approFields input, #retourFields input').forEach(input => {
        input.disabled = true;
        input.required = false;
    });

    if (type === "appro") {
        approFields.style.display = "block";
        document.querySelectorAll('#approFields input').forEach(input => {
            input.disabled = false;
            if (input.name.includes('[pn]') || input.name.includes('[of]')) {
                input.required = true;
            }
        });
    }

    if (type === "retour") {
        retourFields.style.display = "block";
        document.querySelectorAll('#retourFields input').forEach(input => {
            input.disabled = false;
            if (input.name.includes('[pn]')) {
                input.required = true;
            }
        });
    }
}

typeSelect.addEventListener("change", function () {
    toggleFields(this.value);
});

// === APPRO REPEATER ===
document.getElementById('addAppro').addEventListener('click', function () {
    const container = document.getElementById('approList');
    const index = container.children.length;

    const div = document.createElement('div');
    div.className = 'border p-2 mb-2';

    div.innerHTML = `
        <input class="form-control" name="appro[${index}][pn]" placeholder="PN" required>
        <input class="form-control" name="appro[${index}][of]" placeholder="OF" required>
    `;

    container.appendChild(div);
});

// === RETOUR REPEATER ===
document.getElementById('addRetour').addEventListener('click', function () {
    const container = document.getElementById('retourList');
    const index = container.children.length;

    const div = document.createElement('div');
    div.className = 'border p-2 mb-2';

    div.innerHTML = `
        <input class="form-control" name="retour[${index}][pn]" placeholder="PN" required>
        <input class="form-control" type="number" name="retour[${index}][nb]" value="1">
    `;

    container.appendChild(div);
});
</script>