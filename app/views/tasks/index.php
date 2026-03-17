<?php

$stateCounts = [
    0 => 0,
    1 => 0,
    2 => 0,
    3 => 0
];

foreach ($tasks as $t) {
    $state = (int)$t['is_completed'];
    if (isset($stateCounts[$state])) {
        $stateCounts[$state]++;
    }
}

$totalTasks = array_sum($stateCounts);
?>

<?php include __DIR__ . '/../../../public/navbar.php'; ?>

<div class="container-fluid py-4">
    <!-- Header with breadcrumb navigation -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/projects" class="text-decoration-none">Projets</a></li>
            <li class="breadcrumb-item"><a href="/projects/<?= $project['id'] ?>" class="text-decoration-none"><?= htmlspecialchars($project['title']) ?></a></li>
            <li class="breadcrumb-item active" aria-current="page">Demandes</li>
        </ol>
    </nav>

    <!-- Header Card -->
    <div class="card border-0 bg-gradient bg-light mb-4">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <!-- Title with project context -->
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill">
                            <i class="bi bi-file-text me-1"></i>Demandes
                        </span>
                        <span class="text-muted">•</span>
                        <span class="text-muted">Projet #<?= $project['id'] ?></span>
                    </div>
                    
                    <h1 class="display-6 fw-bold mb-2">
                        Demandes vers <?= htmlspecialchars($project['title']) ?>
                    </h1>
                    
                    <!-- Project metadata if available -->
                    <?php if (!empty($project['description'])): ?>
                        <p class="text-muted mb-0">
                            <i class="bi bi-info-circle me-2"></i><?= htmlspecialchars($project['description']) ?>
                        </p>
                    <?php endif; ?>
                </div>
                
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <!-- Enhanced create button -->
                    <a href="/projects/<?= $project['id'] ?>/tasks/create" 
                       class="btn btn-primary btn-lg rounded-pill px-4 shadow-sm">
                        <i class="bi bi-plus-circle me-2"></i>
                        Nouvelle demande
                    </a>
                    
                    <!-- Quick stats (optional - can be removed if not needed) -->
                    <div class="mt-2 small text-muted">
                        <i class="bi bi-clock-history me-1"></i>
                        Créer une demande pour ce projet
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Optional: Quick filters or tabs (can be removed if not needed) -->
<?php

$stateCounts = [
    0 => 0,
    1 => 0,
    2 => 0,
    3 => 0
];

// Count tasks per state
foreach ($tasks as $t) {
    $state = (int)$t['is_completed'];
    if (isset($stateCounts[$state])) {
        $stateCounts[$state]++;
    }
}

$totalTasks = array_sum($stateCounts);

// Determine which states are currently active from query string
$activeStates = $_GET['states'] ?? [];
if (!is_array($activeStates)) $activeStates = [$activeStates];
$activeStates = array_map('intval', $activeStates);

// Filter tasks according to selected states
$filteredTasks = [];
foreach ($tasks as $task) {
    if (empty($activeStates) || in_array((int)$task['is_completed'], $activeStates)) {
        $filteredTasks[] = $task;
    }
}
?>

<div class="d-flex flex-wrap gap-2 mb-4">
    <?php 
    $stateColors = [
        0 => 'primary',
        1 => 'warning text-dark',
        2 => 'info text-dark',
        3 => 'success'
    ];
    foreach ([0=>'Envoyé', 1=>'Traitement', 2=>'Livraison', 3=>'Soldé'] as $state => $label): 
        $count = $stateCounts[$state];
        $isActive = in_array($state, $activeStates);
        $newStates = $activeStates;
        if ($isActive) {
            $newStates = array_diff($activeStates, [$state]);
        } else {
            $newStates[] = $state;
        }
        $query = http_build_query(['states' => $newStates]);
        $btnClass = $isActive ? "btn-{$stateColors[$state]}" : "btn-outline-{$stateColors[$state]}";
    ?>
        <a href="?<?= $query ?>" class="btn <?= $btnClass ?> rounded-pill px-2 py-2">
            <?= $count ?> <?= $label ?>
        </a>
    <?php endforeach; ?>

    <!-- Total tasks button -->
    <a href="?" class="btn btn-dark rounded-pill px-3 py-2">
        <?= $totalTasks ?> demandes
    </a>
</div>

<?php
// Override $tasks with filtered tasks for table rendering
$tasks = $filteredTasks;
?>

    <!-- Content area for tasks will go here -->
    <div id="tasksContainer">
        <!-- Your tasks table/list will be inserted here -->
    </div>
</div>

<!-- Optional: Add Bootstrap Icons if not already included -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<table class="table table-striped table-hover table-bordered table-compact">
<thead>
<tr>
    <th>#</th>
    <th>Type de demande</th>
    <th>Titre</th>
    <th>Description</th>
    <th>Référence</th>
    <th>État</th>
    <th>Date de création</th>
	<th>Date limite</th>
    <th>Actions</th>
</tr>
</thead>

<tbody>

<?php foreach ($tasks as $task): ?>

<?php
$isAppro = !empty($task['appro_pn']);
$isRetour = !empty($task['retour_pn']);
?>


