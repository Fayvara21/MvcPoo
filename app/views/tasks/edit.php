<?php include __DIR__ . '/../../../public/navbar.php'; ?>

<?php
$approList = Appro::findByTaskId($task['id']) ?? [];
$retourList = Retour::findByTaskId($task['id']) ?? [];

// Shared fields
$approShared = $approList[0] ?? [];
$retourShared = $retourList[0] ?? [];

// Determine type
$selectedType = count($approList) > 0 ? 'appro' : (count($retourList) > 0 ? 'retour' : '');

function e($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}
?>

<div class="container py-4">
<form method="POST">
<div class="row g-4">

    <!-- LEFT -->
    <div class="col-10 col-lg-5">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-0 pt-4 px-4 px-xl-5">
                <h3>Edit Task #<?= e($task['id']) ?></h3>
            </div>

            <div class="card-body p-4 p-xl-5">
                <input class="form-control mb-3" name="title" value="<?= e($task['title']) ?>" required>
                <textarea class="form-control mb-3" name="desc"><?= e($task['description']) ?></textarea>
                <input type="datetime-local" class="form-control mb-3" name="dueDate"
                    value="<?= $task['due_date'] ? date('Y-m-d\TH:i', strtotime($task['due_date'])) : '' ?>">
                <select class="form-select mb-3" name="type" id="taskType">
                    <option value="">Type</option>
                    <option value="appro" <?= $selectedType === 'appro' ? 'selected' : '' ?>>Appro</option>
                    <option value="retour" <?= $selectedType === 'retour' ? 'selected' : '' ?>>Retour</option>
                </select>
                <button class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>

    <!-- RIGHT -->
    <div class="col-12 col-lg-5">

        <!-- APPRO -->
        <div id="approFields" style="display: <?= $selectedType === 'appro' ? 'block' : 'none' ?>;">
            <div class="card p-3 mb-3">
                <button type="button" id="addAppro" class="btn btn-sm btn-primary mb-2">+ Add</button>
                <div id="approList">
                    <?php foreach ($approList as $i => $a): ?>
                        <div class="appro-item border p-2 mb-2">
                            <input type="hidden" name="appro[<?= $i ?>][id]" value="<?= $a['ID'] ?>">
                            <div class="row">
                                <div class="col">
                                    <input class="form-control" name="appro[<?= $i ?>][pn]" value="<?= e($a['pn']) ?>">
                                </div>
                                <div class="col">
                                    <input class="form-control" type="number" name="appro[<?= $i ?>][nb]" value="<?= (int)$a['nb'] ?>">
                                </div>
                                <div class="col-auto">
                                    <button type="button" class="btn btn-danger remove-appro">X</button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <hr>
                <input class="form-control mb-2" name="appro_designation" value="<?= e($approShared['designation'] ?? '') ?>">
                <input class="form-control mb-2" name="appro_of" value="<?= e($approShared['of'] ?? '') ?>">
                <input class="form-control mb-2" name="appro_location" value="<?= e($approShared['location'] ?? '') ?>">
                <input class="form-control mb-2" name="appro_plane" value="<?= e($approShared['plane'] ?? '') ?>">
                <input class="form-control" name="appro_oe" value="<?= e($approShared['oe'] ?? '') ?>">
            </div>
        </div>

        <!-- RETOUR -->
        <div id="retourFields" style="display: <?= $selectedType === 'retour' ? 'block' : 'none' ?>;">
            <div class="card p-3">
                <button type="button" id="addRetour" class="btn btn-sm btn-warning mb-2">+ Add</button>
                <div id="retourList">
                    <?php foreach ($retourList as $i => $r): ?>
                        <div class="retour-item border p-2 mb-2">
                            <input type="hidden" name="retour[<?= $i ?>][id]" value="<?= $r['ID'] ?>">
                            <div class="row">
                                <div class="col">
                                    <input class="form-control" name="retour[<?= $i ?>][PN]" value="<?= e($r['PN']) ?>">
                                </div>
                                <div class="col">
                                    <input class="form-control" type="number" name="retour[<?= $i ?>][nb]" value="<?= (int)$r['nb'] ?>">
                                </div>
                                <div class="col-auto">
                                    <button type="button" class="btn btn-danger remove-retour">X</button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <hr>
                <input class="form-control mb-2" name="retour_sn" value="<?= e($retourShared['sn'] ?? '') ?>">
                <input class="form-control" name="retour_certif" value="<?= e($retourShared['certif'] ?? '') ?>">
            </div>
        </div>

    </div>

</div>
</form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const typeSelect = document.getElementById('taskType');
    const approFields = document.getElementById('approFields');
    const retourFields = document.getElementById('retourFields');

    function updateFields() {
        approFields.style.display = 'none';
        retourFields.style.display = 'none';
        if (typeSelect.value === 'appro') approFields.style.display = 'block';
        if (typeSelect.value === 'retour') retourFields.style.display = 'block';
    }

    typeSelect.addEventListener('change', updateFields);
    updateFields();

    document.getElementById('addAppro').addEventListener('click', function () {
        const container = document.getElementById('approList');
        const index = container.children.length;
        const div = document.createElement('div');
        div.className = 'appro-item border p-2 mb-2';
        div.innerHTML = `
        <div class="row">
            <div class="col"><input class="form-control" name="appro[${index}][pn]"></div>
            <div class="col"><input class="form-control" type="number" name="appro[${index}][nb]" value="1"></div>
            <div class="col-auto"><button type="button" class="btn btn-danger remove-appro">X</button></div>
        </div>`;
        container.appendChild(div);
    });

    document.getElementById('addRetour').addEventListener('click', function () {
        const container = document.getElementById('retourList');
        const index = container.children.length;
        const div = document.createElement('div');
        div.className = 'retour-item border p-2 mb-2';
        div.innerHTML = `
        <div class="row">
            <div class="col"><input class="form-control" name="retour[${index}][PN]"></div>
            <div class="col"><input class="form-control" type="number" name="retour[${index}][nb]" value="1"></div>
            <div class="col-auto"><button type="button" class="btn btn-danger remove-retour">X</button></div>
        </div>`;
        container.appendChild(div);
    });

    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-appro')) {
            e.target.closest('.appro-item').remove();
        }
        if (e.target.classList.contains('remove-retour')) {
            e.target.closest('.retour-item').remove();
        }
    });
});
</script>