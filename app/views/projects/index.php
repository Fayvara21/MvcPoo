<?php include __DIR__ . '/../../../public/navbar.php'; ?>

<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="display-6 fw-bold mb-1">Liste des projets</h1>
            <p class="text-muted mb-0">Tous les projets avec des demandes actives</p>
        </div>
    </div>

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

        <div class="row g-4">
            <?php foreach ($projects as $project): ?>
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm">

                        <div class="card-body">
                            <!-- Project Header -->
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-primary bg-opacity-10 p-3 rounded-3 me-3">
                                    <i class="bi bi-folder2-open text-primary fs-4"></i>
                                </div>
                                <div>
                                    <h4 class="card-title fw-semibold mb-0">
                                        <a href="/projects/<?= htmlspecialchars($project['id']) ?>/tasks" 
                                           class="text-decoration-none text-dark stretched-link">
                                            <?= htmlspecialchars($project['title']) ?>
                                        </a>
                                    </h4>
                                </div>
                            </div>

                            <?php if (!empty($project['description'])): ?>
                                <p class="card-text text-muted small mt-2">
                                    <?= htmlspecialchars(substr($project['description'], 0, 100)) ?>
                                </p>
                            <?php endif; ?>

                            <!-- TASKS BLOCK (FIXED VISIBILITY) -->
                            <?php if (!empty($project['tasks'])): ?>
                                <div class="mt-3">

                                    <div class="fw-semibold small text-uppercase text-muted mb-2">
                                        Demandes
                                    </div>

                                    <div class="border rounded-3 overflow-hidden">

                                        <?php foreach ($project['tasks'] as $index => $task): ?>
                                            <div class="p-3 <?= $index % 2 === 0 ? 'bg-light' : 'bg-white' ?> border-bottom">

                                                <!-- Top row -->
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <div class="fw-semibold">
                                                        <?= htmlspecialchars($task['title']) ?>
                                                    </div>

                                                    <!-- Highlight Appro / Retour PN -->
                                                    <?php if (!empty($task['type'])): ?>
                                                        <?php if ($task['type'] === 'appro'): ?>
                                                            <span class="badge bg-success">Appro PN</span>
                                                        <?php elseif ($task['type'] === 'retour'): ?>
                                                            <span class="badge bg-danger">Retour PN</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-secondary">
                                                                <?= htmlspecialchars($task['type']) ?>
                                                            </span>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                </div>

                                                <!-- Bottom row -->
                                                <div class="d-flex justify-content-between small text-muted">
                                                    <div>
                                                        <?= htmlspecialchars($task['created_at'] ?? '') ?>
                                                    </div>

                                                    <?php if (!empty($task['urgent'])): ?>
                                                        <span class="text-warning fw-semibold">
                                                            ⚠ Urgent
                                                        </span>
                                                    <?php endif; ?>
                                                </div>

                                            </div>
                                        <?php endforeach; ?>

                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Stats -->
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

                        </div>

                        <!-- Footer -->
                        <div class="card-footer bg-transparent border-0 pt-0 pb-3 px-3">
                            <a href="/projects/<?= htmlspecialchars($project['id']) ?>/tasks" 
                               class="btn btn-sm btn-outline-primary rounded-pill w-100">
                                <i class="bi bi-eye me-1"></i>
                                Voir les demandes
                            </a>
                        </div>

                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Summary -->
        <div class="mt-4 text-muted small">
            <i class="bi bi-info-circle me-1"></i>
            <?= count($projects) ?> projet<?= count($projects) > 1 ? 's' : '' ?> avec des demandes en cours
        </div>

    <?php endif; ?>

</div>