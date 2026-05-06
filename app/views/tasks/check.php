<?php include __DIR__ . '/../../../public/navbar.php'; ?>

<?php
// Fetch APPRO and RETOUR data for this task
$appro = Appro::findByTaskId($task['id']);
$retour = Retour::findByTaskId($task['id']);

// Determine the task type based on which related data exists
$selectedType = '';
if ($appro) {
    $selectedType = 'appro';
} elseif ($retour) {
    $selectedType = 'retour';
}
?>

<div class="container py-4 main-content">

    <!-- Header -->
    <div class="px-lg-4">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/projects" class="text-decoration-none">Projets</a></li>
                <li class="breadcrumb-item">
                    <a href="/projects/<?= $task['project_id'] ?>" class="text-decoration-none">Projet</a>
                </li>
                <li class="breadcrumb-item active">Editer une tâche</li>
            </ol>
        </nav>
    </div>

    <form method="POST">

        <div class="row g-4">

            <!-- LEFT: MAIN FORM -->
            <div class="col-10 col-lg-5">

                <div class="card shadow-sm border-1">

                    <div class="card-header bg-white border-1 pt-4 px-4 px-xl-5">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-primary bg-opacity-10 p-3 rounded-3">
                                <i class="bi bi-pencil-square text-primary fs-4"></i>
                            </div>
                            <div>
                                <h1 class="h3 fw-bold mb-1">Editer une tâche</h1>
                                <p class="text-muted mb-0">Modifiez la tâche courante</p>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-4 p-xl-5">

                        <h5 class="fw-semibold mb-3">Informations générales</h5>

                        <div class="mb-3">
                            <label class="form-label fw-medium">
                                BP (titre, référence): <span class="text-danger">*</span>
                            </label>
                            <input class="form-control form-control-lg"
                                   type="text"
                                   name="title"
                                   value="<?= htmlspecialchars($task['title']) ?>"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-medium">Description :</label>
                            <textarea class="form-control"
                                      name="desc"
                                      rows="3"><?= htmlspecialchars($task['description']) ?></textarea>
                        </div>

                        <div class="row g-3">

                            <div class="col-md-6 col-lg-8">
                                <label class="form-label fw-medium">Date limite</label>
                                <input type="datetime-local"
                                       class="form-control"
                                       name="dueDate"
                                       value="<?= $task['due_date'] ? date('Y-m-d\TH:i', strtotime($task['due_date'])) : '' ?>">
                            </div>

                            <div class="col-md-6 col-lg-4">
                                <label class="form-label fw-medium">Type de tâche</label>
                                <select class="form-select" name="type" id="taskType" required>
                                    <option value="">Sélectionner un type</option>
                                    <option value="appro" <?= $selectedType === 'appro' ? 'selected' : '' ?>>Appro</option>
                                    <option value="retour" <?= $selectedType === 'retour' ? 'selected' : '' ?>>Retour</option>
                                </select>
                            </div>

                        </div>

                        <div class="d-flex gap-2 justify-content-end border-top pt-4 mt-4">
                            <a href="javascript:history.back()" class="btn btn-outline-secondary px-4">
                                <i class="bi bi-x-lg me-2"></i>Annuler
                            </a>

                            <button type="submit" class="btn btn-primary px-5">
                                <i class="bi bi-check-lg me-2"></i>Enregistrer
                            </button>
                        </div>

                    </div>
                </div>
            </div>

            <!-- RIGHT PANEL -->
            <div class="col-12 col-lg-4">

                <!-- APPRO FIELDS -->
                <div id="approFields" style="display: <?= $selectedType === 'appro' ? 'block' : 'none' ?>;">
                    <div class="card shadow-sm border-1 mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <span class="badge bg-primary">APPRO</span>
                                <h5 class="fw-semibold mb-0">Informations APPRO</h5>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">PN</label>
                                    <input class="form-control" 
                                           name="appro_pn"
                                           value="<?= htmlspecialchars($appro['pn'] ?? '') ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Nombre</label>
                                    <input class="form-control" 
                                           type="number" 
                                           name="appro_nb"
                                           value="<?= htmlspecialchars($appro['nb'] ?? '1') ?>">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Designation</label>
                                    <input class="form-control" 
                                           name="appro_designation"
                                           value="<?= htmlspecialchars($appro['designation'] ?? '') ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">OF</label>
                                    <input class="form-control" 
                                           name="appro_of"
                                           value="<?= htmlspecialchars($appro['of'] ?? '') ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Emplacement</label>
                                    <input class="form-control" 
                                           name="appro_location"
                                           value="<?= htmlspecialchars($appro['location'] ?? '') ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Avion</label>
                                    <input class="form-control" 
                                           name="appro_plane"
                                           value="<?= htmlspecialchars($appro['plane'] ?? '') ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">OE</label>
                                    <input class="form-control" 
                                           name="appro_oe"
                                           value="<?= htmlspecialchars($appro['oe'] ?? '') ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RETOUR FIELDS -->
                <div id="retourFields" style="display: <?= $selectedType === 'retour' ? 'block' : 'none' ?>;">
                    <div class="card shadow-sm border-1">
                        <div class="card-body">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <span class="badge bg-warning text-dark">RETOUR</span>
                                <h5 class="fw-semibold mb-0">Informations RETOUR</h5>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">PN</label>
                                    <input class="form-control" 
                                           name="retour_pn"
                                           value="<?= htmlspecialchars($retour['PN'] ?? '') ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Nombre</label>
                                    <input class="form-control" 
                                           type="number" 
                                           name="retour_nb"
                                           value="<?= htmlspecialchars($retour['nb'] ?? '1') ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">SN</label>
                                    <input class="form-control" 
                                           name="retour_sn"
                                           value="<?= htmlspecialchars($retour['sn'] ?? '') ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Certification</label>
                                    <input class="form-control" 
                                           name="retour_certif"
                                           value="<?= htmlspecialchars($retour['certif'] ?? '') ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </form>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('taskType');
    const approFields = document.getElementById('approFields');
    const retourFields = document.getElementById('retourFields');

    function updateFields() {
        // Hide both sections first
        approFields.style.display = 'none';
        retourFields.style.display = 'none';

        // Show the selected section
        if (typeSelect.value === 'appro') {
            approFields.style.display = 'block';
        } else if (typeSelect.value === 'retour') {
            retourFields.style.display = 'block';
        }
    }

    // Update fields when selection changes
    typeSelect.addEventListener('change', updateFields);
    
    // Initial update based on pre-selected value
    updateFields();
});
</script>

<link rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">