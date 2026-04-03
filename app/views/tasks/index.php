<?php
session_start();

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
            <a href="/projects/<?= (int)$project['id'] ?>/tasks/create" class="btn btn-primary btn-sm">+ Nouvelle demande</a>
            <input type="text" id="task-search" class="form-control form-control-sm" placeholder="Rechercher titre, OF, Avion, PN" style="max-width:300px;">
            <button id="toggle-all" class="btn btn-sm btn-outline-secondary">Cacher / Montrer tout</button>
        </div>
    </div>

    <!-- Filters -->
    <div class="mb-3 d-flex flex-wrap gap-2">
        <?php 
        $labels = [0=>'Envoyé',1=>'Traitement',2=>'Livré',3=>'Soldé'];
        $filterColors = [0=>'secondary',1=>'warning',2=>'info',3=>'success'];
        foreach ($labels as $state => $label): 
            $count = $stateCounts[$state];
            $isActive = in_array($state, $activeStates);
            $newStates = $activeStates;
            $isActive ? $newStates = array_diff($activeStates, [$state]) : $newStates[] = $state;
            $query = http_build_query(['states' => $newStates]);
        ?>
            <a href="?<?= e($query) ?>" class="btn btn-sm <?= $isActive ? 'btn-'.$filterColors[$state] : 'btn-outline-'.$filterColors[$state] ?>">
                <?= $count ?> <?= e($label) ?>
            </a>
        <?php endforeach; ?>
        <a href="?" class="btn btn-sm btn-outline-dark"><?= $totalTasks ?> total</a>
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

                    $canEdit = $canSetState = false;
                    if ($currentUserGroup === 'admin') {
                        $canEdit = $canSetState = true;
                    } elseif ($currentUserGroup === 'magasin' && in_array($s + 1, [1,2])) {
                        $canSetState = true;
                    } elseif ($s === 0 && !in_array($currentUserGroup, ['admin','magasin'])) {
                        $canEdit = true;
                    } elseif (!in_array($currentUserGroup, ['admin','magasin']) && $s + 1 === 3) {
                        $canSetState = true;
                    }

                    $subIndex = 0;
                    $taskId = (int)$task['id'];
                ?>

                <!-- MAIN TASK ROW -->
                <tr class="task-row" data-task="<?= $taskId ?>" style="border-top:2px solid #dee2e6; cursor:pointer;">
                    <td class="fw-semibold"><?= $taskId ?></td>
                    <td>
                        <?php if (!empty($approList)): ?><span class="badge bg-primary">APPRO</span>
                        <?php elseif (!empty($retourList)): ?><span class="badge bg-dark">RETOUR</span>
                        <?php else: ?><span class="text-muted">-</span><?php endif; ?>
                    </td>
                    <td>
                        <div class="fw-semibold task-title" style="white-space:normal;"><?= e($task['title']) ?></div>
                        <div class="small description" style="white-space:normal;"><?= e($task['description']) ?></div>
                    </td>
                    <td><span class="badge bg-<?= $stateClass[$s] ?>"><?= $labels[$s] ?></span></td>
                    <td class="small text-muted"><?= e($task['created_at']) ?></td>
                    <td class="small text-muted"><?= e($task['due_date']) ?></td>
                    <td class="actions" style="display:none;">
                        <div class="d-flex gap-1 flex-wrap">
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
                    </td>
                </tr>

                <!-- SUB-TASK ROWS -->
                <?php foreach (array_merge($approList, $retourList) as $sub): ?>
                    <?php $subIndex++; ?>
                    <tr class="bg-light sub-task-<?= $taskId ?>" style="display:none;">
                        <td><?= $subIndex ?></td>
                        <td colspan="6">
                            <div class="small sub-text" style="white-space:normal;">
                                <?php foreach ($sub as $k => $v) {
                                    echo "<strong>".e($k).":</strong> ".e($v)." ";
                                } ?>
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

    // Toggle sub-rows on task click
    document.querySelectorAll('.task-row').forEach(row => {
        row.addEventListener('click', () => {
            const taskId = row.dataset.task;
            const actions = row.querySelector('.actions');
            document.querySelectorAll('.sub-task-' + taskId).forEach(sub => {
                sub.style.display = sub.style.display === 'none' ? 'table-row' : 'none';
            });
            if (actions) {
                const visible = actions.style.display !== 'none';
                actions.style.display = visible ? 'none' : 'table-cell';
                if (firstActionsHeader) firstActionsHeader.style.display = visible ? 'none' : 'table-cell';
            }
        });
    });

    // Search function (wrap-independent)
    document.getElementById('task-search').addEventListener('input', function() {
        const query = this.value.toLowerCase();
        document.querySelectorAll('.task-row').forEach(taskRow => {
            const taskId = taskRow.dataset.task;
            let match = taskRow.textContent.toLowerCase().includes(query);
            document.querySelectorAll('.sub-task-' + taskId).forEach(sub => {
                if (sub.textContent.toLowerCase().includes(query)) match = true;
            });
            taskRow.style.display = match ? '' : 'none';
            document.querySelectorAll('.sub-task-' + taskId).forEach(sub => {
                sub.style.display = match ? 'table-row' : 'none';
            });
        });
    });

    // Toggle all sub-tasks independently
    let allVisible = false;
    document.getElementById('toggle-all').addEventListener('click', () => {
        allVisible = !allVisible;
        document.querySelectorAll('tr[class^="sub-task-"]').forEach(sub => {
            sub.style.display = allVisible ? 'table-row' : 'none';
        });
    });
});
</script>