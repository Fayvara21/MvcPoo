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

<div class="container-fluid py-4">

    <!-- Header -->
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-body">
            <h1 class="fw-bold">Demandes vers <?= e($project['title']) ?></h1>
            <?php if (!empty($project['description'])): ?>
                <p class="text-muted"><?= e($project['description']) ?></p>
            <?php endif; ?>
            <a href="/projects/<?= (int)$project['id'] ?>/tasks/create" class="btn btn-primary">
                + Nouvelle demande
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="mb-4">
        <?php 
        $labels = [0=>'Envoyé',1=>'Traitement',2=>'Livré',3=>'Soldé'];
        foreach ($labels as $state => $label): 
            $count = $stateCounts[$state];
            $isActive = in_array($state, $activeStates);
            $newStates = $activeStates;
            $isActive ? $newStates = array_diff($activeStates, [$state]) : $newStates[] = $state;
            $query = http_build_query(['states' => $newStates]);
        ?>
            <a href="?<?= e($query) ?>" class="btn btn-sm <?= $isActive ? 'btn-dark' : 'btn-outline-dark' ?>">
                <?= $count ?> <?= e($label) ?>
            </a>
        <?php endforeach; ?>
        <a href="?" class="btn btn-sm btn-secondary"><?= $totalTasks ?> total</a>
    </div>

    <!-- TABLE -->
    <div class="table-responsive">
        <table class="table align-middle">

            <thead class="table-light">
                <tr>
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
                    $statesColor = [0=>'secondary',1=>'warning',2=>'info',3=>'success'];

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
                <tr class="border-top border-3">
                    <td class="fw-bold"><?= (int)$task['id'] ?></td>

                    <td>
                        <?php if ($isAppro): ?>
                            <span class="badge bg-success">APPRO</span>
                        <?php elseif ($isRetour): ?>
                            <span class="badge bg-warning text-dark">RETOUR</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">-</span>
                        <?php endif; ?>
                    </td>

                    <td>
                        <div class="fw-semibold"><?= e($task['title']) ?></div>
                        <div class="small text-muted"><?= e($task['description']) ?></div>
                    </td>

                    <td>
                        <span class="badge bg-<?= $statesColor[$s] ?>">
                            <?= $labels[$s] ?>
                        </span>
                    </td>

                    <td class="small text-muted"><?= e($task['created_at']) ?></td>
                    <td class="small text-muted"><?= e($task['due_date']) ?></td>

                    <td>
                        <div class="d-flex gap-1 flex-wrap">

                            <?php if ($canEdit): ?>
                                <a href="/projects/<?= $project['id'] ?>/tasks/<?= $task['id'] ?>/edit"
                                   class="btn btn-sm btn-primary">
                                    Modifier
                                </a>
                            <?php endif; ?>

                            <?php if ($canEdit && $currentUserGroup === 'admin'): ?>
                                <form method="POST"
                                      action="/projects/<?= $project['id'] ?>/tasks/<?= $task['id'] ?>/delete"
                                      onsubmit="return confirm('Confirmer la suppression ?');">
                                    <button class="btn btn-sm btn-danger">Supprimer</button>
                                </form>
                            <?php endif; ?>

                            <?php if ($s < 3 && $canSetState): ?>
                                <form method="POST"
                                      action="/projects/<?= $project['id'] ?>/tasks/<?= $task['id'] ?>/mark-completed">
                                    <input type="hidden" name="state" value="<?= $s + 1 ?>">
                                    <button class="btn btn-sm btn-success">
                                        → <?= $labels[$s + 1] ?>
                                    </button>
                                </form>
                            <?php endif; ?>

                        </div>
                    </td>
                </tr>

                <!-- APPRO BLOCK -->
                <?php foreach ($approList as $a): ?>
                <tr>
                    <td></td>
                    <td colspan="6">
                        <div class="p-3 rounded-3 border-start border-4 border-info bg-info bg-opacity-10 mb-2">

                            <div class="fw-bold mb-2">APPRO</div>

                            <div class="d-flex align-items-center gap-4 mb-2">
                                <div class="fw-semibold">
                                    PN: <span class="text-dark"><?= e($a['pn']) ?></span>
                                </div>
                                <div class="fw-semibold">
                                    Qté: <span class="text-dark"><?= (int)($a['nb'] ?? 0) ?></span>
                                </div>
                            </div>

                            <div class="small text-muted mb-2">
                                <?= e($a['designation']) ?>
                            </div>

                            <div class="d-flex flex-wrap gap-3 small">
                                <div><span class="text-muted">Emplacement:</span> <?= e($a['location']) ?></div>
                                <div><span class="text-muted">Avion:</span> <?= e($a['plane']) ?></div>
                                <div><span class="text-muted">OF:</span> <?= e($a['of']) ?></div>
                                <div><span class="text-muted">OE:</span> <?= e($a['oe']) ?></div>
                            </div>

                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>

                <!-- RETOUR BLOCK -->
                <?php foreach ($retourList as $r): ?>
                <tr>
                    <td></td>
                    <td colspan="6">
                        <div class="p-3 rounded-3 border-start border-4 border-info bg-info bg-opacity-10 mb-2">

                            <div class="fw-bold mb-2">RETOUR</div>

                            <div class="d-flex align-items-center gap-4 mb-2">
                                <div class="fw-semibold">
                                    PN: <span class="text-dark"><?= e($r['PN']) ?></span>
                                </div>
                                <div class="fw-semibold">
                                    Qté: <span class="text-dark"><?= (int)($r['nb'] ?? 0) ?></span>
                                </div>
                            </div>

                            <div class="d-flex flex-wrap gap-3 small">
                                <div><span class="text-muted">SN:</span> <?= e($r['sn']) ?></div>
                                <div><span class="text-muted">Certif:</span> <?= e($r['certif']) ?></div>
                            </div>

                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>

            <?php endforeach; ?>

            </tbody>
        </table>
    </div>
</div>