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
    <div class="d-flex gap-2 mb-4">
        <span class="badge bg-light text-dark rounded-pill px-3 py-2">
            <i class="bi bi-list-ul me-1"></i>Toutes les demandes
        </span>
        <span class="badge bg-light text-dark rounded-pill px-3 py-2">
            <i class="bi bi-clock me-1"></i>En attente
        </span>
        <span class="badge bg-light text-dark rounded-pill px-3 py-2">
            <i class="bi bi-check-circle me-1"></i>Terminées
        </span>
    </div>

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

<tr>
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
        <?php if ($task['is_completed']): ?>
            <span class="badge bg-success">Terminée</span>
        <?php else: ?>
            <span class="badge bg-warning text-dark">En cours</span>
        <?php endif; ?>
    </td>

    <td><?= $task['created_at'] ?></td>
	<td><?= $task['due_date'] ?></td>

    <td>
        <?php if (!$task['is_completed']): ?>
        <form method="POST" action="/projects/<?= $project['id'] ?>/tasks/<?= $task['id'] ?>/complete">
            <button class="btn btn-primary btn-sm">Marquer terminée</button>
        </form>
        <?php endif; ?>
    </td>
</tr>

<!-- Dedicated row for Informations -->
<tr class="table-info">
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

<?php endforeach; ?>

</tbody>
</table>