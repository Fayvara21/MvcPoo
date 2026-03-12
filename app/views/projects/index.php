<?php include __DIR__ . '/../../../public/navbar.php'; ?>

<div class="container-fluid py-4">
    <!-- Header with global view link -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="display-6 fw-bold mb-1">Liste des projets</h1>
            <p class="text-muted mb-0">Tous les projets avec des demandes actives</p>
        </div>
        <a href="/projects/tasks/view" class="btn btn-outline-primary rounded-pill px-4 py-2">
            <i class="bi bi-grid-3x3-gap-fill me-2"></i>
            Vue globale des tâches
        </a>
    </div>

    <!-- Projects Grid -->
    <?php if (empty($projects)): ?>
        <!-- Empty state -->
        <div class="text-center py-5">
            <div class="display-1 text-muted mb-4">📋</div>
            <h3 class="h4 text-muted mb-3">Aucune demande en cours</h3>
            <p class="text-muted mb-4">Toutes les demandes sont traitées ou aucun projet n'est actif</p>
            <a href="/projects" class="btn btn-primary rounded-pill px-4">
                <i class="bi bi-arrow-left me-2"></i>
                Retour aux projets
            </a>
        </div>
    <?php else: ?>
        <!-- Project Cards Grid -->
        <div class="row g-4">
            <?php foreach ($projects as $project): ?>
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm hover-shadow transition">
                        <div class="card-body">
                            <!-- Project Header -->
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-primary bg-opacity-10 p-3 rounded-3 me-3">
                                    <i class="bi bi-folder2-open text-primary fs-4"></i>
                                </div>
                                <div>
                                    <span class="badge bg-light text-dark mb-2">Projet #<?= $project['id'] ?></span>
                                    <h5 class="card-title fw-semibold mb-0">
                                        <a href="/projects/<?= htmlspecialchars($project['id']) ?>/tasks" 
                                           class="text-decoration-none text-dark stretched-link">
                                            <?= htmlspecialchars($project['title']) ?>
                                        </a>
                                    </h5>
                                </div>
                            </div>
                            
                            <!-- Project Stats (if available) -->
                            <?php if (!empty($project['tasks_count'])): ?>
                                <div class="d-flex gap-3 mt-3 pt-3 border-top">
                                    <div>
                                        <span class="text-muted small">Demandes</span>
                                        <div class="fw-bold"><?= $project['tasks_count'] ?></div>
                                    </div>
                                    <?php if (!empty($project['urgent_count'])): ?>
                                        <div>
                                            <span class="text-muted small">Urgentes</span>
                                            <div class="fw-bold text-warning"><?= $project['urgent_count'] ?></div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Project Description (if available) -->
                            <?php if (!empty($project['description'])): ?>
                                <p class="card-text text-muted small mt-3">
                                    <?= htmlspecialchars(substr($project['description'], 0, 100)) ?>...
                                </p>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Card Footer with Quick Actions -->
                        <div class="card-footer bg-transparent border-0 pt-0 pb-3 px-3">
                            <div class="">
                                <a href="/projects/<?= htmlspecialchars($project['id']) ?>/tasks" 
                                   class="btn btn-sm btn-outline-primary rounded-pill flex-grow-1">
                                    <i class="bi bi-eye me-1"></i>
                                    Voir les demandes
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Summary Footer -->
        <div class="mt-4 text-muted small">
            <i class="bi bi-info-circle me-1"></i>
            <?= count($projects) ?> projet<?= count($projects) > 1 ? 's' : '' ?> avec des demandes en cours
        </div>
    <?php endif; ?>

</div>
