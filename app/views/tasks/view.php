<?php include __DIR__ . '/../../../public/navbar.php'; ?>

<div class="container-fluid py-4">

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="fw-semibold mb-1">Vue globale</h1>
                    <p class="text-muted mb-3">Vue sur toutes les demandes en cours</p>
                </div>
            </div>

        </div>
    </div>

    <?php

    $allProjects = Task::getAllProjects();
    $tasks = Task::getAllTasksForAllProjects();

    $projects = [];

    foreach ($allProjects as $p) {
        $projects[$p['id']] = [
            'title' => $p['title'],
            'tasks' => []
        ];
    }

    foreach ($tasks as $task) {
        if (isset($projects[$task['project_id']])) {
            $projects[$task['project_id']]['tasks'][] = $task;
        }
    }

    function getDeadlineClass($dueDate)
    {
        if (!$dueDate)
            return '';

        $due = strtotime($dueDate);
        $todayStart = strtotime('today');
        $tomorrowStart = strtotime('tomorrow');

        if ($due < $todayStart) {
            return 'table-danger'; // expired (past)
        }

        if ($due >= $todayStart && $due < $tomorrowStart) {
            return 'table-warning'; // today
        }

        return '';
    }

    ?>

    <div id="taskContainer">

        <?php foreach ($projects as $projectId => $projectData): ?>

            <div class="card shadow-sm border-1 mb-4">

                <div class="card-header bg-white border-1 pt-4 px-4">
                    <h3 class="h5 fw-semibold mb-0">
                        <i class="bi bi-folder2-open text-primary me-2"></i>
                        <?= htmlspecialchars($projectData['title']) ?>
                    </h3>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">

                        <table class="table table-hover align-middle mb-0 project-table"
                            data-project-id="<?= $projectId ?>">

                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4" style="width:60px">#</th>
                                    <th style="width:100px">Type</th>
                                    <th>Titre</th>
                                    <th>Desc</th>
                                    <th style="width:120px">Ref</th>
                                    <th style="width:160px">Date limite</th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php if (!empty($projectData['tasks'])): ?>

                                    <?php foreach ($projectData['tasks'] as $task):

                                        $appro = $task['appro'] ?? [];
                                        $retour = $task['retour'] ?? [];
                                        $verifStock = $task['verif_stock'] ?? [];

                                        $isAppro = !empty($appro);
                                        $isRetour = !empty($retour);
                                        $isVerifStock = !empty($verifStock);

                                        $approFirst = $appro[0] ?? [];
                                        $retourFirst = $retour[0] ?? [];
                                        $verifStockFirst = $verifStock[0] ?? [];

                                        $deadlineClass = getDeadlineClass($task['due_date']);

                                        ?>

                                        <tr class="<?= $deadlineClass ?>">

                                            <td class="ps-4 fw-medium"><?= $task['id'] ?></td>
                                            <td>
                                                <?php if ($isAppro): ?>
                                                    <span class="badge bg-primary">APPRO</span>
                                                <?php elseif ($isRetour): ?>
                                                    <span class="badge bg-warning text-dark">RETOUR</span>
                                                <?php elseif ($isVerifStock): ?>
                                                    <span class="badge bg-success">VERIF STOCK</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">AUTRE</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= htmlspecialchars($task['title']) ?></td>
                                            <td>
                                                <?= $isAppro
                                                    ? ($approFirst['designation'] ?? '-')
                                                    : ($isRetour ? ($retourFirst['sn'] ?? '-') : ($isVerifStock ? ($verifStockFirst['name'] ?? '-') : '-')) ?>
                                            </td>
                                            <td>
                                                <?= $isAppro
                                                    ? ($approFirst['pn'] ?? '-')
                                                    : ($isRetour ? ($retourFirst['pn'] ?? '-') : ($isVerifStock ? ($verifStockFirst['pn'] ?? '-') : '-')) ?>
                                            </td>
                                            <td>
                                                <?= $task['due_date'] ? date('d/m/Y H:i', strtotime($task['due_date'])) : '-' ?>
                                                <?php if ($task['is_completed'] == 1): ?>
                                                    <span class="ms-2 spinner-border spinner-border-sm text-warning"></span>
                                                <?php endif; ?>
                                            </td>

                                        </tr>

                                        <tr class="table <?= $deadlineClass ?>">
                                            <td colspan="6" class="p-3">
                                                <div class="mb-2"><strong>Description:</strong>
                                                    <?= htmlspecialchars($task['description']) ?></div>

                                                <?php if ($isAppro): ?>
                                                    <div class="d-flex flex-wrap gap-4 small">
                                                        <span><strong>Quantité:</strong> <?= $approFirst['nb'] ?? '-' ?></span>
                                                        <span><strong>Lieu:</strong> <?= $approFirst['location'] ?? '-' ?></span>
                                                        <span><strong>Avion:</strong> <?= $approFirst['plane'] ?? '-' ?></span>
                                                        <span><strong>OE:</strong> <?= $approFirst['oe'] ?? '-' ?></span>
                                                        <span><strong>OF:</strong> <?= $approFirst['of'] ?? '-' ?></span>
                                                    </div>
                                                <?php elseif ($isRetour): ?>
                                                    <div class="d-flex flex-wrap gap-4 small">
                                                        <span><strong>Quantité:</strong> <?= $retourFirst['nb'] ?? '-' ?></span>
                                                        <span><strong>Certif:</strong> <?= $retourFirst['certif'] ?? '-' ?></span>
                                                    </div>
                                                <?php elseif ($isVerifStock): ?>
                                                    <div class="d-flex flex-wrap gap-4 small">
                                                        <span><strong>Quantité:</strong> <?= $verifStockFirst['nb'] ?? '-' ?></span>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="text-muted small">-</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>

                                    <?php endforeach; ?>

                                <?php else: ?>

                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            Aucune tâche pour ce projet
                                        </td>
                                    </tr>

                                <?php endif; ?>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        <?php endforeach; ?>

    </div>
