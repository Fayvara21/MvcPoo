<?php include __DIR__ . '/../../../public/navbar.php'; ?>

<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="display-6 fw-bold mb-1">Real Time View</h1>
            <p class="text-muted mb-0">Vue globale en temps réel</p>
        </div>
        <div>
            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2">
                <i class="bi bi-clock-history me-2"></i>Mise à jour en direct
            </span>
        </div>
    </div>

    <div id="taskContainer">
        <?php
        // Group tasks by project
		$projects = [];
		$allProjects = Task::getAuthorizedProjects();

		// initialize all projects
		foreach ($allProjects as $p) {
			$projects[$p['id']] = [
				'title' => $p['title'],
				'tasks' => []
			];
		}

		// attach tasks if they exist
		foreach ($tasks as $task) {
			if (!$task['is_completed'] && isset($projects[$task['project_id']])) {
				$projects[$task['project_id']]['tasks'][] = $task;
			}
		}

        foreach ($projects as $projectId => $projectData): ?>
            <!-- Project Card -->
            <div class="card shadow-sm border-1 mb-4">
                <div class="card-header bg-white border-1 pt-4 px-4">
                    <h3 class="h5 fw-semibold mb-0">
                        <i class="bi bi-folder2-open text-primary me-2"></i>
                        <?= htmlspecialchars($projectData['title']) ?>
                    </h3>
                </div>
                
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 project-table" data-project-id="<?= $projectId ?>">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4" style="width: 60px">#</th>
                                    <th style="width: 100px">Type</th>
                                    <th>Titre</th>
                                    <th>Desc</th>
                                    <th style="width: 120px">Ref</th>
                                    <th style="width: 160px">Date limite</th>
                                </tr>
                            </thead>
							<tbody>

								<?php if (!empty($projectData['tasks'])): ?>

									<?php foreach ($projectData['tasks'] as $task): 
										$isAppro = !empty($task['appro_pn']);
										$isRetour = !empty($task['retour_pn']);

										// Determine urgency class
										$urgencyClass = '';
										$dueClass = '';

										if (!empty($task['due_date'])) {
											$now = new DateTime();
											$due = new DateTime($task['due_date']);
											$diffDays = (int)$now->diff($due)->format('%r%a');

											if ($diffDays > 7) {
												$urgencyClass = 'table-primary';
												$dueClass = 'text-primary';
											} elseif ($diffDays > 0) {
												$urgencyClass = 'table-warning';
												$dueClass = 'text-warning';
											} else {
												$urgencyClass = 'table-danger';
												$dueClass = 'text-danger';
											}
										}
									?>

										<!-- Main Task Row -->
										<tr class="<?= $urgencyClass ?>">
											<td class="ps-4 fw-medium"><?= $task['id'] ?></td>
											<td>
												<?php if ($isAppro): ?>
													<span class="badge bg-primary">APPRO</span>
												<?php elseif ($isRetour): ?>
													<span class="badge bg-warning text-dark">RETOUR</span>
												<?php else: ?>
													<span class="badge bg-secondary">Tâche</span>
												<?php endif; ?>
											</td>
											<td><?= htmlspecialchars($task['title']) ?></td>
											<td><?= htmlspecialchars($task['description']) ?></td>
											<td>
												<?= $isAppro ? $task['appro_pn'] : ($isRetour ? $task['retour_pn'] : '-') ?>
											</td>
											<td class="<?= $dueClass ?>">
												<?= $task['due_date'] ? date('d/m/Y H:i', strtotime($task['due_date'])) : '-' ?>
											</td>
										</tr>

										<!-- Details Row -->
										<tr class="table-info">
											<td colspan="7" class="p-3">
												<?php if ($isAppro): ?>
													<div class="d-flex flex-wrap gap-4 small">
														<span><strong>Quantité:</strong> <?= $task['appro_nb'] ?? '-' ?></span>
														<span><strong>Désignation:</strong> <?= $task['designation'] ?? '-' ?></span>
														<span><strong>Lieu:</strong> <?= $task['location'] ?? '-' ?></span>
														<span><strong>Avion:</strong> <?= $task['plane'] ?? '-' ?></span>
													</div>
												<?php elseif ($isRetour): ?>
													<div class="d-flex flex-wrap gap-4 small">
														<span><strong>Quantité:</strong> <?= $task['retour_nb'] ?? '-' ?></span>
														<span><strong>SN:</strong> <?= $task['sn'] ?? '-' ?></span>
														<span><strong>Certif:</strong> <?= $task['certif'] ?? '-' ?></span>
													</div>
												<?php else: ?>
													<span class="text-muted small">-</span>
												<?php endif; ?>
											</td>
										</tr>

									<?php endforeach; ?>

								<?php else: ?>

									<!-- Empty project row -->
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
        
        <?php if (empty($projects)): ?>
            <div class="text-center py-5">
                <div class="display-1 text-muted mb-3">📋</div>
                <h3 class="h5 text-muted">Aucune tâche en cours</h3>
                <p class="text-muted small">Toutes les tâches sont terminées</p>
            </div>
        <?php endif; ?>
    </div>
