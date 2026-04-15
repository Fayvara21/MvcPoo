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
    if (isset($stateCounts[$state]))
        $stateCounts[$state]++;
}
$totalTasks = array_sum($stateCounts);

//colors

$stateClass = [
    0 => 'blue',
    1 => 'red',
    2 => 'orange',
    3 => 'yellow',
    4 => 'indigo',
    5 => 'pink'
];

// WORKFLOW (correct branching model)
$transitions = [
    0 => [1],
    1 => [2, 4, 5],
    2 => [3],
    3 => [],
    4 => [0],
    5 => [0]
];

// Filters
$activeStates = $_GET['states'] ?? [];
if (!is_array($activeStates))
    $activeStates = [$activeStates];
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

            <!-- Divider -->
            <hr class="my-3">

            <!-- Filters -->
            <div class="d-flex flex-wrap align-items-center gap-2">

                <span class="text-muted small me-2">Filtres :</span>

                <?php
                $labels = [0 => 'Envoyé', 1 => 'Traitement', 2 => 'Livré', 3 => 'Soldé', 4 => 'En achat', 5 => 'En sous-traitance'];

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
                        class="btn btn-sm <?= $isActive ? 'btn-' . $stateClass[$state] : 'btn-outline-' . $stateClass[$state] ?> d-flex align-items-center gap-1">
                        <span><?= e($label) ?></span>
                        <span class="badge bg-light text-dark"><?= $count ?></span>
                    </a>
                <?php endforeach; ?>

                <div class="">
                    <a href="?" class="btn btn-sm btn-outline-dark">
                        <?= $totalTasks ?> total
                    </a>
                </div>

            </div>

        </div>
    </div>

    <div class="card shadow-sm border-0">

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
                        <th>Demandeur</th>
                        <th class="actions-header" style="">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($tasks as $taskIndex => $task): ?>
                        <?php
                        $approList = $task['appro'] ?? [];
                        $retourList = $task['retour'] ?? [];

                        $s = (int) ($task['is_completed'] ?? 0);

                        $canEdit = false;
                        $canSetState = false;

                        if ($currentUserGroup === 'admin') {
                            $canEdit = true;
                            $canSetState = true;

                        } elseif ($currentUserGroup === 'magasin' && in_array($s, [0, 1, 2, 4, 5])) {
                            $canSetState = true;

                        } elseif ($s === 0 && !in_array($currentUserGroup, ['admin', 'magasin'])) {
                            $canEdit = true;

                        } elseif (!in_array($currentUserGroup, ['admin', 'magasin']) && in_array($s, [3])) {
                            $canSetState = true;
                        }

                        $subIndex = 0;
                        $taskId = (int) $task['id'];

                        $nextStates = $transitions[$s] ?? [];
                        $nextColor = $stateClass[$nextStates[0]] ?? 'secondary';
                        ?>

                        <!-- MAIN TASK ROW -->
                        <tr style="border-top:2px solid #dee2e6; cursor:pointer;" class="task-row"
                            data-task="<?= $taskId ?>">

                            <td class="fw-semibold"><?= $taskId ?></td>

                            <td>
                                <?php if (!empty($approList)): ?>
                                    <span class="badge bg-blue">APPRO</span>
                                <?php elseif (!empty($retourList)): ?>
                                    <span class="badge bg-yellow">RETOUR</span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <div class="fw-semibold task-title">
                                    <?= e($task['title']) ?>
                                </div>

                                <div class="small description" style="display:block; font-size:1rem;">
                                    <?= e($task['description']) ?>
                                </div>
                            </td>

                            <td>
                                <span class="badge bg-<?= $stateClass[$s] ?>">
                                    <?= e($labels[$s]) ?>
                                </span>
                            </td>

                            <td class="small text-muted">
                                <?= e($task['created_at']) ?>
                            </td>

                            <td class="small text-muted">
                                <?= e($task['due_date']) ?>
                            </td>

                            <td class="small text-muted">
                                <?= e($task["user_name"]) ?>
                            </td>

                            <td class="actions">
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
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                Supprimer
                                            </button>
                                        </form>
                                    <?php endif; ?>

                                    <!-- STATE TRANSITIONS (FIXED: no more +1 logic) -->
                                    <?php if (!empty($nextStates) && $canSetState): ?>
                                        <?php foreach ($nextStates as $nextState): ?>
                                            <form method="POST"
                                                action="/projects/<?= $project['id'] ?>/tasks/<?= $task['id'] ?>/mark-completed">

                                                <input type="hidden" name="state" value="<?= (int) $nextState ?>">

                                                <button type="submit"
                                                    class="btn btn-sm <?= 'btn-' . ($stateClass[$nextState] ?? 'secondary') ?>">
                                                    → <?= e($labels[$nextState]) ?>
                                                </button>
                                            </form>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>

                        <!-- SUB-TASK ROWS -->
                        <?php foreach ($approList as $a): ?>
                            <?php $subIndex++; ?>
                            <tr class="bg-light sub-task-<?= $taskId ?>" style="">
                                <td><?= $subIndex ?></td>
                                <td colspan="7">
                                    <div class="small fw-semibold mb-1 text-blue">APPRO</div>
                                    <div class="d-flex gap-4 mb-1">
                                        <div><strong>PN:</strong> <?= e($a['pn'] ?? '') ?></div>
                                        <div><strong>Qté:</strong> <?= (int) ($a['nb'] ?? 0) ?></div>
                                        <div><strong>OF:</strong> <?= e($a['of'] ?? '') ?></div>
                                        <div><strong>Avion:</strong> <?= e($a['plane'] ?? '') ?></div>
                                    </div>
                                    <div class="small mb-1">
                                        <div><strong>Désignation:</strong> <?= e($a['designation'] ?? '') ?></div>
                                    </div>
                                    <div class="small d-flex flex-wrap gap-3">
                                        <div><strong>Emplacement:</strong> <?= e($a['location'] ?? '') ?></div>
                                        <div><strong>OE:</strong> <?= e($a['oe'] ?? '') ?></div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        <?php foreach ($retourList as $r): ?>
                            <?php $subIndex++; ?>
                            <tr class="bg-light sub-task-<?= $taskId ?>" style="">
                                <td><?= $subIndex ?></td>
                                <td colspan="6">
                                    <div class="small fw-semibold mb-1 text-yellow">RETOUR</div>
                                    <div class="d-flex gap-4 mb-1">
                                        <div><strong>PN:</strong> <?= e($r['PN'] ?? '') ?></div>
                                        <div><strong>Qté:</strong> <?= (int) ($r['nb'] ?? 0) ?></div>
                                    </div>
                                    <div class="small d-flex gap-3">
                                        <div><strong>SN:</strong> <?= e($r['sn'] ?? '') ?></div>
                                        <div><strong>Certif:</strong> <?= e($r['certif'] ?? '') ?></div>
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
        const firstActionsHeader = document.querySelector('.actions-header');

        // Toggle task rows on click
        document.querySelectorAll('.task-row').forEach(function (row) {
            row.addEventListener('click', function () {
                const taskId = row.dataset.task;
                const actions = row.querySelector('.actions');

                // Toggle sub-rows
                document.querySelectorAll('.sub-task-' + taskId).forEach(function (subRow) {
                    subRow.style.display = subRow.style.display === 'none' ? 'table-row' : 'none';
                });

                // Toggle actions column

            });
        });

        // Search function
        const searchInput = document.getElementById('task-search');

        searchInput.addEventListener('input', function () {
            const query = searchInput.value.toLowerCase().trim();

            document.querySelectorAll('.task-row').forEach(function (taskRow) {
                const taskId = taskRow.dataset.task;

                // Get all relevant main row text
                const title = taskRow.querySelector('.task-title')?.textContent.toLowerCase() || '';
                const description = taskRow.querySelector('.description')?.textContent.toLowerCase() || '';
                const requester = taskRow.querySelector('td:nth-child(7)')?.textContent.toLowerCase() || '';

                // Combine main fields
                let match = (
                    title.includes(query) ||
                    description.includes(query) ||
                    requester.includes(query)
                );

                // Search inside subtasks
                document.querySelectorAll('.sub-task-' + taskId).forEach(function (subRow) {
                    const subText = subRow.textContent.toLowerCase();
                    if (subText.includes(query)) {
                        match = true;
                    }
                });

                // Show / hide
                if (match || query === '') {
                    taskRow.style.display = '';

                    // Show subtasks only if searching
                    document.querySelectorAll('.sub-task-' + taskId).forEach(function (subRow) {
                        subRow.style.display = query ? 'table-row' : 'none';
                    });

                } else {
                    taskRow.style.display = 'none';
                    document.querySelectorAll('.sub-task-' + taskId).forEach(function (subRow) {
                        subRow.style.display = 'none';
                    });
                }
            });
        });
    });
</script>