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
        IntlDateFormatter::SHORT,
    );

    return $formatter->format($dt);
}

$currentUserGroup = $_SESSION['group'] ?? '';

// Store original tasks before filtering
$originalTasks = $tasks;

// ============================================================
// 1. INITIALIZE FILTERS FROM URL (must be done first)
// ============================================================
$activeApproStates = $_GET['appro_states'] ?? [];
if (!is_array($activeApproStates)) {
    $activeApproStates = [$activeApproStates];
}
$activeApproStates = array_map('intval', $activeApproStates);

$activeRetourStates = $_GET['retour_states'] ?? [];
if (!is_array($activeRetourStates)) {
    $activeRetourStates = [$activeRetourStates];
}
$activeRetourStates = array_map('intval', $activeRetourStates);

// For VERIF STOCK
$activeVerifStates = $_GET['verif_states'] ?? [];
if (!is_array($activeVerifStates)) {
    $activeVerifStates = [$activeVerifStates];
}
$activeVerifStates = array_map('intval', $activeVerifStates);

// For EXPEDITION
$activeExpeditionStates = $_GET['expedition_states'] ?? [];
if (!is_array($activeExpeditionStates)) {
    $activeExpeditionStates = [$activeExpeditionStates];
}
$activeExpeditionStates = array_map('intval', $activeExpeditionStates);

// Ensure $activeTypes is always an array, even when not present in URL
$activeTypes = $_GET['types'] ?? [];
if (!is_array($activeTypes)) {
    $activeTypes = [$activeTypes];
}
// Remove empty values
$activeTypes = array_filter($activeTypes, function ($value) {
    return $value !== '';
});

$activeTypes = array_map('strval', $activeTypes);

