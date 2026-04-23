<?php
session_start();

// Helper
function e($value)
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function formatDateFr($date)
{
    if (empty($date)) {
        return '—';
    }

    $dt = new DateTime($date);

    $formatter = new IntlDateFormatter(
        'fr_FR',
        IntlDateFormatter::MEDIUM,
        IntlDateFormatter::SHORT
    );

    return $formatter->format($dt);
}


$currentUserGroup = $_SESSION['group'] ?? '';


// State counts 
$stateCounts = [0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0];
foreach ($tasks as $t) {
    $state = (int) ($t['is_completed'] ?? 0);
    if (isset($stateCounts[$state]))
        $stateCounts[$state]++;
}
$totalTasks = array_sum($stateCounts);

// Colors for APPRO/RETOUR
$labels = [0 => 'Envoyé', 4 => 'En achat', 5 => 'En sous-traitance', 1 => 'Traitement', 2 => 'Livré', 3 => 'Soldé'];

// Verif stock labels
$verifStockLabels = [
    0 => 'Envoyé',
    1 => 'Traitement',
    2 => 'OK',
    3 => 'NOK',
    4 => 'Sans CC',
    5 => 'Avec CC',
    6 => 'Form1',
    7 => 'Soldé'
];

$stateClass = [
    0 => 'blue',
    1 => 'red',
    2 => 'orange',
    3 => 'yellow',
    4 => 'indigo',
    5 => 'pink',
    6 => 'primary',
    7 => 'secondary'
];

// WORKFLOW for APPRO/RETOUR
$transitions = [
    0 => [1],
    1 => [2, 4, 5],
    2 => [3],
    3 => [],
    4 => [1],
    5 => [1]
];

// WORKFLOW for VERIF STOCK
$verifStockTransitions = [
    0 => [1],
    1 => [2, 3],
    2 => [4, 5, 6],
    3 => [7],
    4 => [7],
    5 => [7],
    6 => [7],
    7 => []
];

// Filters
$activeStates = $_GET['states'] ?? [];
if (!is_array($activeStates))
    $activeStates = [$activeStates];
$activeStates = array_map('intval', $activeStates);

// Type filters
$activeTypes = $_GET['types'] ?? [];
if (!is_array($activeTypes)) {
    $activeTypes = [$activeTypes];
}
$activeTypes = array_map('strval', $activeTypes);

// Filter tasks
$tasks = array_filter($tasks, function ($task) use ($activeStates, $activeTypes) {

    $stateMatch = empty($activeStates)
        || in_array((int) $task['is_completed'], $activeStates);

    $hasAppro = !empty($task['appro']);
    $hasRetour = !empty($task['retour']);
    $hasVerifStock = !empty($task['verif_stock']);

    $typeMatch = empty($activeTypes)
        || (in_array('appro', $activeTypes) && $hasAppro)
        || (in_array('retour', $activeTypes) && $hasRetour)
        || (in_array('verif_stock', $activeTypes) && $hasVerifStock);

    return $stateMatch && $typeMatch;
});
?>

<?php include __DIR__ . '/../../../public/navbar.php'; ?>

