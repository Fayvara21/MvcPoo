<?php include __DIR__ . '/../../../public/navbar.php'; ?>

<div class="container-fluid custom-container px-4 py-4">
    <!-- Header -->

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">

            <h1 class="fw-bold mb-2 cb12">CB12</h1>

            <h2 class="fw-semibold mb-1">Demandes Magasin</h2>

            <p class="text-muted mb-3">
                Dépôt de nouvelles demandes au magasin, rangées par émetteur
            </p>

        </div>
    </div>

    <?php if (empty($projects)): ?>
        <!-- Empty state -->
        <div class="text-center py-5">
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
                            <?php if (($project['tasks_count'] ?? 0) > 0): ?>
                                <div class="d-flex gap-3 mt-3 pt-3 border-top">
                                    <div>
                                        <span class="text-muted small">Demandes</span>
                                        <div class="fw-bold"><?= $project['tasks_count'] ?></div>
                                    </div>

                                    <div>
                                        <span class="text-muted small">Urgentes</span>
                                        <div class="fw-bold">
                                            <?php if (($project['urgent_count'] ?? 0) > 0): ?>
                                                <span class="badge bg-danger"><?= $project['urgent_count'] ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">0</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
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



    <div class=" CHANGELOG d-flex justify-content-end mt-4">
        <div class="container my-4 overflow-y-auto" style="max-height: 500px;">

            <div class="mb-3 p-4 border rounded shadow-sm bg-light">
                <h4 class="mb-2 text-primary">Mise à jour v260428:</h4>

                <ul class="list-group list-group-flush">

                    <li class="list-group-item">Correction d'un bug d'affichage lors de la création d'un retour</li>
                    <li class="list-group-item">Les boutons d'actions sont desormais formattées en listes déroulantes</li>
                    <li class="list-group-item">Les changements d'état auront un pop-up de confirmation pour éviter les erreurs</li>

                </ul>
            </div>

            <div class="mb-3 p-4 border rounded shadow-sm bg-light">
                <h4 class="mb-2 text-primary">Mise à jour v260427:</h4>

                <ul class="list-group list-group-flush">

                    <li class="list-group-item">Séparation de la logique de filtres d'appro, retour et verif stock</li>
                    <li class="list-group-item">Système de droit de création selon type de sous-taches</li>
                    <li class="list-group-item">Remplacement de "magasinier" par "emplacement" et "remarques"</li>
                    <li class="list-group-item">Renommage de "nom" en "libellé"</li>
                    <li class="list-group-item">Les Verifications de stocks ne seront plus soldables, et garderons
                        simplement leur dernier statut</li>

                </ul>
            </div>

            <div class=" mb-3 p-4 border rounded shadow-sm bg-light">
                <h4 class="mb-2 text-primary">Mise à jour v260423:</h4>

                <ul class="list-group list-group-flush">
                    <li class="list-group-item">Ajout des demandes de vérification de stock dans la liste des
                        demandes
                    </li>
                    <li class="list-group-item">Refactorisation de la base de code pour faciliter l'intégration de
                        nouvelles fonctionnalitées</li>
                    <li class="list-group-item">Modification du système de filtres par boutons pour intégrer les
                        vérifications de stock</li>

                    <li class="list-group-item">Correction de plusieurs bugs d'affichage mineurs</li>



                </ul>
            </div>

            <div class="mb-3 p-4 border rounded shadow-sm bg-light">
                <h4 class="mb-2 text-primary">Mise à jour v260420:</h4>

                <ul class="list-group list-group-flush">
                    <li class="list-group-item">Ajout du bouton contact dans la barre de navigation</li>
                    <li class="list-group-item">Ajout du formulaire de rapport d'incident pour les utilisateurs</li>
                    <li class="list-group-item">Ajout de la vue de tous les rapports d'incidents pour les admins</li>
                    <li class="list-group-item">Ajout de la date de complétion lorsqu'une demande est marquée comme
                        livrée</li>
                    <li class="list-group-item">Les horodatages des demandes sont formatées pour être plus lisibles</li>
                    <li class="list-group-item">Application de plusieurs ajustements de l'interface</li>

                </ul>
            </div>

            <div class="mb-3 p-4 border rounded shadow-sm bg-light">
                <h4 class="mb-2 text-primary">Mise à jour v260417:</h4>

                <ul class="list-group list-group-flush">
                    <li class="list-group-item">Ajout des groupes "BE" et "Qualité"</li>
                    <li class="list-group-item">Ajustements visuels de l'interface (page d'accueil, Vue des demandes)
                    </li>
                    <li class="list-group-item">Mise à jour de la barre de navigation</li>
                    <li class="list-group-item">Les demandes dépassées sont desormais colorées en rouge, et échéances
                        proches en orange</li>
                    <li class="list-group-item">Correction d'un bug fonctionnel lors de la création d'un retour</li>
                    <li class="list-group-item">Correction d'un bug fonctionnel lors de la création d'un nouvel
                        utilisateur </li>
                    <li class="list-group-item">Le temps d'une session navigateur à été étendue de 2h à 12h</li>
                </ul>
            </div>

            <div class="mb-3 p-4 border rounded shadow-sm bg-light">
                <h4 class="mb-2 text-primary">Mise à jour v260415:</h4>

                <ul class="list-group list-group-flush">
                    <li class="list-group-item">Refactorisation du système de couleurs pour integrer plus de
                        fonctionnalitées</li>
                    <li class="list-group-item">Ajout des statuts "en achat" et "en sous-traitance"</li>
                    <li class="list-group-item">Correction d'un bug d'affichage des statuts dans la vue globale</li>

                </ul>
            </div>

            <div class="mb-3 p-4 border rounded shadow-sm bg-light">
                <h4 class="mb-2 text-primary">Mise à jour v260410:</h4>

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

            <div class="mb-3 p-4 border rounded shadow-sm bg-light">
                <h4 class="mb-2 text-primary">Mise à jour v260409:</h4>

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

        <div class="container my-4 overflow-y-auto" style="max-height: 500px;">
            <div class="mb-3 p-4 border rounded shadow-sm bg-light">

                <h4 class="mb-2 text-primary">Mises à jour à venir:</h4>

                <ul class="list-group list-group-flush mb-4">
                    <li class="list-group-item">
                        Recherche de stock pour l'ADV avec :
                        <ul class="mt-2">
                            <li>Documents en pièce jointe</li>
                        </ul>
                    </li>
                    <li class="list-group-item">Recherche globale dans toute la base de données</li>
                    <li class="list-group-item">Priorisation des OF avec date souhaitée pour gérer les urgences</li>
                    <li class="list-group-item">Demandes d'enlevements (dont colisage)</li>
                    <li class="list-group-item">Demandes de livraison (dont 3rd party) </li>
                    <li class="list-group-item">Trier les demandes par date, statut, ou autre critère</li>
                    <li class="list-group-item">Vue améliorée des MaJ</li>
                    <li class="list-group-item">Système de réponses aux demandes</li>
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