</div>

<script>
    const addSound = new Audio('/sounds/open.mp3');       // state 0
    const completeSound = new Audio('/sounds/close.mp3'); // state 2

    let previousTasks = {};        // { taskId: state }
    let notifiedTasks = {};        // { taskId: {open: true/false, close: true/false} }

    function getDeadlineClassJS(dueDate) {
        if (!dueDate) return '';

        const now = new Date();
        const todayStr = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}-${String(now.getDate()).padStart(2, '0')}`;

        // Compare just the date part (YYYY-MM-DD)
        if (dueDate < todayStr) {
            return 'table-danger'; // expired
        }

        if (dueDate.startsWith(todayStr)) {
            return 'table-warning'; // today
        }

        return '';
    }

    function loadTasks() {
        fetch(window.location.pathname + '/json')
            .then(r => r.json())
            .then(tasks => {

                const projects = {};

                tasks.forEach(task => {
                    const s = parseInt(task.is_completed);

                    // --- SOUND LOGIC for all tasks ---
                    if (!notifiedTasks[task.id]) notifiedTasks[task.id] = { open: false, close: false };
                    const prevState = previousTasks[task.id] ?? null;

                    // Open sound
                    if (s === 0 && !notifiedTasks[task.id].open) {
                        addSound.play().catch(() => { });
                        notifiedTasks[task.id].open = true;
                    }

                    // Close sound
                    if (prevState !== 2 && s === 2 && !notifiedTasks[task.id].close) {
                        completeSound.play().catch(() => { });
                        notifiedTasks[task.id].close = true;
                    }

                    // Update previous state
                    previousTasks[task.id] = s;

                    // --- FILTER FOR RENDERING ONLY ---
                    if (![0, 1].includes(s)) return;

                    const appro = task.appro || [];
                    const retour = task.retour || [];
                    const verifStock = task.verif_stock || [];

                    task.approFirst = appro[0] || {};
                    task.retourFirst = retour[0] || {};
                    task.verifStockFirst = verifStock[0] || {};

                    task.isAppro = appro.length > 0;
                    task.isRetour = retour.length > 0;
                    task.isVerifStock = verifStock.length > 0;

                    if (!projects[task.project_id]) {
                        projects[task.project_id] = { title: task.project_title, tasks: [] };
                    }

                    projects[task.project_id].tasks.push(task);
                });

                document.querySelectorAll('.project-table').forEach(table => {
                    const projectId = table.dataset.projectId;
                    const tbody = table.querySelector('tbody');
                    const projectTasks = projects[projectId]?.tasks || [];

                    let html = [];

                    projectTasks.forEach(task => {
                        let typeBadge = '';
                        if (task.isAppro) {
                            typeBadge = '<span class="badge bg-primary">APPRO</span>';
                        } else if (task.isRetour) {
                            typeBadge = '<span class="badge bg-warning text-dark">RETOUR</span>';
                        } else if (task.isVerifStock) {
                            typeBadge = '<span class="badge bg-success">VERIF STOCK</span>';
                        } else {
                            typeBadge = '<span class="badge bg-secondary">AUTRE</span>';
                        }

                        let reference = '-';
                        if (task.isAppro) {
                            reference = task.approFirst.pn ?? '-';
                        } else if (task.isRetour) {
                            reference = task.retourFirst.pn ?? '-';
                        } else if (task.isVerifStock) {
                            reference = task.verifStockFirst.pn ?? '-';
                        }

                        const rowClass = getDeadlineClassJS(task.due_date);

                        const loadingIcon = task.is_completed === 1
                            ? '<span class="spinner-border spinner-border-sm text-warning ms-2"></span>'
                            : '';

                        let detailsHtml = '';
                        if (task.isAppro) {
                            detailsHtml = `
                                <strong>Quantité:</strong> ${task.approFirst.nb ?? '-'} |
                                <strong>Lieu:</strong> ${task.approFirst.location ?? '-'} |
                                <strong>Avion:</strong> ${task.approFirst.plane ?? '-'}
                            `;
                        } else if (task.isRetour) {
                            detailsHtml = `
                                <strong>Quantité:</strong> ${task.retourFirst.nb ?? '-'} |
                                <strong>SN:</strong> ${task.retourFirst.sn ?? '-'} |
                                <strong>Certif:</strong> ${task.retourFirst.certif ?? '-'}
                            `;
                        } else if (task.isVerifStock) {
                            detailsHtml = `
                                <strong>Quantité:</strong> ${task.verifStockFirst.nb ?? '-'} |
                                <strong>Nom:</strong> ${task.verifStockFirst.name ?? '-'}
                            `;
                        } else {
                            detailsHtml = '-';
                        }

                        let designationOrRef = '-';
                        if (task.isAppro) {
                            designationOrRef = task.approFirst.designation ?? '-';
                        } else if (task.isRetour) {
                            designationOrRef = task.retourFirst.sn ?? '-';
                        } else if (task.isVerifStock) {
                            designationOrRef = task.verifStockFirst.name ?? '-';
                        }

                        html.push(`
                            <tr class="${rowClass}">
                                <td class="ps-4 fw-medium">${task.id}</td>
                                <td>${typeBadge}</td>
                                <td>${task.title}</td>
                                <td>${designationOrRef}</td>
                                <td>${reference}</td>
                                <td>${task.due_date ?? '-'} ${loadingIcon}</td>
                            </tr>
                            <tr class="${rowClass}">
                                <td colspan="6" class="p-3">
                                    <div class="mb-2"><strong>Description:</strong> ${task.description || '-'}</div>
                                    ${detailsHtml}
                                </td>
                            </tr>
                        `);
                    });

                    tbody.innerHTML = html.join('');
                });

            })
            .catch(console.error);
    }

    loadTasks();
    setInterval(loadTasks, 3000);
</script>