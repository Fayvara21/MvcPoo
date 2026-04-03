<?php
session_start();

// Helper
function e($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

$currentUserGroup = $_SESSION['group'] ?? '';

$stateCounts = [0 => 0, 1 => 0, 2 => 0, 3 => 0];
foreach ($tasks as $t) {
    $state = (int)($t['is_completed'] ?? 0);
    if (isset($stateCounts[$state])) $stateCounts[$state]++;
}
$totalTasks = array_sum($stateCounts);

$activeStates = $_GET['states'] ?? [];
if (!is_array($activeStates)) $activeStates = [$activeStates];
$activeStates = array_map('intval', $activeStates);

$searchQuery = trim($_GET['q'] ?? '');

$tasks = array_filter($tasks, function($task) use ($activeStates, $searchQuery) {
    $matchState = empty($activeStates) || in_array((int)$task['is_completed'], $activeStates);
    if ($searchQuery === '') return $matchState;
    $matchSearch = stripos($task['title'], $searchQuery) !== false
                || stripos($task['description'] ?? '', $searchQuery) !== false;
    return $matchState && $matchSearch;
});

$defaultWrapped = true;
?>

<?php include __DIR__ . '/../../../public/navbar.php'; ?>

<div class="container-fluid py-4">

    <div class="card mb-4 shadow-sm border-0 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="fw-bold">Demandes vers <?= e($project['title']) ?></h1>
            <?php if (!empty($project['description'])): ?>
                <p class="text-muted"><?= e($project['description']) ?></p>
            <?php endif; ?>
        </div>
        <button id="toggle-all" class="btn btn-outline-primary">Montrer tout</button>
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
            $query = http_build_query(['states' => $newStates, 'q' => $searchQuery]);
        ?>
            <a href="?<?= e($query) ?>" class="btn btn-sm <?= $isActive ? 'btn-dark' : 'btn-outline-dark' ?>">
                <?= $count ?> <?= e($label) ?>
            </a>
        <?php endforeach; ?>
        <a href="?q=<?= urlencode($searchQuery) ?>" class="btn btn-sm btn-secondary"><?= $totalTasks ?> total</a>
    </div>

    <!-- Search -->
    <form class="mb-4" method="GET">
        <div class="input-group">
            <input type="text" name="q" class="form-control" placeholder="Rechercher..." value="<?= e($searchQuery) ?>">
            <?php foreach ($activeStates as $state): ?>
                <input type="hidden" name="states[]" value="<?= (int)$state ?>">
            <?php endforeach; ?>
            <button class="btn btn-outline-primary" type="submit">🔍</button>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Type</th>
                    <th>Titre / Description</th>
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

                    $firstSubTaskRendered = false; // Track first APPRO/RETOUR for actions
                ?>

                <!-- MAIN TASK ROW -->
                <tr class="border-top border-3 task-row wrapped">
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
                        <?php if (!empty($task['description'])): ?>
                            <div class="small text-muted"><?= e($task['description']) ?></div>
                        <?php endif; ?>
                    </td>
                    <td><span class="badge bg-<?= $statesColor[$s] ?>"><?= $labels[$s] ?></span></td>
                    <td class="small text-muted"><?= e($task['created_at']) ?></td>
                    <td class="small text-muted"><?= e($task['due_date']) ?></td>
                    <td></td> <!-- Actions moved to first sub-task -->
                </tr>

                <!-- APPRO BLOCK -->
                <?php foreach ($approList as $a): ?>
                <tr class="wrapped">
                    <td></td>
                    <td colspan="6">
                        <div class="p-3 rounded-3 border-start border-4 border-success bg-success bg-opacity-10 mb-2 d-flex justify-content-between align-items-start">
                            <div>
                                <div class="fw-bold text-success mb-2">APPRO PN</div>
                                <div class="d-flex align-items-center gap-4 mb-2">
                                    <div class="fw-semibold">PN: <?= e($a['pn']) ?></div>
                                    <div class="fw-semibold">Qté: <?= (int)($a['nb'] ?? 0) ?></div>
                                </div>
                                <?php if (!empty($a['designation'])): ?>
                                    <div class="small text-muted mb-2"><?= e($a['designation']) ?></div>
                                <?php endif; ?>
                                <?php if (!empty($a['description'])): ?>
                                    <div class="small text-muted mb-2"><?= e($a['description']) ?></div>
                                <?php endif; ?>
                                <div class="d-flex flex-wrap gap-3 small">
                                    <div><span class="text-muted">Emplacement:</span> <?= e($a['location']) ?></div>
                                    <div><span class="text-muted">Avion:</span> <?= e($a['plane']) ?></div>
                                    <div><span class="text-muted">OF:</span> <?= e($a['of']) ?></div>
                                    <div><span class="text-muted">OE:</span> <?= e($a['oe']) ?></div>
                                </div>
                            </div>
                            <?php if (!$firstSubTaskRendered): ?>
                                <div class="d-flex flex-column gap-1 ms-3">
                                    <?php if ($canEdit): ?>
                                        <a href="/projects/<?= $project['id'] ?>/tasks/<?= $task['id'] ?>/edit" class="btn btn-sm btn-primary">Modifier</a>
                                    <?php endif; ?>
                                    <?php if ($canEdit && $currentUserGroup === 'admin'): ?>
                                        <form method="POST" action="/projects/<?= $project['id'] ?>/tasks/<?= $task['id'] ?>/delete" onsubmit="return confirm('Confirmer la suppression ?');">
                                            <button class="btn btn-sm btn-danger">Supprimer</button>
                                        </form>
                                    <?php endif; ?>
                                    <?php if ($s < 3 && $canSetState): ?>
                                        <form method="POST" action="/projects/<?= $project['id'] ?>/tasks/<?= $task['id'] ?>/mark-completed">
                                            <input type="hidden" name="state" value="<?= $s + 1 ?>">
                                            <button class="btn btn-sm btn-success">→ <?= $labels[$s + 1] ?></button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                                <?php $firstSubTaskRendered = true; ?>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>

                <!-- RETOUR BLOCK -->
                <?php foreach ($retourList as $r): ?>
                <tr class="wrapped">
                    <td></td>
                    <td colspan="6">
                        <div class="p-3 rounded-3 border-start border-4 border-warning bg-warning bg-opacity-10 mb-2 d-flex justify-content-between align-items-start">
                            <div>
                                <div class="fw-bold text-warning mb-2">RETOUR PN</div>
                                <div class="d-flex align-items-center gap-4 mb-2">
                                    <div class="fw-semibold">PN: <?= e($r['PN']) ?></div>
                                    <div class="fw-semibold">Qté: <?= (int)($r['nb'] ?? 0) ?></div>
                                </div>
                                <?php if (!empty($r['designation'])): ?>
                                    <div class="small text-muted mb-2"><?= e($r['designation']) ?></div>
                                <?php endif; ?>
                                <?php if (!empty($r['description'])): ?>
                                    <div class="small text-muted mb-2"><?= e($r['description']) ?></div>
                                <?php endif; ?>
                                <div class="d-flex flex-wrap gap-3 small">
                                    <div><span class="text-muted">SN:</span> <?= e($r['sn']) ?></div>
                                    <div><span class="text-muted">Certif:</span> <?= e($r['certif']) ?></div>
                                </div>
                            </div>
                            <?php if (!$firstSubTaskRendered): ?>
                                <div class="d-flex flex-column gap-1 ms-3">
                                    <?php if ($canEdit): ?>
                                        <a href="/projects/<?= $project['id'] ?>/tasks/<?= $task['id'] ?>/edit" class="btn btn-sm btn-primary">Modifier</a>
                                    <?php endif; ?>
                                    <?php if ($canEdit && $currentUserGroup === 'admin'): ?>
                                        <form method="POST" action="/projects/<?= $project['id'] ?>/tasks/<?= $task['id'] ?>/delete" onsubmit="return confirm('Confirmer la suppression ?');">
                                            <button class="btn btn-sm btn-danger">Supprimer</button>
                                        </form>
                                    <?php endif; ?>
                                    <?php if ($s < 3 && $canSetState): ?>
                                        <form method="POST" action="/projects/<?= $project['id'] ?>/tasks/<?= $task['id'] ?>/mark-completed">
                                            <input type="hidden" name="state" value="<?= $s + 1 ?>">
                                            <button class="btn btn-sm btn-success">→ <?= $labels[$s + 1] ?></button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                                <?php $firstSubTaskRendered = true; ?>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>

            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.getElementById('toggle-all').addEventListener('click', function() {
    const rows = document.querySelectorAll('.task-row, tr.wrapped');
    const isWrapped = Array.from(rows).every(r => r.classList.contains('wrapped'));
    rows.forEach(r => r.classList.toggle('wrapped', !isWrapped));
    this.textContent = isWrapped ? 'Cacher tout' : 'Montrer tout';
});
</script>

<style>
tr.wrapped > td > div {
    display: none;
}
</style>