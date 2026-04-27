<?php include __DIR__ . '/../../../public/navbar.php'; ?>


<div class="container py-4">

    <!-- Header -->
    <div class="px-lg-4">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/projects" class="text-decoration-none">Projets</a></li>
                <li class="breadcrumb-item"><a href="/projects/<?= $_GET['project_id'] ?? '' ?>"
                        class="text-decoration-none">Projet</a></li>
                <li class="breadcrumb-item active">Nouvelle tâche</li>
            </ol>
        </nav>
    </div>

    <form method="POST">

        <div class="row g-4">

            <!-- LEFT -->
            <div class="col-10 col-lg-5">

                <div class="card shadow-sm border-0">

                    <div class="card-header bg-white border-0 pt-4 px-4 px-xl-5">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-primary bg-opacity-10 p-3 rounded-3">
                                <i class="bi bi-plus-circle text-primary fs-4"></i>
                            </div>
                            <div>
                                <h1 class="h3 fw-bold mb-1">Ajouter une tâche</h1>
                                <p class="text-muted mb-0">Créez une nouvelle demande</p>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-4 p-xl-5">

                        <h5 class="fw-semibold mb-3">Informations générales</h5>

                        <div class="mb-3">
                            <label class="form-label fw-medium">
                                BP (titre, référence): <span class="text-danger">*</span>
                            </label>
                            <input class="form-control form-control-lg" type="text" name="title" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-medium">Description :</label>
                            <textarea class="form-control" name="desc" rows="3"></textarea>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6 col-lg-8">
                                <label class="form-label fw-medium">Date limite</label>
                                <input type="datetime-local" class="form-control" name="dueDate"
                                    min="<?= date('Y-m-d\TH:i') ?>">
                            </div>

                            <div class="col-md-6 col-lg-4">
                                <label class="form-label fw-medium">Type de tâche</label>
                                <select class="form-select" name="type" id="taskType">
                                    <option value="">-- Choisir --</option>
                                    <?php
                                    foreach ($taskTypes as $value => $label) {
                                        echo "<option value=\"$value\">$label</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="d-flex gap-2 justify-content-end border-top pt-4 mt-4">
                            <a href="javascript:history.back()" class="btn btn-outline-secondary px-4">
                                <i class="bi bi-x-lg me-2"></i>Annuler
                            </a>
                            <button type="submit" class="btn btn-primary px-5">
                                <i class="bi bi-check-lg me-2"></i>Ajouter
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT -->
            <div class="col-12 col-lg-5">
                <?php include __DIR__ . '/partials/appro.php'; ?>
                <?php include __DIR__ . '/partials/retour.php'; ?>
                <?php include __DIR__ . '/partials/verif_Stock.php'; ?>
            </div>

        </div>

    </form>

</div>

<script defer src="/js/tasks/create/appro.js"></script>
<script defer src="/js/tasks/create/retour.js"></script>
<script defer src="/js/tasks/create/verif_stock.js"></script>
<script defer src="/js/tasks/shared.js"></script>
<script defer src="/js/tasks/task-type-toggle.js"></script>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">