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

// Filter tasks
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
        <div class="mt-2 d-flex gap-2 flex-wrap">
            <a href="/projects/<?= (int)$project['id'] ?>/tasks/create" class="btn btn-primary btn-sm">
                + Nouvelle demande
            </a>
            <input type="text" id="task-search" class="form-control form-control-sm" placeholder="Rechercher titre, OF, Avion, PN" style="max-width:300px;">
        </div>
    </div>

    <!-- Filters -->
    <div class="mb-3 d-flex flex-wrap gap-2">
        <?php 
        $labels = [0=>'Envoyé',1=>'Traitement',2=>'Livré',3=>'Soldé'];
        $filterColors = [0=>'primary',1=>'warning',2=>'info',3=>'success'];

        foreach ($labels as $state => $label): 
            $count = $stateCounts[$state];
            $isActive = in_array($state, $activeStates);
            $newStates = $activeStates;
            $isActive ? $newStates = array_diff($activeStates, [$state]) : $newStates[] = $state;
            $query = http_build_query(['states' => $newStates]);
        ?>
            <a href="?<?= e($query) ?>"
               class="btn btn-sm <?= $isActive ? 'btn-'.$filterColors[$state] : 'btn-outline-'.$filterColors[$state] ?>">
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
                    <th class="actions-header" style="display:none;">Actions</th>
                </tr>
            </thead>

            <tbody>
            <?php foreach ($tasks as $taskIndex => $task): ?>
                <?php 
                    $approList = $task['appro'] ?? [];
                    $retourList = $task['retour'] ?? [];

                    $s = (int)($task['is_completed'] ?? 0);
                    $stateClass = [0=>'secondary',1=>'warning',2=>'info',3=>'success'];

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

                    $subIndex = 0; // sub-counter for APPRO/RETOUR
                    $taskId = (int)$task['id'];
                ?>

                <!-- MAIN TASK ROW -->
                <tr style="border-top:2px solid #dee2e6; cursor:pointer;" class="task-row" data-task="<?= $taskId ?>">
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
                        <div class="fw-semibold"><?= e($task['title']) ?></div>
                        <div class="small description" style="display:block; font-size:1rem;"><?= e($task['description']) ?></div>
                    </td>
                    <td><span class="badge bg-<?= $stateClass[$s] ?>"><?= $labels[$s] ?></span></td>
                    <td class="small text-muted"><?= e($task['created_at']) ?></td>
                    <td class="small text-muted"><?= e($task['due_date']) ?></td>
                    <td class="actions" style="display:none;">
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
                            <?php if ($s < 3 && $canSetState): ?>
                                <form method="POST"
                                      action="/projects/<?= $project['id'] ?>/tasks/<?= $task['id'] ?>/mark-completed">
                                    <input type="hidden" name="state" value="<?= $s + 1 ?>">
                                    <button class="btn btn-sm btn-success">→ <?= $labels[$s + 1] ?></button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>

                <!-- SUB-TASK ROWS -->
                <?php foreach ($approList as $a): ?>
                    <?php $subIndex++; ?>
                    <tr class="bg-light sub-task-<?= $taskId ?>" style="display:none;">
                        <td><?= $subIndex ?></td>
                        <td colspan="6">
                            <div class="small fw-semibold mb-1 text-primary">APPRO</div>
                            <div class="d-flex gap-4 mb-1">
                                <div><strong>PN:</strong> <?= e($a['pn'] ?? '') ?></div>
                                <div><strong>Qté:</strong> <?= (int)($a['nb'] ?? 0) ?></div>
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
                    <tr class="bg-light sub-task-<?= $taskId ?>" style="display:none;">
                        <td><?= $subIndex ?></td>
                        <td colspan="6">
                            <div class="small fw-semibold mb-1 text-dark">RETOUR</div>
                            <div class="d-flex gap-4 mb-1">
                                <div><strong>PN:</strong> <?= e($r['PN'] ?? '') ?></div>
                                <div><strong>Qté:</strong> <?= (int)($r['nb'] ?? 0) ?></div>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const firstActionsHeader = document.querySelector('.actions-header');

    // Toggle task rows on click
    document.querySelectorAll('.task-row').forEach(function(row) {
        row.addEventListener('click', function() {
            const taskId = row.dataset.task;
            const actions = row.querySelector('.actions');

            // Toggle sub-rows
            document.querySelectorAll('.sub-task-' + taskId).forEach(function(subRow) {
                subRow.style.display = subRow.style.display === 'none' ? 'table-row' : 'none';
            });

            // Toggle actions column
            if (actions) {
                const isVisible = actions.style.display !== 'none';
                actions.style.display = isVisible ? 'none' : 'table-cell';
                if (firstActionsHeader) firstActionsHeader.style.display = isVisible ? 'none' : 'table-cell';
            }
        });
    });

    // Search function
    const searchInput = document.getElementById('task-search');
    searchInput.addEventListener('input', function() {
        const query = searchInput.value.toLowerCase();

        document.querySelectorAll('.task-row').forEach(function(taskRow) {
            const taskId = taskRow.dataset.task;
            const title = taskRow.querySelector('td:nth-child(3) div.fw-semibold')?.textContent.toLowerCase() || '';
            
            // Search inside subtasks
            let match = title.includes(query);
            document.querySelectorAll('.sub-task-' + taskId).forEach(function(subRow) {
                const text = subRow.textContent.toLowerCase();
                if (text.includes(query)) match = true;
            });

            if (match) {
                taskRow.style.display = '';
                // Unwrap matched task
                document.querySelectorAll('.sub-task-' + taskId).forEach(function(subRow) {
                    subRow.style.display = 'table-row';
                });
                const actions = taskRow.querySelector('.actions');
                if (actions) {
                    actions.style.display = 'table-cell';
                    if (firstActionsHeader) firstActionsHeader.style.display = 'table-cell';
                }
            } else {
                taskRow.style.display = 'none';
                document.querySelectorAll('.sub-task-' + taskId).forEach(function(subRow) {
                    subRow.style.display = 'none';
                });
            }
        });
    });
});
</script>