<?php include __DIR__ . '/../../../public/navbar.php'; ?>

<?php
$approList = Appro::findByTaskId($task['id']) ?? [];
$retourList = Retour::findByTaskId($task['id']) ?? [];

$approShared = $approList[0] ?? [];
$retourShared = $retourList[0] ?? [];

function e($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}
?>

<div class="container py-4">
<form method="POST">
<div class="row g-4">

    <!-- LEFT: Task Info -->
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
                    <option value="appro" <?= count($approList) > 0 ? 'selected' : '' ?>>Appro</option>
                    <option value="retour" <?= count($retourList) > 0 ? 'selected' : '' ?>>Retour</option>
                </select>
                <button class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>

    <!-- RIGHT: APPRO / RETOUR -->
    <div class="col-12 col-lg-5">

        <!-- APPRO -->
        <div id="approFields" style="display: <?= count($approList) > 0 ? 'block' : 'none' ?>;">
            <div id="approList">
                <?php foreach ($approList as $i => $a): ?>
                <div class="card mb-3 appro-item border p-3 position-relative">
                    <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 remove-appro">X</button>
                    <input type="hidden" name="appro[<?= $i ?>][id]" value="<?= $a['ID'] ?>">
                    <div class="mb-2">
                        <label>PN</label>
                        <input class="form-control" name="appro[<?= $i ?>][pn]" value="<?= e($a['pn']) ?>">
                    </div>
                    <div class="mb-2">
                        <label>Quantity</label>
                        <input class="form-control" type="number" name="appro[<?= $i ?>][nb]" value="<?= (int)$a['nb'] ?>">
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <hr>
            <!-- Shared fields -->
            <input class="form-control mb-2" name="appro_designation" value="<?= e($approShared['designation'] ?? '') ?>">
            <input class="form-control mb-2" name="appro_of" value="<?= e($approShared['of'] ?? '') ?>">
            <input class="form-control mb-2" name="appro_location" value="<?= e($approShared['location'] ?? '') ?>">
            <input class="form-control mb-2" name="appro_plane" value="<?= e($approShared['plane'] ?? '') ?>">
            <input class="form-control" name="appro_oe" value="<?= e($approShared['oe'] ?? '') ?>">
        </div>

        <!-- RETOUR -->
        <div id="retourFields" style="display: <?= count($retourList) > 0 ? 'block' : 'none' ?>;">
            <div id="retourList">
                <?php foreach ($retourList as $i => $r): ?>
                <div class="card mb-3 retour-item border p-3 position-relative">
                    <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 remove-retour">X</button>
                    <input type="hidden" name="retour[<?= $i ?>][id]" value="<?= $r['ID'] ?>">
                    <div class="mb-2">
                        <label>PN</label>
                        <input class="form-control" name="retour[<?= $i ?>][PN]" value="<?= e($r['PN']) ?>">
                    </div>
                    <div class="mb-2">
                        <label>Quantity</label>
                        <input class="form-control" type="number" name="retour[<?= $i ?>][nb]" value="<?= (int)$r['nb'] ?>">
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <hr>
            <!-- Shared fields -->
            <input class="form-control mb-2" name="retour_sn" value="<?= e($retourShared['sn'] ?? '') ?>">
            <input class="form-control" name="retour_certif" value="<?= e($retourShared['certif'] ?? '') ?>">
        </div>

    </div>

</div>
</form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-appro')) {
            e.target.closest('.appro-item').remove();
        }
        if (e.target.classList.contains('remove-retour')) {
            e.target.closest('.retour-item').remove();
        }
    });
});
</script>