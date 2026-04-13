<?php
session_start();

// Helper
function e($value)
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

$currentUserGroup = $_SESSION['group'] ?? '';


// State counts 
$stateCounts = [0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
foreach ($tasks as $t) {
    $state = (int) ($t['is_completed'] ?? 0);
    if (isset($stateCounts[$state])) {
        $stateCounts[$state]++;
    }
}
$totalTasks = array_sum($stateCounts);

// Filters
$activeStates = $_GET['states'] ?? [];
if (!is_array($activeStates)) {
    $activeStates = [$activeStates];
}
$activeStates = array_map('intval', $activeStates);

// Filter tasks
$tasks = array_filter($tasks, function ($task) use ($activeStates) {
    return empty($activeStates) || in_array((int) $task['is_completed'], $activeStates);
});
?>

<?php include __DIR__ . '/../../../public/navbar.php'; ?>

<div class="container-fluid py-3">

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">

            <!-- Header -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
                <div>
                    <h2 class="fw-semibold mb-1">
                        Demandes — <?= e($project['title']) ?>
                    </h2>
                    <?php if (!empty($project['description'])): ?>
                        <div class="text-muted small">
                            <?= e($project['description']) ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="d-flex gap-2 align-items-center flex-wrap">
                    <input type="text" id="task-search" class="form-control" placeholder="Rechercher..."
                        style="width: 220px;">

                    <a href="/projects/<?= (int) $project['id'] ?>/tasks/create" class="btn btn-primary fw-semibold">
                        + Nouvelle demande
                    </a>
                </div>
            </div>

            <hr class="my-3">

            <!-- Filters -->
            <div class="d-flex flex-wrap align-items-center gap-2">

                <span class="text-muted small me-2">Filtres :</span>

                <?php
                $labels = [
                    0 => 'Envoyé',
                    1 => 'Traitement',
                    2 => 'Livré',
                    3 => 'Soldé',
                    4 => 'en achat',
                    5 => 'en sous-traitance'
                ];

                $filterColors = [
                    0 => 'primary',
                    1 => 'warning',
                    2 => 'info',
                    3 => 'success',
                    4 => 'secondary',
                    5 => 'danger'
                ];

                foreach ($labels as $state => $label):
                    $count = $stateCounts[$state] ?? 0;
                    $isActive = in_array($state, $activeStates);
                    $newStates = $activeStates;

                    if ($isActive) {
                        $newStates = array_diff($activeStates, [$state]);
                    } else {
                        $newStates[] = $state;
                    }

                    $query = http_build_query(['states' => $newStates]);
                ?>
                    <a href="?<?= e($query) ?>"
                        class="btn btn-sm <?= $isActive ? 'btn-' . $filterColors[$state] : 'btn-outline-' . $filterColors[$state] ?> d-flex align-items-center gap-1">
                        <span><?= e($label) ?></span>
                        <span class="badge bg-light text-dark"><?= $count ?></span>
                    </a>
                <?php endforeach; ?>

                <div>
                    <a href="?" class="btn btn-sm btn-outline-dark">
                        <?= $totalTasks ?> total
                    </a>
                </div>

            </div>

        </div>
    </div>

    <div class="card shadow-sm border-0">

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
                        <th>Demandeur</th>
                        <th class="actions-header">Actions</th>
                    </tr>
                </thead>

                <tbody>

                <?php foreach ($tasks as $taskIndex => $task): ?>

                    <?php
                    $approList = $task['appro'] ?? [];
                    $retourList = $task['retour'] ?? [];

                    $s = (int) ($task['is_completed'] ?? 0);

                    $stateClass = [
                        0 => 'secondary',
                        1 => 'warning',
                        2 => 'info',
                        3 => 'success',
                        4 => 'secondary',
                        5 => 'danger'
                    ];

                    // WORKFLOW (correct branching model)
                    $transitions = [
                        0 => [1],
                        1 => [2],
                        2 => [3, 4, 5],
                        3 => [],
                        4 => [3],
                        5 => [3]
                    ];

                    $canEdit = false;
                    $canSetState = false;

                    if ($currentUserGroup === 'admin') {
                        $canEdit = true;
                        $canSetState = true;
                    } elseif ($currentUserGroup === 'magasin' && in_array($s, [1, 2])) {
                        $canSetState = true;
                    } elseif ($s === 0 && !in_array($currentUserGroup, ['admin', 'magasin'])) {
                        $canEdit = true;
                    } elseif (!in_array($currentUserGroup, ['admin', 'magasin']) && in_array($s, [3, 4, 5])) {
                        $canSetState = true;
                    }

                    $subIndex = 0;
                    $taskId = (int) $task['id'];
                    ?>

                    <!-- MAIN ROW -->
                    <tr style="border-top:2px solid #dee2e6; cursor:pointer;" class="task-row"
                        data-task="<?= $taskId ?>">

                        <td class="fw-semibold"><?= $taskId ?></td>

                        <td>
                            <?php if (!empty($approList)): ?>
                                <span class="badge bg-primary">APPRO</span>
                            <?php elseif (!empty($retourList)): ?>
                                <span class="badge bg-warning">RETOUR</span>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>

                        <td>
                            <div class="fw-semibold task-title"><?= e($task['title']) ?></div>
                            <div class="small description"><?= e($task['description']) ?></div>
                        </td>

                        <td>
                            <span class="badge bg-<?= $stateClass[$s] ?? 'secondary' ?>">
                                <?= e($labels[$s] ?? 'Unknown') ?>
                            </span>
                        </td>

                        <td class="small text-muted"><?= e($task['created_at']) ?></td>
                        <td class="small text-muted"><?= e($task['due_date']) ?></td>
                        <td class="small text-muted task-requester"><?= e($task['user_name']) ?></td>

                        <td class="actions">
                            <div class="d-flex gap-1 flex-wrap">

                                <?php if ($canEdit): ?>
                                    <a href="/projects/<?= $project['id'] ?>/tasks/<?= $task['id'] ?>/edit"
                                        class="btn btn-sm btn-primary">Modifier</a>
                                <?php endif; ?>

                                <?php if ($canEdit && $currentUserGroup === 'admin'): ?>
                                    <form method="POST"
                                        action="/projects/<?= $project['id'] ?>/tasks/<?= $task['id'] ?>/delete"
                                        onsubmit="return confirm('Confirmer la suppression ?');">
                                        <button class="btn btn-sm btn-danger">Supprimer</button>
                                    </form>
                                <?php endif; ?>

                                <!-- STATE ACTIONS (FIXED) -->
                                <?php if (!empty($transitions[$s]) && $canSetState): ?>
                                    <?php foreach ($transitions[$s] as $next): ?>
                                        <form method="POST"
                                            action="/projects/<?= $project['id'] ?>/tasks/<?= $task['id'] ?>/mark-completed">

                                            <input type="hidden" name="state" value="<?= $next ?>">

                                            <button class="btn btn-sm btn-success">
                                                → <?= $labels[$next] ?>
                                            </button>
                                        </form>
                                    <?php endforeach; ?>
                                <?php endif; ?>

                            </div>
                        </td>
                    </tr>

                    <!-- SUBTASKS -->
                    <?php foreach ($approList as $a): $subIndex++; ?>
                        <tr class="bg-light sub-task-<?= $taskId ?>">
                            <td><?= $subIndex ?></td>
                            <td colspan="7">
                                <div class="small fw-semibold text-primary">APPRO</div>
                                <div class="d-flex gap-4">
                                    <div><strong>PN:</strong> <?= e($a['pn'] ?? '') ?></div>
                                    <div><strong>Qté:</strong> <?= (int) ($a['nb'] ?? 0) ?></div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <?php foreach ($retourList as $r): $subIndex++; ?>
                        <tr class="bg-light sub-task-<?= $taskId ?>">
                            <td><?= $subIndex ?></td>
                            <td colspan="7">
                                <div class="small fw-semibold text-dark">RETOUR</div>
                                <div class="d-flex gap-4">
                                    <div><strong>PN:</strong> <?= e($r['PN'] ?? '') ?></div>
                                    <div><strong>Qté:</strong> <?= (int) ($r['nb'] ?? 0) ?></div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                <?php endforeach; ?>

                </tbody>

            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('.task-row').forEach(function (row) {
        row.addEventListener('click', function () {
            const taskId = row.dataset.task;

            document.querySelectorAll('.sub-task-' + taskId).forEach(function (subRow) {
                subRow.style.display = subRow.style.display === 'none' ? 'table-row' : 'none';
            });
        });
    });

    const searchInput = document.getElementById('task-search');

    searchInput.addEventListener('input', function () {
        const query = searchInput.value.toLowerCase().trim();

        document.querySelectorAll('.task-row').forEach(function (taskRow) {

            const title = taskRow.querySelector('.task-title')?.textContent.toLowerCase() || '';
            const description = taskRow.querySelector('.description')?.textContent.toLowerCase() || '';
            const requester = taskRow.querySelector('.task-requester')?.textContent.toLowerCase() || '';

            let match = (
                title.includes(query) ||
                description.includes(query) ||
                requester.includes(query)
            );

            const taskId = taskRow.dataset.task;

            document.querySelectorAll('.sub-task-' + taskId).forEach(function (subRow) {
                if (subRow.textContent.toLowerCase().includes(query)) {
                    match = true;
                }
            });

            if (match || query === '') {
                taskRow.style.display = '';
            } else {
                taskRow.style.display = 'none';
            }
        });
    });

});
</script>