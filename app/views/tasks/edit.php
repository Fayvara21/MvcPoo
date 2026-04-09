<?php include __DIR__ . '/../../../public/navbar.php'; ?>

<?php
$approList = Appro::findByTaskId($task['id']) ?? [[]];
$retourList = Retour::findByTaskId($task['id']) ?? [[]];

// Determine task type
$selectedType = !empty($approList) ? 'appro' : (!empty($retourList) ? 'retour' : '');

// Safe escaping
function e($str) { return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8'); }
?>

<div class="container py-4">
<form method="POST">
<div class="row g-4">

    <!-- LEFT: MAIN FORM -->
    <div class="col-10 col-lg-5">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-0 pt-4 px-4 px-xl-5">
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
                    <label class="form-label fw-medium">BP (titre, référence): <span class="text-danger">*</span></label>
                    <input class="form-control form-control-lg" type="text" name="title" value="<?= e($task['title']) ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-medium">Description :</label>
                    <textarea class="form-control" name="desc" rows="3"><?= e($task['description']) ?></textarea>
                </div>

                <div class="row g-3">
                    <div class="col-md-6 col-lg-8">
                        <label class="form-label fw-medium">Date limite</label>
                        <input type="datetime-local" class="form-control" name="dueDate" value="<?= $task['due_date'] ? date('Y-m-d\TH:i', strtotime($task['due_date'])) : '' ?>">
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
                    <a href="javascript:history.back()" class="btn btn-outline-secondary px-4"><i class="bi bi-x-lg me-2"></i>Annuler</a>
                    <button type="submit" class="btn btn-primary px-5"><i class="bi bi-check-lg me-2"></i>Enregistrer</button>
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT PANEL -->
    <div class="col-12 col-lg-4">

        <!-- APPRO FIELDS -->
        <div id="approFields" style="display: <?= $selectedType === 'appro' ? 'block' : 'none' ?>;">
            <div class="card shadow-sm border-0 mb-3 p-3">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="badge bg-primary">APPRO</span>
                    <h5 class="fw-semibold mb-0">Informations APPRO</h5>
                </div>

                <button type="button" id="addAppro" class="btn btn-sm btn-primary mb-3">+ Ajouter PN</button>

                <!-- REPEATER for APPRO rows -->
                <div id="approList">
                    <?php foreach ($approList as $i => $a): ?>
                        <div class="appro-item border rounded p-2 mb-2">
                            <div class="row g-2 align-items-center">
                                <div class="col"><input class="form-control" name="appro[<?= $i ?>][pn]" placeholder="PN" value="<?= e($a['pn'] ?? '') ?>" required></div>
                                <div class="col"><input class="form-control" type="number" name="appro[<?= $i ?>][nb]" value="<?= (int)($a['nb'] ?? 1) ?>"></div>
                                <div class="col"><input class="form-control" name="appro[<?= $i ?>][designation]" placeholder="Désignation" value="<?= e($a['designation'] ?? '') ?>"></div>
                                <div class="col"><input class="form-control" name="appro[<?= $i ?>][of]" placeholder="OF" value="<?= e($a['of'] ?? '') ?>"></div>
                                <div class="col"><input class="form-control" name="appro[<?= $i ?>][location]" placeholder="Emplacement" value="<?= e($a['location'] ?? '') ?>"></div>
                                <div class="col"><input class="form-control" name="appro[<?= $i ?>][plane]" placeholder="Avion" value="<?= e($a['plane'] ?? '') ?>"></div>
                                <div class="col"><input class="form-control" name="appro[<?= $i ?>][oe]" placeholder="OE" value="<?= e($a['oe'] ?? '') ?>"></div>
                                <div class="col-auto"><button type="button" class="btn btn-danger remove-appro">✕</button></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- RETOUR FIELDS -->
        <div id="retourFields" style="display: <?= $selectedType === 'retour' ? 'block' : 'none' ?>;">
            <div class="card shadow-sm border-0 p-3">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="badge bg-warning text-dark">RETOUR</span>
                    <h5 class="fw-semibold mb-0">Informations RETOUR</h5>
                </div>

                <button type="button" id="addRetour" class="btn btn-sm btn-warning mb-3">+ Ajouter retour</button>

                <!-- REPEATER for RETOUR rows -->
                <div id="retourList">
                    <?php foreach ($retourList as $i => $r): ?>
                        <div class="retour-item border rounded p-2 mb-2">
                            <div class="row g-2 align-items-center">
                                <div class="col"><input class="form-control" name="retour[<?= $i ?>][pn]" placeholder="PN" value="<?= e($r['pn'] ?? '') ?>"></div>
                                <div class="col"><input class="form-control" type="number" name="retour[<?= $i ?>][nb]" value="<?= (int)($r['nb'] ?? 1) ?>"></div>
                                <div class="col"><input class="form-control" name="retour[<?= $i ?>][sn]" placeholder="SN" value="<?= e($r['sn'] ?? '') ?>"></div>
                                <div class="col"><input class="form-control" name="retour[<?= $i ?>][certif]" placeholder="Certification" value="<?= e($r['certif'] ?? '') ?>"></div>
                                <div class="col-auto"><button type="button" class="btn btn-danger remove-retour">✕</button></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
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
        approFields.style.display = 'none';
        retourFields.style.display = 'none';
        approFields.querySelectorAll('input').forEach(i => i.disabled = true);
        retourFields.querySelectorAll('input').forEach(i => i.disabled = true);

        if (typeSelect.value === 'appro') {
            approFields.style.display = 'block';
            approFields.querySelectorAll('input').forEach(i => i.disabled = false);
        }

        if (typeSelect.value === 'retour') {
            retourFields.style.display = 'block';
            retourFields.querySelectorAll('input').forEach(i => i.disabled = false);
        }
    }

    typeSelect.addEventListener('change', updateFields);
    updateFields();

    // APPRO repeater
    document.getElementById('addAppro').addEventListener('click', function () {
        const container = document.getElementById('approList');
        const index = container.children.length;
        const div = document.createElement('div');
        div.className = 'appro-item border rounded p-2 mb-2';
        div.innerHTML = `
            <div class="row g-2 align-items-center">
                <div class="col"><input class="form-control" name="appro[${index}][pn]" placeholder="PN"></div>
                <div class="col"><input class="form-control" type="number" name="appro[${index}][nb]" value="1"></div>
                <div class="col"><input class="form-control" name="appro[${index}][designation]" placeholder="Désignation"></div>
                <div class="col"><input class="form-control" name="appro[${index}][of]" placeholder="OF"></div>
                <div class="col"><input class="form-control" name="appro[${index}][location]" placeholder="Emplacement"></div>
                <div class="col"><input class="form-control" name="appro[${index}][plane]" placeholder="Avion"></div>
                <div class="col"><input class="form-control" name="appro[${index}][oe]" placeholder="OE"></div>
                <div class="col-auto"><button type="button" class="btn btn-danger remove-appro">✕</button></div>
            </div>`;
        container.appendChild(div);
    });

    // RETOUR repeater
    document.getElementById('addRetour').addEventListener('click', function () {
        const container = document.getElementById('retourList');
        const index = container.children.length;
        const div = document.createElement('div');
        div.className = 'retour-item border rounded p-2 mb-2';
        div.innerHTML = `
            <div class="row g-2 align-items-center">
                <div class="col"><input class="form-control" name="retour[${index}][pn]" placeholder="PN"></div>
                <div class="col"><input class="form-control" type="number" name="retour[${index}][nb]" value="1"></div>
                <div class="col"><input class="form-control" name="retour[${index}][sn]" placeholder="SN"></div>
                <div class="col"><input class="form-control" name="retour[${index}][certif]" placeholder="Certification"></div>
                <div class="col-auto"><button type="button" class="btn btn-danger remove-retour">✕</button></div>
            </div>`;
        container.appendChild(div);
    });

    // Remove buttons
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-appro')) e.target.closest('.appro-item').remove();
        if (e.target.classList.contains('remove-retour')) e.target.closest('.retour-item').remove();
    });
});
</script>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">