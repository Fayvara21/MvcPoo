<?php include __DIR__ . '/../../../public/navbar.php'; ?>

<div class="container-fluid py-4">
    <!-- Header -->

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body py-4 px-4">

            <h2 class="h2 fw-semibold mb-1">Demandes Magasin</h2>

            <p class="text-muted mb-3">
                Dépôt de nouvelles demandes au magasin, rangées par émetteur
            </p>


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

    <div class="d-flex justify-content-end mt-4">
        <div class="container my-4">

            <div class="p-4 border rounded shadow-sm bg-light">
                <h4 class="mb-3 text-primary">Mise à jour v260415:</h4>

                <ul class="list-group list-group-flush">
                    <li class="list-group-item">Refactorisation du système de couleurs pour integrer plus de
                        fonctionnalitées</li>
                    <li class="list-group-item">Ajout des statuts "en achat" et "en sous-traitance"</li>
                    <li class="list-group-item">Correction d'un bug d'affichage des statuts dans la vue globale</li>
                </ul>
            </div>

            <div class="p-4 border rounded shadow-sm bg-light">
                <h4 class="mb-3 text-primary">Mise à jour v260410:</h4>

                <ul class="list-group list-group-flush">
                    <li class="list-group-item">Ajout de l'emplacement lors de la création récursive des demandes
                        d'appro</li>
                    <li class="list-group-item">Un appro / retour sera desormais inseré par défaut lors d'une demande
                    </li>
                    <li class="list-group-item">Ajustements mineurs lors de la modification d'une demande d'appro</li>
                    <li class="list-group-item">Correction d'un bug d'affichage du champ OF dans les demandes d'appro
                    </li>
                    <li class="list-group-item">Correction d'un bug d'affichage lors de la modification d'une appro</li>


                </ul>
            </div>

            <div class="p-4 border rounded shadow-sm bg-light">
                <h4 class="mb-3 text-primary">Mise à jour v260409:</h4>

                <ul class="list-group list-group-flush">
                    <li class="list-group-item">Amélioration de l’interface (meilleure lisibilité des lignes et statuts)
                    </li>
                    <li class="list-group-item">Les demandes peuvent êtres cachées / déroulées en cliquant sur le titre
                    </li>
                    <li class="list-group-item">Les appro / retours peuvent être modifiés indépendamment après leur
                        création</li>
                    <li class="list-group-item">Ajout d'une barre de recherche pour filtrer les demandes (recherche PN,
                        SN, Avion, etc.)</li>
                    <li class="list-group-item">Correction de l'horodatage des demandes sur le fuseau horaire
                        Europe/Paris</li>
                </ul>
            </div>

        </div>

        <div class="container my-4">
            <div class="p-4 border rounded shadow-sm bg-light">

                <h4 class="mb-3 text-primary">Mises à jour à venir:</h4>

                <ul class="list-group list-group-flush mb-4">
                    <li class="list-group-item">
                        Ouverture de nouveaux groupes de travail :
                        <ul class="mt-2">
                            <li>BE</li>
                            <li>Qualité</li>
                        </ul>
                    </li>
                    <li class="list-group-item">Ajout de la partie partie Qualité pour la métrologie</li>
                    <li class="list-group-item">Liste déroulante pour la création de taches</li>
                    <li class="list-group-item">
                        Recherche de stock pour l'ADV avec :
                        <ul class="mt-2">
                            <li>Réponse écrite (stock vrai, faux ou partiel)</li>
                            <li>Quantité</li>
                            <li>Documents en pièce jointe</li>
                        </ul>
                    </li>
                    <li class="list-group-item">Recherche globale dans toute la base de données</li>
                    <li class="list-group-item">Ajout de l’heure de clôture dans l’historique</li>
                    <li class="list-group-item">Ajout d’un onglet contact pour les rapports d’incident</li>
                    <li class="list-group-item">Lignes dépassées en rouge, échéances proches en orange</li>
                    <li class="list-group-item">Priorisation des OF avec date souhaitée pour gérer les urgences</li>
                </ul>

                <h4 class="mb-3 text-success">Modules futurs envisagés</h4>

                <ul class="list-group list-group-flush">
                    <li class="list-group-item">Demande d’achat indirect avec notification au responsable</li>
                    <li class="list-group-item">Demande moyens généraux (ex : location de voiture)</li>
                    <li class="list-group-item">Demande d’achat direct (à traiter en dernier car plus complexe)</li>
                </ul>

            </div>
        </div>
    </div>

</div>