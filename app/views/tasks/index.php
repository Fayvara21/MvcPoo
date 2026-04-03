<?php
session_start();

// Helper
function e($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

$currentUserGroup = $_SESSION['group'] ?? '';

// State counts
$stateCounts = [0 => 0, 1 => 0, 2 => 0, 3 => 0];
foreach ($tasks as $t) {
    $state = (int)($t['is_completed'] ?? 0);
    if (isset($stateCounts[$state])) $stateCounts[$state]++;
}
$totalTasks = array_sum($stateCounts);

// Filters
$activeStates = $_GET['states'] ?? [];
if (!is_array($activeStates)) $activeStates = [$activeStates];
$activeStates = array_map('intval', $activeStates);

// Filter
$tasks = array_filter($tasks, function($task) use ($activeStates) {
    return empty($activeStates) || in_array((int)$task['is_completed'], $activeStates);
});
?>

<?php include __DIR__ . '/../../../public/navbar.php'; ?>

<div class="container-fluid py-3">

    <!-- Header -->
    <div class="mb-3">
        <h2 class="fw-semibold mb-1">Demandes — <?= e($project['title']) ?></h2>
        <?php if (!empty($project['description'])): ?>
            <div class="text-muted small"><?= e($project['description']) ?></div>
        <?php endif; ?>
        <div class="mt-2">
            <a href="/projects/<?= (int)$project['id'] ?>/tasks/create" class="btn btn-sm btn-dark">
                Nouvelle demande
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="mb-3 d-flex flex-wrap gap-2">
        <?php 
        $labels = [0=>'Envoyé',1=>'Traitement',2=>'Livré',3=>'Soldé'];
        foreach ($labels as $state => $label): 
            $count = $stateCounts[$state];
            $isActive = in_array($state, $activeStates);
            $newStates = $activeStates;
            $isActive ? $newStates = array_diff($activeStates, [$state]) : $newStates[] = $state;
            $query = http_build_query(['states' => $newStates]);
        ?>
            <a href="?<?= e($query) ?>"
               class="btn btn-sm <?= $isActive ? 'btn-dark' : 'btn-outline-secondary' ?>">
                <?= $count ?> <?= e($label) ?>
            </a>
        <?php endforeach; ?>
        <a href="?" class="btn btn-sm btn-outline-dark">
            <?= $totalTasks ?> total
        </a>
    </div>

    <!-- TABLE -->
    <div class="table-responsive">
        <table class="table table-sm align-middle table-bordered">

            <thead class="table-light">
                <tr class="text-uppercase small text-muted">
                    <th>#</th>
                    <th>Type</th>
                    <th>Titre</th>
                    <th>État</th>
                    <th>Créé</th>
                    <th>Deadline</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>

            <?php foreach ($tasks as $task): ?>
                <?php 
                    $approList = $task['appro'] ?? [];
                    $retourList = $task['retour'] ?? [];
                    $isAppro = !empty($approList);
                    $isRetour = !empty($retourList);

                    $s = (int)$task['is_completed'];

                    $stateClass = [
                        0 => 'secondary',
                        1 => 'dark',
                        2 => 'primary',
                        3 => 'success'
                    ];

                    $canEdit = false;
                    $canSetState = false;

                    if ($currentUserGroup === 'admin') {
                        $canEdit = true;
                        $canSetState = true;
                    } elseif ($currentUserGroup === 'magasin' && in_array($s + 1, [1,2])) {
                        $canSetState = true;
                    } elseif ($s === 0 && !in_array($currentUserGroup, ['admin','magasin'])) {
                        $canEdit = true;
                    } elseif (!in_array($currentUserGroup, ['admin','magasin']) && $s + 1 === 3) {
                        $canSetState = true;
                    }
                ?>

                <!-- TASK ROW -->
                <tr>
                    <td class="fw-semibold"><?= (int)$task['id'] ?></td>

                    <td>
                        <?php if ($isAppro): ?>
                            <span class="badge bg-dark">APPRO</span>
                        <?php elseif ($isRetour): ?>
                            <span class="badge bg-secondary">RETOUR</span>
                        <?php else: ?>
                            <span class="text-muted">-</span>
                        <?php endif; ?>
                    </td>

                    <td>
                        <div class="fw-semibold"><?= e($task['title']) ?></div>
                        <div class="small text-muted"><?= e($task['description']) ?></div>
                    </td>

                    <td>
                        <span class="badge bg-<?= $stateClass[$s] ?>">
                            <?= $labels[$s] ?>
                        </span>
                    </td>

                    <td class="small text-muted"><?= e($task['created_at']) ?></td>
                    <td class="small text-muted"><?= e($task['due_date']) ?></td>

                    <td>
                        <div class="d-flex gap-1 flex-wrap">

                            <?php if ($canEdit): ?>
                                <a href="/projects/<?= $project['id'] ?>/tasks/<?= $task['id'] ?>/edit"
                                   class="btn btn-sm btn-outline-dark">
                                    Modifier
                                </a>
                            <?php endif; ?>

                            <?php if ($canEdit && $currentUserGroup === 'admin'): ?>
                                <form method="POST"
                                      action="/projects/<?= $project['id'] ?>/tasks/<?= $task['id'] ?>/delete"
                                      onsubmit="return confirm('Confirmer la suppression ?');">
                                    <button class="btn btn-sm btn-outline-danger">Supprimer</button>
                                </form>
                            <?php endif; ?>

                            <?php if ($s < 3 && $canSetState): ?>
                                <form method="POST"
                                      action="/projects/<?= $project['id'] ?>/tasks/<?= $task['id'] ?>/mark-completed">
                                    <input type="hidden" name="state" value="<?= $s + 1 ?>">
                                    <button class="btn btn-sm btn-dark">
                                        → <?= $labels[$s + 1] ?>
                                    </button>
                                </form>
                            <?php endif; ?>

                        </div>
                    </td>
                </tr>

                <!-- APPRO BLOCK -->
                <?php foreach ($approList as $a): ?>
                <tr class="bg-light">
                    <td></td>
                    <td colspan="6">

                        <div class="small fw-semibold mb-1">APPRO</div>

                        <div class="d-flex gap-4 mb-1">
                            <div>PN: <?= e($a['pn']) ?></div>
                            <div>Qté: <?= (int)($a['nb'] ?? 0) ?></div>
                        </div>

                        <div class="small text-muted mb-1">
                            <?= e($a['designation']) ?>
                        </div>

                        <div class="small text-muted d-flex flex-wrap gap-3">
                            <div>Emplacement: <?= e($a['location']) ?></div>
                            <div>Avion: <?= e($a['plane']) ?></div>
                            <div>OF: <?= e($a['of']) ?></div>
                            <div>OE: <?= e($a['oe']) ?></div>
                        </div>

                    </td>
                </tr>
                <?php endforeach; ?>

                <!-- RETOUR BLOCK -->
                <?php foreach ($retourList as $r): ?>
                <tr class="bg-light">
                    <td></td>
                    <td colspan="6">

                        <div class="small fw-semibold mb-1">RETOUR</div>

                        <div class="d-flex gap-4 mb-1">
                            <div>PN: <?= e($r['PN']) ?></div>
                            <div>Qté: <?= (int)($r['nb'] ?? 0) ?></div>
                        </div>

                        <div class="small text-muted d-flex gap-3">
                            <div>SN: <?= e($r['sn']) ?></div>
                            <div>Certif: <?= e($r['certif']) ?></div>
                        </div>

                    </td>
                </tr>
                <?php endforeach; ?>

            <?php endforeach; ?>

            </tbody>
        </table>
    </div>
</div>