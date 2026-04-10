<?php include __DIR__ . '/../../../public/navbar.php'; ?>

<div class="container py-4">

    <!-- Header -->
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

                    <div class="card-header bg-white border-0 pt-4 px-4 px-xl-5">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-primary bg-opacity-10 p-3 rounded-3">
                                <i class="bi bi-plus-circle text-primary fs-4"></i>
                            </div>
                            <div>
                                <h1 class="h3 fw-bold mb-1">Ajouter une tâche</h1>
                                <p class="text-muted mb-0">Créez une nouvelle demande</p>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-4 p-xl-5">

                        <h5 class="fw-semibold mb-3">Informations générales</h5>

                        <div class="mb-3">
                            <label class="form-label fw-medium">
                                BP (titre, référence): <span class="text-danger">*</span>
                            </label>
                            <input class="form-control form-control-lg" type="text" name="title" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-medium">Description :</label>
                            <textarea class="form-control" name="desc" rows="3"></textarea>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6 col-lg-8">
                                <label class="form-label fw-medium">Date limite</label>
                                <input type="datetime-local" class="form-control" name="dueDate"
                                    min="<?= date('Y-m-d\TH:i') ?>">
                            </div>

                            <div class="col-md-6 col-lg-4">
                                <label class="form-label fw-medium">Type de tâche</label>
                                <select class="form-select" name="type" id="taskType">
                                    <option value="">-- Choisir --</option>
                                    <option value="appro">APPRO</option>
                                    <option value="retour">RETOUR</option>
                                </select>
                            </div>
                        </div>

                        <div class="d-flex gap-2 justify-content-end border-top pt-4 mt-4">
                            <a href="javascript:history.back()" class="btn btn-outline-secondary px-4">
                                <i class="bi bi-x-lg me-2"></i>Annuler
                            </a>
                            <button type="submit" class="btn btn-primary px-5">
                                <i class="bi bi-check-lg me-2"></i>Ajouter
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT -->
            <div class="col-12 col-lg-5">

                <!-- APPRO -->
                <div id="approFields" class="card shadow-sm border-0 mb-3" style="display:none;">
                    <div class="card-body">

                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="badge bg-primary">APPRO</span>
                            <h5 class="fw-semibold mb-0">Informations APPRO</h5>
                        </div>

                        <!-- MULTI ROW -->
                        <button type="button" id="addAppro" class="btn btn-sm btn-primary mb-3">
                            + Ajouter PN
                        </button>
                        <div id="approList">
                            <div class="border rounded p-2 mb-2">
                                <div class="row g-2">
                                    <div class="col">
                                        <input class="form-control" name="appro[0][pn]" placeholder="PN" required>
                                    </div>
                                    <div class="col">
                                        <input class="form-control" name="appro[0][location]" placeholder="Emplacement" required>
                                    </div>
                                    <div class="col">
                                        <input class="form-control" type="number" name="appro[0][nb]" value="1">
                                    </div>
                                    <div class="col-auto">
                                        <button type="button" class="btn btn-danger remove">✕</button>
                                    </div>
                                </div>
                            </div>
                        </div>



                        <hr>

                        <!-- SHARED APPRO FIELDS -->
                        <div class="mb-1">
                            <label class="form-label fw-medium">Désignation :</label>
                            <input class="form-control" name="appro_designation" placeholder="Designation">
                        </div>
                        <div class="mb-1">
                            <label class="form-label fw-medium">OF <span class="text-danger">*</span> :</label>
                            <input class="form-control" name="appro_of" placeholder="OF" required>
                        </div>

                        <!-- <div class="mb-1">
                            <label class="form-label fw-medium">Emplacement :</label>
                            <input class="form-control" name="appro_location" placeholder="Emplacement">
                        </div> -->

                        <div class="mb-1>
                            <label class=" form-label fw-medium">Avion :</label>
                            <input class="form-control" name="appro_plane" placeholder="Avion">
                        </div>

                        <div class="mb-" 1>
                            <label class="form-label fw-medium">OE :</label>
                            <input class="form-control" name="appro_oe" placeholder="OE">
                        </div>

                    </div>
                </div>

                <!-- RETOUR -->
                <div id="retourFields" class="card shadow-sm border-0" style="display:none;">
                    <div class="card-body">

                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="badge bg-warning text-dark">RETOUR</span>
                            <h5 class="fw-semibold mb-0">Informations RETOUR</h5>
                        </div>
                        <button type="button" id="addRetour" class="btn btn-sm btn-warning mb-3">
                            + Ajouter retour
                        </button>

                        <!-- MULTI ROW -->
                        <div id="retourList">
                            <div class="border rounded p-2 mb-2">
                                <div class="row g-2">
                                    <div class="col">
                                        <input class="form-control" name="retour[0][pn]" placeholder="PN" required>
                                    </div>
                                    <div class="col">
                                        <input class="form-control" type="number" name="retour[0][nb]" value="1">
                                    </div>
                                    <div class="col-auto">
                                        <button type="button" class="btn btn-danger remove">✕</button>
                                    </div>
                                </div>
                            </div>
                        </div>



                        <hr>
                        <!-- SHARED RETOUR FIELDS -->
                        <div class="mb-1">
                            <label class="form-label fw-medium">SN :</label>
                            <input class="form-control" name="retour_sn" placeholder="SN">
                        </div>

                        <div class="mb-1">
                            <label class="form-label fw-medium">Certification :</label>
                            <input class="form-control" name="retour_certif" placeholder="Certification">
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

    const approList = document.getElementById('approList');
    const retourList = document.getElementById('retourList');

    function clearContainer(container) {
        container.innerHTML = "";
    }

    function toggleFields() {
        // Hide both
        approFields.style.display = "none";
        retourFields.style.display = "none";

        if (typeSelect.value === "appro") {
            // Clear retour
            clearContainer(retourList);

            // Show appro
            approFields.style.display = "block";

            // Add one entry if empty
            if (approList.children.length === 0) {
                addApproItem();
            }
        }

        if (typeSelect.value === "retour") {
            // Clear appro
            clearContainer(approList);

            // Show retour
            retourFields.style.display = "block";

            // Add one entry if empty
            if (retourList.children.length === 0) {
                addRetourItem();
            }
        }
    }

    typeSelect.addEventListener("change", toggleFields);
    toggleFields(); // run on load

    // === APPRO REPEATER ===
    function addApproItem() {
        const index = Date.now(); // unique index

        const div = document.createElement('div');
        div.className = 'border rounded p-2 mb-2';

        div.innerHTML = `
        <div class="row g-2">
            <div class="col">
                <input class="form-control" name="appro[${index}][pn]" placeholder="PN" required>
            </div>
            <div class="col">
                <input class="form-control" name="appro[${index}][of]" placeholder="OF" required>
            </div>
            <div class="col">
                <input class="form-control" type="number" name="appro[${index}][nb]" value="1">
            </div>
            <div class="col-auto">
                <button type="button" class="btn btn-danger remove">✕</button>
            </div>
        </div>
        `;

        approList.appendChild(div);
    }

    document.getElementById('addAppro').addEventListener('click', addApproItem);

    // === RETOUR REPEATER ===
    function addRetourItem() {
        const index = Date.now(); // unique index

        const div = document.createElement('div');
        div.className = 'border rounded p-2 mb-2';

        div.innerHTML = `
        <div class="row g-2">
            <div class="col">
                <input class="form-control" name="retour[${index}][pn]" placeholder="PN" required>
            </div>
            <div class="col">
                <input class="form-control" type="number" name="retour[${index}][nb]" value="1">
            </div>
            <div class="col-auto">
                <button type="button" class="btn btn-danger remove">✕</button>
            </div>
        </div>
        `;

        retourList.appendChild(div);
    }

    document.getElementById('addRetour').addEventListener('click', addRetourItem);

    // === REMOVE BUTTON ===
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove')) {
            e.target.closest('.border').remove();
        }
    });
</script>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">