<tr class="task-row" data-task-id="<?= $task['id']?>" data-task-state="<?= $task["is_completed"] ?>" >
    <td><?= $task['id'] ?></td>
    <td>
        <?php if ($isAppro): ?>
            <span class="badge bg-primary">APPRO</span>
        <?php elseif ($isRetour): ?>
            <span class="badge bg-warning text-dark">RETOUR</span>
        <?php else: ?>
            <span class="badge bg-secondary">Tâche</span>
        <?php endif; ?>
    </td>

    <td><?= $task['title'] ?></td>
    <td><?= $task['description'] ?></td>

    <td>
        <?php if ($isAppro): ?>
            <span class="font-monospace"><?= $task['appro_pn'] ?></span>
        <?php elseif ($isRetour): ?>
            <span class="font-monospace"><?= $task['retour_pn'] ?></span>
        <?php else: ?>
            -
        <?php endif; ?>
    </td>

    <td>
	<?php
	switch($task['is_completed']) {
		case 0:
			echo '<span class="badge bg-primary">Envoyé</span>';
			break;
		case 1:
			echo '<span class="badge bg-warning text-dark">Traitement</span>';
			break;
		case 2:
			echo '<span class="badge bg-info text-dark">Livré</span>';
			break;
		case 3:
			echo '<span class="badge bg-success">Soldé</span>';
			break;
	}
	?>
	</td>

    <td><?= $task['created_at'] ?></td>
	<td><?= $task['due_date'] ?></td>

	<td>
		<?php if (!$task['is_completed']): ?>
			<!-- Mark as En traitement -->
			<form method="POST" action="/projects/<?= $project['id'] ?>/tasks/<?= $task['id'] ?>/complete/1" class="d-inline">
				<button class="btn btn-warning btn-sm">Marquer comme En traitement</button>
			</form>

			<!-- Delete button -->
			<form method="POST" action="/projects/<?= $project['id'] ?>/tasks/<?= $task['id'] ?>/delete" class="d-inline ms-2">
				<button class="btn btn-danger btn-sm" type="submit" onclick="return confirm('Supprimer cette tâche ?')">
					<i class="bi bi-trash"></i> Supprimer
				</button>
			</form>
		<?php elseif ($task['is_completed'] == 1): ?>
			<form method="POST" action="/projects/<?= $project['id'] ?>/tasks/<?= $task['id'] ?>/complete/2">
				<button class="btn btn-info btn-sm">Marquer comme Livré</button>
			</form>
		<?php elseif ($task['is_completed'] == 2): ?>
			<form method="POST" action="/projects/<?= $project['id'] ?>/tasks/<?= $task['id'] ?>/complete/3">
				<button class="btn btn-success btn-sm">Marquer comme Soldé</button>
			</form>
		<?php else: ?>
			<span class="text-success">Traité</span>
		<?php endif; ?>
	</td>
</tr>

<!-- Dedicated row for Informations -->
<tr class="task-row bg-light" data-task-id="<?= $task['id'] ?>" data-task-state="<?= $task["is_completed"] ?>" >
    <td colspan="9">
        <?php if ($isAppro): ?>
            <strong>Quantité:</strong> <?= $task['appro_nb'] ?? '-' ?> &nbsp; | &nbsp;
            <strong>Désignation:</strong> <?= $task['designation'] ?? '-' ?> &nbsp; | &nbsp;
            <strong>Lieu:</strong> <?= $task['location'] ?? '-' ?> &nbsp; | &nbsp;
            <strong>Avion:</strong> <?= $task['plane'] ?? '-' ?>
        <?php elseif ($isRetour): ?>
            <strong>Quantité:</strong> <?= $task['retour_nb'] ?? '-' ?> &nbsp; | &nbsp;
            <strong>SN:</strong> <?= $task['sn'] ?? '-' ?> &nbsp; | &nbsp;
            <strong>Certif:</strong> <?= $task['certif'] ?? '-' ?>
        <?php else: ?>
            -
        <?php endif; ?>
    </td>
</tr>



<script>
const projectId = <?= $project['id'] ?>;

function showError(message) {
    const toastEl = document.getElementById('errorToast');
    toastEl.querySelector('.toast-body').textContent = message;
    const toast = new bootstrap.Toast(toastEl);
    toast.show();
}

// Use event delegation
document.addEventListener('click', function(e) {
    // Ignore clicks on buttons, forms, or links
    if (e.target.closest('button, form, a')) return;

    // Only handle row clicks
    const row = e.target.closest('.task-row');
    if (!row) return;

    const taskState = Number(row.dataset.taskState || 0);
    const taskId = row.dataset.taskId;

    if (taskState !== 0) {
        showError("Cette demande ne peut plus être modifiée.");
        return;
    }

    window.location.href = `/projects/${projectId}/tasks/${taskId}/edit`;
});
</script>
<?php endforeach; ?>


</tbody>
</table>

<div class="toast-container position-fixed bottom-0 end-0 p-3">
  <div id="errorToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="toast-header bg-danger text-white">
      <strong class="me-auto">Error</strong>
      <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
    </div>
    <div class="toast-body">
      Error message here.
    </div>
  </div>
</div>