</div>


<script>
const addSound = new Audio('/sounds/open.mp3');
const removeSound = new Audio('/sounds/close.mp3');

let previousTaskIds = JSON.parse(localStorage.getItem('previousTaskIds') || '{}');
let soundEnabled = true;
let firstLoad = true;

function loadTasks() {
    fetch(window.location.pathname + '/json')
        .then(r => r.json())
        .then(tasks => {

            const projects = {};

            // Group tasks by project
            tasks.forEach(task => {
                if (!task.is_completed) {
                    if (!projects[task.project_id]) {
                        projects[task.project_id] = { title: task.project_title, tasks: [] };
                    }
                    projects[task.project_id].tasks.push(task);
                }
            });

            document.querySelectorAll('.project-table').forEach(table => {

				const projectId = table.dataset.projectId;
				const projectDiv = table.querySelector('tbody');

				const projectTasks = projects[projectId]?.tasks || [];

                let html = [];
				let currentIds = [];

				projectTasks.forEach(task => {

                    let rowClass = '';
                    let dueClass = '';

                    if (task.due_date) {

                        const now = new Date();
                        const due = new Date(task.due_date);
                        const diffDays = Math.floor((due - now) / (1000 * 60 * 60 * 24));

                        if (diffDays > 7) {
                            rowClass = 'table-success';
                            dueClass = 'text-success';
                        }
                        else if (diffDays > 0) {
                            rowClass = 'table-warning';
                            dueClass = 'text-warning';
                        }
                        else {
                            rowClass = 'table-danger';
                            dueClass = 'text-danger';
                        }
                    }

                    currentIds.push(task.id);

                    const typeBadge = task.appro_pn 
                        ? '<span class="badge bg-primary">APPRO</span>'
                        : task.retour_pn 
                            ? '<span class="badge bg-warning text-dark">RETOUR</span>'
                            : '<span class="badge bg-secondary">Tâche</span>';

                    const reference = task.appro_pn ?? task.retour_pn ?? '-';

                    html.push(`
                        <tr class="${rowClass}">
                            <td>${task.id}</td>
                            <td>${typeBadge}</td>
                            <td>${task.title}</td>
                            <td>${task.description}</td>
                            <td>${reference}</td>
                            <td class="${dueClass}">${task.due_date ?? '-'}</td>
                        </tr>
                        <tr class="table-info">
                            <td colspan="8">
                                ${task.appro_pn ? `<strong>Quantité:</strong> ${task.appro_nb ?? '-'} | 
                                <strong>Désignation:</strong> ${task.designation ?? '-'} | 
                                <strong>Lieu:</strong> ${task.location ?? '-'} | 
                                <strong>Avion:</strong> ${task.plane ?? '-'}` : ''}
                                ${task.retour_pn ? `<strong>Quantité:</strong> ${task.retour_nb ?? '-'} | 
                                <strong>SN:</strong> ${task.sn ?? '-'} | 
                                <strong>Certif:</strong> ${task.certif ?? '-'}` : ''}
                                ${!task.appro_pn && !task.retour_pn ? '-' : ''}
                            </td>
                        </tr>
                    `);
                });
				
				if (projectTasks.length === 0) {
					html.push(`
						<tr>
							<td colspan="6" class="text-center text-muted py-0">
								<div class="text-center py-0">
									<div class="display-1 text-muted mb-1">📋</div>
									<h3 class="h5 text-muted">Aucune tâche en cours</h3>
									<p class="text-muted small">Toutes les tâches sont terminées</p>
								</div>
							</td>
						</tr>
					`);
				}

                // Sound logic
                const prev = previousTaskIds[projectId] || [];

				const addedTasks = currentIds.filter(id => !prev.includes(id));
				const removedTasks = prev.filter(id => !currentIds.includes(id));

                if (!firstLoad && soundEnabled) {
                    if (addedTasks.length) addSound.play().catch(()=>{});
                    if (removedTasks.length) removeSound.play().catch(()=>{});
                }

                previousTaskIds[projectId] = currentIds;

                projectDiv.innerHTML = html.join('');
            });

            // Save state
            localStorage.setItem('previousTaskIds', JSON.stringify(previousTaskIds));

            // After first poll we allow sounds
            firstLoad = false;

        })
        .catch(console.error);
}

loadTasks();
setInterval(loadTasks, 3000);
</script>