<div class="container-fluid px-3 py-4">

    <!-- Filters -->
    <div class="d-flex align-items-center gap-2 flex-wrap">

        <span class="text-muted small me-2">Filtres :</span>

        <!-- TYPE FILTERS -->
        <div class="d-flex gap-2 pe-2" style="border-right:2px solid #dee2e6;">
            <?php
            $typeLabels = [
                'appro' => 'APPRO',
                'retour' => 'RETOUR',
                'verif_stock' => 'VERIF STOCK'
            ];

            $typeColors = [
                'appro' => 'primary',
                'retour' => 'warning',
                'verif_stock' => 'success'
            ];

            foreach ($typeLabels as $type => $label):
                $isActive = in_array($type, $activeTypes);
                $newTypes = $activeTypes;

                if ($isActive) {
                    $newTypes = array_diff($activeTypes, [$type]);
                } else {
                    $newTypes[] = $type;
                }

                $query = http_build_query([
                    'states' => $activeStates,
                    'types' => $newTypes
                ]);
                ?>
                <a href="?<?= e($query) ?>"
                    class="btn btn-sm <?= $isActive ? 'btn-' . $typeColors[$type] : 'btn-outline-' . $typeColors[$type] ?>">
                    <?= e($label) ?>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- STATE FILTERS -->
        <div class="d-flex gap-2 flex-wrap">
            <?php foreach ($labels as $state => $label):
                $count = $stateCounts[$state] ?? 0;
                $isActive = in_array($state, $activeStates);
                $newStates = $activeStates;

                if ($isActive) {
                    $newStates = array_diff($activeStates, [$state]);
                } else {
                    $newStates[] = $state;
                }

                $query = http_build_query([
                    'states' => $newStates,
                    'types' => $activeTypes
                ]);
                ?>
                <a href="?<?= e($query) ?>"
                    class="btn btn-sm <?= $isActive ? 'btn-' . $stateClass[$state] : 'btn-outline-' . $stateClass[$state] ?> d-flex align-items-center gap-1">
                    <span><?= e($label) ?></span>
                    <span class="badge bg-light text-dark"><?= $count ?></span>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- TOTAL / RESET -->
        <div class="ms-2">
            <a href="?" class="btn btn-sm btn-outline-dark">
                <?= $totalTasks ?> total
            </a>
        </div>

    </div>

    <!-- Divider for second filter row -->
    <hr class="my-3">

    <!-- VERIF STOCK STATE FILTERS (second row) -->
    <div class="d-flex align-items-center gap-2 flex-wrap mt-2">
        <span class="text-muted small me-2">Filtres VERIF STOCK :</span>

        <div class="d-flex gap-2 flex-wrap">
            <?php
            $verifStockStateKeys = [0, 1, 2, 3, 4, 5, 6, 7];
            foreach ($verifStockStateKeys as $state):
                $label = $verifStockLabels[$state];
                $count = $stateCounts[$state] ?? 0;
                $isActive = in_array($state, $activeStates);
                $newStates = $activeStates;

                if ($isActive) {
                    $newStates = array_diff($activeStates, [$state]);
                } else {
                    $newStates[] = $state;
                }

                $query = http_build_query([
                    'states' => $newStates,
                    'types' => $activeTypes
                ]);
                ?>
                <a href="?<?= e($query) ?>"
                    class="btn btn-sm <?= $isActive ? 'btn-' . $stateClass[$state] : 'btn-outline-' . $stateClass[$state] ?> d-flex align-items-center gap-1">
                    <span><?= e($label) ?></span>
                    <span class="badge bg-light text-dark"><?= $count ?></span>
                </a>
            <?php endforeach; ?>
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
                        <th>Complétion</th>
                        <th>Demandeur</th>
                        <th class="actions-header" style="">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($tasks as $taskIndex => $task): ?>
                        <?php
                        $approList = $task['appro'] ?? [];
                        $retourList = $task['retour'] ?? [];
                        $verifStockList = $task['verif_stock'] ?? [];

                        $s = (int) ($task['is_completed'] ?? 0);
                        $isVerifStock = !empty($verifStockList);

                        // Use appropriate transitions and labels based on task type
                        $currentTransitions = $isVerifStock ? $verifStockTransitions : $transitions;
                        $currentLabels = $isVerifStock ? $verifStockLabels : $labels;
                        $currentStateLabel = $currentLabels[$s] ?? $labels[$s] ?? 'Unknown';

                        $canEdit = false;
                        $canSetState = false;

                        if ($currentUserGroup === 'admin') {
                            $canEdit = true;
                            $canSetState = true;
                        } elseif ($currentUserGroup === 'magasin' && !$isVerifStock && in_array($s, [0, 1, 2, 4, 5])) {
                            $canSetState = true;
                        } elseif ($isVerifStock && $currentUserGroup === 'magasin') {
                            $canSetState = true;
                        } elseif ($s === 0 && !in_array($currentUserGroup, ['admin', 'magasin'])) {
                            $canEdit = true;
                        } elseif (!in_array($currentUserGroup, ['admin', 'magasin']) && in_array($s, [3])) {
                            $canSetState = true;
                        }

                        // For verif_stock, check if user can use button 7 (Soldé)
                        $canUseState7 = false;
                        if ($isVerifStock) {
                            if ($currentUserGroup === 'admin') {
                                $canUseState7 = true;
                            } elseif (in_array($currentUserGroup, explode(',', $project['groups'] ?? ''))) {
                                $canUseState7 = true;
                            }
                        }

                        $subIndex = 0;
                        $taskId = (int) $task['id'];

                        $nextStates = $currentTransitions[$s] ?? [];
                        $nextColor = !empty($nextStates) ? ($stateClass[$nextStates[0]] ?? 'secondary') : 'secondary';
                        ?>

                        <!-- MAIN TASK ROW -->
                        <tr style="border-top:2px solid #dee2e6; cursor:pointer;" class="task-row"
                            data-task="<?= $taskId ?>">

                            <td class="p-2 fw-semibold"><?= $taskId ?> </td>

                            <td class="p-2 badgeType">
                                <?php if (!empty($approList)): ?>
                                    <span class="badge bg-primary">APPRO</span>
                                <?php elseif (!empty($retourList)): ?>
                                    <span class="badge bg-warning text-dark">RETOUR</span>
                                <?php elseif (!empty($verifStockList)): ?>
                                    <span class="badge bg-success">VERIF STOCK</span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>

                            <td class="p-2 task-description">
                                <div class="fw-semibold task-title">
                                    <?= e($task['title']) ?>
                                </div>

                                <div class="small description" style="display:block; font-size:1rem;">
                                    <?= e($task['description']) ?>
                                </div>
                            </td>

                            <td class="p-2 badgeState">
                                <span class="badge bg-<?= $stateClass[$s] ?>">
                                    <?= e($currentStateLabel) ?>
                                </span>
                            </td>

                            <td class="small text-muted">
                                <?= e(formatDateFr($task['created_at'])) ?>
                            </td>

                            <td class="small text-muted">
                                <?= (!isset($task['due_date']) || $task['due_date'] === '' || $task['due_date'] === null)
                                    ? '-'
                                    : e(formatDateFr($task['due_date'])) ?>
                            </td>

                            <td class="small text-muted">
                                <?= (!isset($task['completed_at']) || $task['completed_at'] === '' || $task['completed_at'] === null)
                                    ? '-'
                                    : e(formatDateFr($task['completed_at'])) ?>
                            </td>

                            <td class="small text-muted">
                                <?= e($task["user_name"]) ?>
                            </td>

                            <td class="actions">
                                <div class="d-inline-flex flex-nowrap">

                                    <?php if ($canEdit): ?>
                                        <a href="/projects/<?= $project['id'] ?>/tasks/<?= $task['id'] ?>/edit"
                                            class="btn btn-sm btn-primary d-flex align-items-center justify-content-center square-btn m-1">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>
                                    <?php endif; ?>

                                    <?php if ($canEdit && $currentUserGroup === 'admin'): ?>
                                        <form method="POST"
                                            action="/projects/<?= $project['id'] ?>/tasks/<?= $task['id'] ?>/delete"
                                            onsubmit="return confirm('Confirmer la suppression ?');" class="m-0">
                                            <button type="submit"
                                                class="btn btn-sm btn-danger d-flex align-items-center justify-content-center square-btn m-1">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>

                                    <!-- STATE TRANSITIONS -->
                                    <?php if (!empty($nextStates) && $canSetState): ?>
                                        <div class="btn-group">
                                            <?php foreach ($nextStates as $nextState): ?>
                                                <?php
                                                // Skip state 7 (Soldé) for magasin on verif_stock tasks
                                                if ($isVerifStock && $nextState == 7 && !$canUseState7) {
                                                    continue;
                                                }
                                                ?>
                                                <form method="POST"
                                                    action="/projects/<?= $project['id'] ?>/tasks/<?= $task['id'] ?>/mark-completed"
                                                    class="d-inline m-1">

                                                    <input type="hidden" name="state" value="<?= (int) $nextState ?>">

                                                    <button type="submit"
                                                        class="btn btn-sm <?= 'btn-' . ($stateClass[$nextState] ?? 'secondary') ?>">
                                                        <i class="bi bi-arrow-return-right"></i>
                                                        <?= e($currentLabels[$nextState]) ?>
                                                    </button>
                                                </form>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>

                        <!-- SUB-TASK ROWS FOR APPRO -->
                        <?php foreach ($approList as $a): ?>
                            <?php $subIndex++; ?>
                            <tr class="bg-light sub-task-<?= $taskId ?>" style="display: none;">
                                <td class="p-2 fw-semibold"><?= $subIndex ?></td>
                                <td class="task-description" colspan="8">
                                    <div class="small fw-semibold mb-1 text-primary">APPRO</div>
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

                        <!-- SUB-TASK ROWS FOR RETOUR -->
                        <?php foreach ($retourList as $r): ?>
                            <?php $subIndex++; ?>
                            <tr class="bg-light sub-task-<?= $taskId ?>" style="display: none;">
                                <td class="p-2 fw-semibold"><?= $subIndex ?></td>
                                <td class="task-description" colspan="8">
                                    <div class="small fw-semibold mb-1 text-warning">RETOUR</div>
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

                        <!-- SUB-TASK ROWS FOR VERIF STOCK -->
                        <?php foreach ($verifStockList as $v): ?>
                            <?php $subIndex++; ?>
                            <tr class="bg-light sub-task-<?= $taskId ?>" style="display: none;">
                                <td class="p-2 fw-semibold"><?= $subIndex ?></td>
                                <td class="task-description" colspan="8">
                                    <div class="small fw-semibold mb-1 text-success">VERIF STOCK</div>
                                    <div class="d-flex gap-4 mb-1">
                                        <div><strong>PN:</strong> <?= e($v['pn'] ?? '') ?></div>
                                        <div><strong>Qté:</strong> <?= (int) ($v['nb'] ?? 0) ?></div>
                                        <div><strong>Nom:</strong> <?= e($v['name'] ?? '') ?></div>
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
        // Toggle task rows on click
        document.querySelectorAll('.task-row').forEach(function (row) {
            row.addEventListener('click', function (e) {
                // Don't toggle if clicking on a button or form element
                if (e.target.closest('.btn') || e.target.closest('form')) return;

                const taskId = row.dataset.task;
                document.querySelectorAll('.sub-task-' + taskId).forEach(function (subRow) {
                    subRow.style.display = subRow.style.display === 'none' ? 'table-row' : 'none';
                });
            });
        });

        // Search function
        const searchInput = document.getElementById('task-search');
        if (searchInput) {
            searchInput.addEventListener('input', function () {
                const query = searchInput.value.toLowerCase().trim();
                document.querySelectorAll('.task-row').forEach(function (taskRow) {
                    const taskId = taskRow.dataset.task;
                    const title = taskRow.querySelector('td:nth-child(3) .fw-semibold')?.textContent.toLowerCase() || '';
                    const description = taskRow.querySelector('td:nth-child(3) .text-muted')?.textContent.toLowerCase() || '';

                    let match = title.includes(query) || description.includes(query);

                    document.querySelectorAll('.sub-task-' + taskId).forEach(function (subRow) {
                        if (subRow.textContent.toLowerCase().includes(query)) match = true;
                    });

                    if (match || query === '') {
                        taskRow.style.display = '';
                        document.querySelectorAll('.sub-task-' + taskId).forEach(function (subRow) {
                            subRow.style.display = 'none';
                        });
                    } else {
                        taskRow.style.display = 'none';
                        document.querySelectorAll('.sub-task-' + taskId).forEach(function (subRow) {
                            subRow.style.display = 'none';
                        });
                    }
                });
            });
        }
    });
</script>