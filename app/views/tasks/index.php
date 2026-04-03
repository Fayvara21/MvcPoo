<?php
session_start(); // Ensure session is started

// Safe helper
function e($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

// Current user group
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
$filteredTasks = [];
foreach ($tasks as $task) {
    if (empty($activeStates) || in_array((int)$task['is_completed'], $activeStates)) {
        $filteredTasks[] = $task;
    }
}
$tasks = $filteredTasks;
?>

<?php include __DIR__ . '/../../../public/navbar.php'; ?>

<div class="container-fluid py-4">

    <!-- Breadcrumb -->
    <nav class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/projects">Projets</a></li>
            <li class="breadcrumb-item">
                <a href="/projects/<?= (int)$project['id'] ?>"><?= e($project['title']) ?></a>
            </li>
            <li class="breadcrumb-item active">Demandes</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="card mb-4">
        <div class="card-body">
            <h1 class="fw-bold">Demandes vers <?= e($project['title']) ?></h1>
            <?php if (!empty($project['description'])): ?>
                <p class="text-muted"><?= e($project['description']) ?></p>
            <?php endif; ?>
            <a href="/projects/<?= (int)$project['id'] ?>/tasks/create" class="btn btn-primary">+ Nouvelle demande</a>
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
    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>#</th>
                <th>Type</th>
                <th>Titre</th>
                <th>Description</th>
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
                $s = (int)($task['is_completed']);
                $labels = [0=>'Envoyé',1=>'Traitement',2=>'Livré',3=>'Soldé'];
                $statesColor = [0=>'primary',1=>'warning',2=>'info',3=>'success'];

                // --- PERMISSIONS BASED ON SESSION GROUP ---
                $canEdit = false;
                $canSetState = false;

                if ($currentUserGroup === 'admin') {
                    $canEdit = true;
                    $canSetState = true;
                } elseif ($currentUserGroup === 'magasin' && in_array($s + 1, [1,2])) {
                    $canSetState = true;
                } elseif ($s === 0 && $currentUserGroup !== 'magasin' && $currentUserGroup !== 'admin') {
                    // Members of original group can edit tasks with state 0
                    $canEdit = true;
                } elseif ($currentUserGroup !== 'admin' && $currentUserGroup !== 'magasin' && $s + 1 === 3) {
                    // Other groups can set state only to 3
                    $canSetState = true;
                }
            ?>

            <!-- Main task row -->
            <tr class="task-row" data-task-id="<?= (int)$task['id'] ?>" data-task-state="<?= $s ?>">
                <td><?= (int)$task['id'] ?></td>
                <td>
                    <?= $isAppro ? '<span class="badge bg-primary">APPRO</span>' :
                       ($isRetour ? '<span class="badge bg-warning">RETOUR</span>' :
                       '<span class="badge bg-secondary">-</span>') ?>
                </td>
                <td><?= e($task['title']) ?></td>
                <td><?= e($task['description']) ?></td>

                <td>
                    <span class="badge bg-<?= $statesColor[$s] ?>"><?= $labels[$s] ?></span>
                </td>

                <td><?= e($task['created_at']) ?></td>
                <td><?= e($task['due_date']) ?></td>

                <td>
                    <div class="d-flex gap-1 flex-wrap">
                        <!-- Edit button -->
                        <?php if ($canEdit): ?>
                        <a href="/projects/<?= $project['id'] ?>/tasks/<?= $task['id'] ?>/edit" class="btn btn-sm btn-primary">Modifier</a>
                        <?php endif; ?>

                        <!-- Delete button -->
                        <?php if ($canEdit && $currentUserGroup === 'admin'): ?>
                        <form method="POST" action="/projects/<?= $project['id'] ?>/tasks/<?= $task['id'] ?>/delete" onsubmit="return confirm('Confirmer la suppression ?');">
                            <button type="submit" class="btn btn-sm btn-danger">Supprimer</button>
                        </form>
                        <?php endif; ?>

                        <!-- State increment button -->
                        <?php if ($s < 3 && $canSetState): ?>
                        <form method="POST" action="/projects/<?= $project['id'] ?>/tasks/<?= $task['id'] ?>/mark-completed" class="ms-1">
                            <input type="hidden" name="state" value="<?= $s + 1 ?>">
                            <button type="submit" class="btn btn-sm btn-success">
                                Définir comme: <?= $labels[$s + 1] ?>
                            </button>
                        </form>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>

            <!-- Sub-info rows for APPRO -->
            <?php foreach ($approList as $a): ?>
            <tr class="bg-light">
                <td colspan="8">
                    <strong>APPRO</strong> - PN: <?= e($a['pn']) ?> |
                    Q: <?= (int)($a['nb'] ?? 0) ?> |
                    Désignation: <?= e($a['designation']) ?> |
                    Lieu: <?= e($a['location']) ?> |
                    Avion: <?= e($a['plane']) ?> |
                    OF: <?= e($a['of']) ?> |
                    OE: <?= e($a['oe']) ?>
                </td>
            </tr>
            <?php endforeach; ?>

            <!-- Sub-info rows for RETOUR -->
            <?php foreach ($retourList as $r): ?>
            <tr class="bg-light">
                <td colspan="8">
                    <strong>RETOUR</strong> - PN: <?= e($r['PN']) ?> |
                    Q: <?= (int)($r['nb'] ?? 0) ?> |
                    SN: <?= e($r['sn']) ?> |
                    Certif: <?= e($r['certif']) ?>
                </td>
            </tr>
            <?php endforeach; ?>

        <?php endforeach; ?>
        </tbody>
    </table>
</div>