// ============================================================
// 2. CALCULATE COUNTS FOR EACH STATE (for badge display)
// ============================================================
$approCounts = [0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
$retourCounts = [0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
$verifStockCounts = [0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0];
$expeditionCounts = [0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0];

foreach ($originalTasks as $t) {
    $state = (int) ($t['is_completed'] ?? 0);
    $hasAppro = !empty($t['appro']);
    $hasRetour = !empty($t['retour']);
    $hasVerifStock = !empty($t['verif_stock']);
    $hasExpedition = !empty($t['expedition']);

    if ($hasVerifStock) {
        if (isset($verifStockCounts[$state])) {
            $verifStockCounts[$state]++;
        }
    } elseif ($hasAppro) {
        if (isset($approCounts[$state])) {
            $approCounts[$state]++;
        }
    } elseif ($hasRetour) {
        if (isset($retourCounts[$state])) {
            $retourCounts[$state]++;
        }
    } elseif ($hasExpedition) {
        if (isset($expeditionCounts[$state])) {
            $expeditionCounts[$state]++;
        }
    }
}

$totalAppro = array_sum($approCounts);
$totalRetour = array_sum($retourCounts);
$totalVerifStock = array_sum($verifStockCounts);
$totalExpedition = array_sum($expeditionCounts);
$totalTasks = $totalAppro + $totalRetour + $totalVerifStock + $totalExpedition;

// ============================================================
// 3. LABELS & WORKFLOWS
// ============================================================
// Colors for APPRO/RETOUR
$labels = [0 => 'Envoyé', 4 => 'En achat', 5 => 'En sous-traitance', 1 => 'Traitement', 2 => 'Livré', 3 => 'Soldé'];

// Verif stock labels
$verifStockLabels = [
    0 => 'Envoyé',
    1 => 'Traitement',
    2 => 'OK',
    3 => 'NOK',
    4 => 'OK Sans CC',
    5 => 'OK Avec CC',
    6 => 'OK Form1',
    7 => 'Soldé',
];

$stateClass = [
    0 => 'blue',
    1 => 'red',
    2 => 'orange',
    3 => 'yellow',
    4 => 'indigo',
    5 => 'pink',
    6 => 'primary',
    7 => 'secondary',
];

// WORKFLOW for APPRO/RETOUR
$transitions = [
    0 => [1],
    1 => [2, 4, 5],
    2 => [3],
    3 => [],
    4 => [1],
    5 => [1],
];

// WORKFLOW for VERIF STOCK
$verifStockTransitions = [
    0 => [1],
    1 => [2, 3],
    2 => [4, 5, 6],
    3 => [],
    4 => [],
    5 => [],
    6 => [],
    7 => [],
];

// ============================================================
// 4. FILTER TASKS BASED ON ACTIVE FILTERS
// ============================================================
$tasks = array_filter($originalTasks, function ($task) use ($activeApproStates, $activeRetourStates, $activeVerifStates, $activeTypes) {
    $state = (int) $task['is_completed'];
    $hasAppro = !empty($task['appro']);
    $hasRetour = !empty($task['retour']);
    $hasVerifStock = !empty($task['verif_stock']);
    $hasExpedition = !empty($task['expedition']);

    // Determine specific task type
    $specificType = null;
    if ($hasVerifStock) {
        $specificType = 'verif_stock';
    } elseif ($hasAppro) {
        $specificType = 'appro';
    } elseif ($hasRetour) {
        $specificType = 'retour';
    } elseif ($hasExpedition) {
        $specificType = 'expedition';
    }

    // Type match logic - if no active types, show all
    $typeMatch = true;
    if (!empty($activeTypes)) {
        $typeMatch = in_array($specificType, $activeTypes);
    }

    if (!$typeMatch) {
        return false;
    }

    // State match - completely separate by type
    $stateMatch = true;

    if ($hasVerifStock) {
        // VERIF STOCK task
        if (!empty($activeVerifStates)) {
            $stateMatch = in_array($state, $activeVerifStates);
        }
    } elseif ($hasAppro) {
        // APPRO task
        if (!empty($activeApproStates)) {
            $stateMatch = in_array($state, $activeApproStates);
        }
    } elseif ($hasRetour) {
        // RETOUR task
        if (!empty($activeRetourStates)) {
            $stateMatch = in_array($state, $activeRetourStates);
        }
    } elseif ($hasExpedition) {
        // expedition task
        if (!empty($activeExpeditionStates)) {
            $stateMatch = in_array($state, $activeExpeditionStates);
        }
    }

    return $typeMatch && $stateMatch;
});

// Determine visibility of state filter rows - HIDDEN BY DEFAULT
$showApproFilters = !empty($activeTypes) && (in_array('appro', $activeTypes));
$showRetourFilters = !empty($activeTypes) && (in_array('retour', $activeTypes));
$showVerifStockFilters = !empty($activeTypes) && in_array('verif_stock', $activeTypes);
$showExpeditionFilters = !empty($activeTypes) && in_array('expedition', $activeTypes);



// ============================================================
// 5. PAGINATION SETUP
// ============================================================
$itemsPerPage = 20; // Number of tasks per page
$currentPage = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;

// Get total number of filtered tasks
$totalFilteredTasks = count($tasks);

// Calculate total pages
$totalPages = ceil($totalFilteredTasks / $itemsPerPage);

// Adjust current page if it exceeds total pages
if ($currentPage > $totalPages && $totalPages > 0) {
    $currentPage = $totalPages;
}

// Calculate offset
$offset = ($currentPage - 1) * $itemsPerPage;

// Slice the tasks array for current page
$paginatedTasks = array_slice($tasks, $offset, $itemsPerPage);

// Build pagination URL parameters (preserve all filters)
$paginationParams = $_GET;
unset($paginationParams['page']);
$baseUrl = '?' . http_build_query($paginationParams);



?>

<?php include __DIR__ . '/../../../public/navbar.php'; ?>

<div class="container-fluid custom-container px-3 py-4">

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">

            <!-- Header -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
                <div>
                    <h2 class="fw-semibold mb-1">
                        Demandes - <?= e($project['title']) ?>
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
            <div class="d-flex flex-column gap-3">

                <!-- First row: Type filters + Total (always visible) -->
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="text-muted small me-2">Filtres :</span>

                    <div class="d-flex gap-2 pe-2" style="border-right:2px solid #dee2e6;">
                        <?php
                        $typeLabels = ['appro' => 'APPRO', 'retour' => 'RETOUR', 'verif_stock' => 'VERIF STOCK', 'expedition' => 'EXPEDITION'];
                        $typeColors = ['appro' => 'primary', 'retour' => 'warning', 'verif_stock' => 'success', 'expedition' => 'danger'];

                        foreach ($typeLabels as $type => $label):
                            $isActive = in_array($type, $activeTypes);
                            $newTypes = $activeTypes;
                            if ($isActive) {
                                $newTypes = array_diff($activeTypes, [$type]);
                            } else {
                                $newTypes[] = $type;
                            }
                            // Build query preserving both state filters
                            $query = http_build_query([
                                'appro_states' => $activeApproStates,
                                'retour_states' => $activeRetourStates,
                                'verif_states' => $activeVerifStates,
                                'expedition_states' => $activeExpeditionStates,
                                'types' => $newTypes
                            ]);
                            switch ($type) {
                                case 'appro':
                                    $count = $totalAppro;
                                    break;
                                case 'retour':
                                    $count = $totalRetour;
                                    break;
                                case 'verif_stock':
                                    $count = $totalVerifStock;
                                    break;
                                case 'expedition':
                                    $count = $totalExpedition;
                                    break;
                                default:
                                    $count = 0;
                            }
                            ?>
                            <a href="?<?= e($query) ?>"
                                class="btn btn-sm <?= $isActive ? 'btn-' . $typeColors[$type] : 'btn-outline-' . $typeColors[$type] ?> d-flex align-items-center gap-1">
                                <?= e($label) ?>
                                <span class="badge bg-light text-dark"><?= $count ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>

                    <div class="ms-2">
                        <a href="?" class="btn btn-sm btn-outline-dark"><?= $totalTasks ?> total</a>
                    </div>
                </div>

                <!-- Second row: APPRO State Filters (hidden by default, appears only when APPRO type is selected) -->
                <div class="d-flex align-items-center gap-2 flex-wrap" <?= $showApproFilters ? '' : 'style="display: none !important;"' ?>>
                    <span class="text-muted small me-2">États APPRO :</span>
                    <div class="d-flex gap-2 flex-wrap">
                        <?php
                        $approStatesList = [0, 1, 2, 3, 4, 5];
                        foreach ($approStatesList as $state):
                            $label = $labels[$state] ?? '?';
                            $count = $approCounts[$state] ?? 0;
                            $isActive = in_array($state, $activeApproStates);
                            $newApproStates = $activeApproStates;
                            if ($isActive) {
                                $newApproStates = array_diff($activeApproStates, [$state]);
                            } else {
                                $newApproStates[] = $state;
                            }
                            // FIX: Use 'appro_states' parameter instead of 'ar_states'
                            $query = http_build_query([
                                'appro_states' => $newApproStates,
                                'types' => $activeTypes,
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

                <!-- Third row: RETOUR State Filters (hidden by default, appears only when RETOUR type is selected) -->
                <div class="d-flex align-items-center gap-2 flex-wrap" <?= $showRetourFilters ? '' : 'style="display: none !important;"' ?>>
                    <span class="text-muted small me-2">États Retour :</span>
                    <div class="d-flex gap-2 flex-wrap">
                        <?php
                        $retourStatesList = [0, 1, 2, 3, 4, 5];
                        foreach ($retourStatesList as $state):
                            $label = $labels[$state] ?? '?';
                            $count = $retourCounts[$state] ?? 0;
                            $isActive = in_array($state, $activeRetourStates);
                            $newRetourStates = $activeRetourStates;
                            if ($isActive) {
                                $newRetourStates = array_diff($activeRetourStates, [$state]);
                            } else {
                                $newRetourStates[] = $state;
                            }
                            // FIX: Use 'retour_states' parameter
                            $query = http_build_query([
                                'retour_states' => $newRetourStates,
                                'types' => $activeTypes,
                            ]);
                            ?>
                            <a href="?<?= e($query) ?>"
                                class="btn btn-sm <?= $isActive ? 'btn-' . $stateClass[$state] : 'btn-outline-' . $stateClass[$state] ?> d-flex align-items-center gap-1">
                                <span>
                                    <?= e($label) ?>
                                </span>
                                <span class="badge bg-light text-dark">
                                    <?= $count ?>
                                </span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>



                <!-- Fourth row: VERIF STOCK State Filters (hidden by default, appears only when VERIF STOCK type is selected) -->
                <div class="d-flex align-items-center gap-2 flex-wrap" <?= $showVerifStockFilters ? '' : 'style="display: none !important;"' ?>>
                    <span class="text-muted small me-2">États VERIF STOCK :</span>
                    <div class="d-flex gap-2 flex-wrap">
                        <?php
                        $verifStatesList = [0, 1, 2, 3, 4, 5, 6, 7];
                        foreach ($verifStatesList as $state):
                            $label = $verifStockLabels[$state] ?? '?';
                            $count = $verifStockCounts[$state] ?? 0;
                            $isActive = in_array($state, $activeVerifStates);
                            $newVerifStates = $activeVerifStates;
                            if ($isActive) {
                                $newVerifStates = array_diff($activeVerifStates, [$state]);
                            } else {
                                $newVerifStates[] = $state;
                            }
                            $query = http_build_query([
                                'verif_states' => $newVerifStates,
                                'types' => $activeTypes,
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

                <!-- Fourth row: EXPEDITION State Filters (hidden by default, appears only when EXPEDITION type is selected) -->
                <div class="d-flex align-items-center gap-2 flex-wrap" <?= $showExpeditionFilters ? '' : 'style="display: none !important;"' ?>>
                    <span class="text-muted small me-2">États EXPEDITION :</span>
                    <div class="d-flex gap-2 flex-wrap">
                        <?php
                        $expeditionStatesList = [0, 1, 2, 3, 4, 5];
                        foreach ($expeditionStatesList as $state):
                            $label = $labels[$state] ?? '?';
                            $count = $expeditionCounts[$state] ?? 0;
                            $isActive = in_array($state, $activeExpeditionStates);
                            $newExpeditionStates = $activeExpeditionStates;
                            if ($isActive) {
                                $newExpeditionStates = array_diff($activeExpeditionStates, [$state]);
                            } else {
                                $newExpeditionStates[] = $state;
                            }
                            $query = http_build_query([
                                'expedition_states' => $newExpeditionStates,
                                'types' => $activeTypes,
                            ]);
                            ?>
                            <a href="?<?= e($query) ?>"
                                class="btn btn-sm <?= $isActive ? 'btn-' . $stateClass[$state] : 'btn-outline-' . $stateClass[$state] ?> d-flex align-items-center gap-1">
                                <span>
                                    <?= e($label) ?>
                                </span>
                                <span class="badge bg-light text-dark">
                                    <?= $count ?>
                                </span>
                            </a>
                        <?php endforeach; ?>
                    </div>
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
                        <th>Complétion</th>
                        <th>Demandeur</th>
                        <th class="actions-header">Actions</th>
                    </tr>
                </thead>
                <tbody>

                    <?php foreach ($paginatedTasks as $taskIndex => $task): ?>
                        <?php
                        $approList = $task['appro'] ?? [];
                        $retourList = $task['retour'] ?? [];
                        $verifStockList = $task['verif_stock'] ?? [];
                        $expeditionList = $task['expedition'] ?? [];

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
                        } elseif (!$isVerifStock) {
                            if ($currentUserGroup === 'magasin' && in_array($s, [0, 1, 2, 4, 5])) {
                                $canSetState = true;
                            }
                        } elseif ($currentUserGroup === 'magasin') {
                            $canSetState = true;
                        } elseif ($s === 0 && $currentUserGroup === 'magasin') {
                            $canEdit = true;
                        } elseif ($currentUserGroup === 'magasin' && in_array($s, [3])) {
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

                            <td class="p-2 fw-semibold"><?= $taskId ?></td>

                            <td class="p-2 badgeType">
                                <?php if (!empty($approList)): ?>
                                    <span class="badge bg-primary">APPRO</span>
                                <?php elseif (!empty($retourList)): ?>
                                    <span class="badge bg-warning text-dark">RETOUR</span>
                                <?php elseif (!empty($verifStockList)): ?>
                                    <span class="badge bg-success">VERIF STOCK</span>
                                <?php elseif (!empty($expeditionList)): ?>
                                    <span class="badge bg-danger">EXPEDITION</span>
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

                            <td class="small text-muted"><?= e(formatDateFr($task['created_at'])) ?></td>
                            <td class="small text-muted"><?= $task['due_date'] ? e(formatDateFr($task['due_date'])) : '-' ?>
                            </td>
                            <td class="small text-muted">
                                <?= $task['completed_at'] ? e(formatDateFr($task['completed_at'])) : '-' ?>
                            </td>
                            <td class="small text-muted"><?= e($task["user_name"]) ?></td>

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

                                    <?php if (!empty($nextStates) && $canSetState): ?>
                                        <div class="d-inline-flex ms-2">
                                            <select class="form-select form-select-sm state-select"
                                                data-task-id="<?= $task['id'] ?>" data-project-id="<?= $project['id'] ?>"
                                                style="width: auto;">
                                                <option value="">Changer l'état...</option>
                                                <?php foreach ($nextStates as $nextState): ?>
                                                    <?php if ($isVerifStock && $nextState == 7 && !$canUseState7) {
                                                        continue;
                                                    } ?>
                                                    <option value="<?= (int) $nextState ?>"
                                                        data-state-label="<?= e($currentLabels[$nextState]) ?>"
                                                        class="bg-<?= $stateClass[$nextState] ?? 'secondary' ?> text-white">
                                                        <?= e($currentLabels[$nextState]) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </td>

                            <script>
                                document.addEventListener('DOMContentLoaded', function () {
                                    const stateSelects = document.querySelectorAll('.state-select');

                                    stateSelects.forEach(select => {
                                        select.addEventListener('change', function () {
                                            const selectedOption = this.options[this.selectedIndex];
                                            const stateValue = this.value;

                                            if (!stateValue) return;

                                            const stateLabel = selectedOption.dataset.stateLabel;
                                            const taskId = this.dataset.taskId;
                                            const projectId = this.dataset.projectId;

                                            // Confirmation prompt
                                            const confirmMessage = `Confirmer le passage à l'état "${stateLabel}" ?`;

                                            if (confirm(confirmMessage)) {
                                                // Create and submit form
                                                const form = document.createElement('form');
                                                form.method = 'POST';
                                                form.action = `/projects/${projectId}/tasks/${taskId}/mark-completed`;

                                                const input = document.createElement('input');
                                                input.type = 'hidden';
                                                input.name = 'state';
                                                input.value = stateValue;

                                                form.appendChild(input);
                                                document.body.appendChild(form);
                                                form.submit();
                                            } else {
                                                // Reset select to default option
                                                this.value = '';
                                            }
                                        });
                                    });
                                });
                            </script>

                        </tr>

                        <!-- SUB-TASK ROWS FOR APPRO -->
                        <?php foreach ($approList as $a): ?>
                            <?php $subIndex++; ?>
                            <tr class="bg-light sub-task-<?= $taskId ?>" style="display: table-row;">
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
                            <tr class="bg-light sub-task-<?= $taskId ?>" style="display: table-row;">
                                <td class="p-2 fw-semibold"><?= $subIndex ?></td>
                                <td class="task-description" colspan="8">
                                    <div class="small fw-semibold mb-1 text-warning">RETOUR</div>
                                    <div class="d-flex gap-4 mb-1">
                                        <div><strong>PN:</strong> <?= e($r['pn'] ?? '') ?></div>
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
                            <tr class="bg-light sub-task-<?= $taskId ?>" style="display: table-row;">
                                <td class="p-2 fw-semibold"><?= $subIndex ?></td>
                                <td class="task-description" colspan="8">
                                    <div class="small fw-semibold mb-1 text-success">VERIF STOCK</div>
                                    <div class="d-flex gap-4 mb-1">
                                        <div><strong>PN:</strong> <?= e($v['pn'] ?? '') ?></div>
                                        <div><strong>Qté:</strong> <?= (int) ($v['nb'] ?? 0) ?></div>
                                        <div><strong>Nom:</strong> <?= e($v['name'] ?? '') ?></div>
                                    </div>
                                    <div class="small d-flex gap-3">
                                        <div><strong>Emplacement:</strong> <?= e($v['location'] ?? '') ?></div>
                                        <div><strong>Remarques:</strong> <?= e($v['remarks'] ?? '') ?></div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        <!-- SUB-TASK ROWS FOR EXPEDITION -->
                        <?php foreach ($expeditionList as $t): ?>
                            <?php $subIndex++; ?>
                            <tr class="bg-light sub-task-<?= $taskId ?>" style="display: table-row;">
                                <td class="p-2 fw-semibold">
                                    <?= $subIndex ?>
                                </td>
                                <td class="task-description" colspan="8">
                                    <div class="small fw-semibold mb-1 text-danger">EXPEDITION</div>
                                    <div class="d-flex gap-4 mb-1">
                                        <div><strong>PN:</strong> <?= e($t['pn'] ?? '') ?></div>
                                        <div><strong>Qté:</strong> <?= (int) ($t['nb'] ?? 0) ?></div>
                                        <div><strong>Nom:</strong> <?= e($t['name'] ?? '') ?></div>
                                    </div>
                                    <div class="small d-flex gap-3">
                                        <div><strong>Emplacement:</strong> <?= e($t['location'] ?? '') ?></div>
                                        <div><strong>N° Commande:</strong> <?= e($t['order_nb'] ?? '') ?></div>
                                        <div><strong>Destinataire:</strong> <?= e($t['destination'] ?? '') ?></div>
                                        <div><strong>Compte:</strong> <?= e($t['account'] ?? '') ?></div>
                                        <div>
                                            <strong>3rd Party:</strong>
                                            <input type="checkbox" disabled <?= !empty($t['third_party']) ? 'checked' : '' ?>>
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

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
        <div class="d-flex justify-content-center align-items-center mt-4">
            <nav aria-label="Pagination des demandes">
                <ul class="pagination mb-0">
                    <!-- Previous button -->
                    <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= $currentPage > 1 ? $baseUrl . '&page=' . ($currentPage - 1) : '#' ?>"
                            aria-label="Précédent">
                            <span aria-hidden="true">&laquo;</span>
                            <span class="visually-hidden">Précédent</span>
                        </a>
                    </li>

                    <!-- Page numbers -->
                    <?php
                    // Calculate page range to show (show up to 7 pages)
                    $startPage = max(1, $currentPage - 3);
                    $endPage = min($totalPages, $currentPage + 3);

                    // Add first page if not in range
                    if ($startPage > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?= $baseUrl . '&page=1' ?>">1</a>
                        </li>
                        <?php if ($startPage > 2): ?>
                            <li class="page-item disabled"><span class="page-link">...</span></li>
                        <?php endif; ?>
                    <?php endif; ?>

                    <!-- Page numbers in range -->
                    <?php for ($page = $startPage; $page <= $endPage; $page++): ?>
                        <li class="page-item <?= $page == $currentPage ? 'active' : '' ?>">
                            <a class="page-link" href="<?= $baseUrl . '&page=' . $page ?>">
                                <?= $page ?>
                            </a>
                        </li>
                    <?php endfor; ?>

                    <!-- Add last page if not in range -->
                    <?php if ($endPage < $totalPages): ?>
                        <?php if ($endPage < $totalPages - 1): ?>
                            <li class="page-item disabled"><span class="page-link">...</span></li>
                        <?php endif; ?>
                        <li class="page-item">
                            <a class="page-link" href="<?= $baseUrl . '&page=' . $totalPages ?>">
                                <?= $totalPages ?>
                            </a>
                        </li>
                    <?php endif; ?>

                    <!-- Next button -->
                    <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
                        <a class="page-link"
                            href="<?= $currentPage < $totalPages ? $baseUrl . '&page=' . ($currentPage + 1) : '#' ?>"
                            aria-label="Suivant">
                            <span aria-hidden="true">&raquo;</span>
                            <span class="visually-hidden">Suivant</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Items per page info -->
            <div class="ms-3 text-muted small">
                Affichage <?= $offset + 1 ?> à <?= min($offset + $itemsPerPage, $totalFilteredTasks) ?>
                sur <?= $totalFilteredTasks ?> demandes
            </div>
        </div>
    <?php endif; ?>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Toggle task rows on click
        document.querySelectorAll('.task-row').forEach(function (row) {
            row.addEventListener('click', function (e) {
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
                    const title = taskRow.querySelector('.task-title')?.textContent.toLowerCase() || '';
                    const description = taskRow.querySelector('.description')?.textContent.toLowerCase() || '';

                    let match = title.includes(query) || description.includes(query);

                    document.querySelectorAll('.sub-task-' + taskId).forEach(function (subRow) {
                        if (subRow.textContent.toLowerCase().includes(query)) match = true;
                    });

                    if (match || query === '') {
                        taskRow.style.display = '';
                        document.querySelectorAll('.sub-task-' + taskId).forEach(function (subRow) {
                            subRow.style.display = 'table-row';
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