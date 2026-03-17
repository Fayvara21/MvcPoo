<?php include __DIR__ . '/../../../public/navbar.php'; ?>

<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="display-6 fw-bold mb-1">Vue globale</h1>
            <p class="text-muted mb-0">Vue sur toutes les demandes en cours</p>
        </div>
        <div>
            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2">
                <i class="bi bi-clock-history me-2"></i>Mise à jour en direct
            </span>
        </div>
    </div>

<?php

$allProjects = Task::getAuthorizedProjects();
$tasks = Task::getAllTasksForAllProjects();

$projects = [];

foreach ($allProjects as $p) {
    $projects[$p['id']] = [
        'title' => $p['title'],
        'tasks' => []
    ];
}

foreach ($tasks as $task) {

    if (($task['is_completed'] == 0 || $task['is_completed'] == 1) && isset($projects[$task['project_id']])) {
        $projects[$task['project_id']]['tasks'][] = $task;
    }
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

<table class="table table-hover align-middle mb-0 project-table" data-project-id="<?= $projectId ?>">

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

<?php

usort($projectData['tasks'], function($a,$b){

$getTypePriority = fn($t) => !empty($t['appro_pn']) ? 1 : (!empty($t['retour_pn']) ? 2 : 3);

$typeA = $getTypePriority($a);
$typeB = $getTypePriority($b);

if ($typeA !== $typeB) return $typeA <=> $typeB;

$dueA = !empty($a['due_date']) ? strtotime($a['due_date']) : PHP_INT_MAX;
$dueB = !empty($b['due_date']) ? strtotime($b['due_date']) : PHP_INT_MAX;

return $dueA <=> $dueB;

});

$lastType = 0;

?>

<?php foreach ($projectData['tasks'] as $task):

$isAppro = !empty($task['appro_pn']);
$isRetour = !empty($task['retour_pn']);

$currentType = $isAppro ? 1 : ($isRetour ? 2 : 3);

if ($lastType && $lastType !== $currentType) {
echo '<tr class="table-info"><td colspan="6" class="text-center text-muted fw-bold"></td></tr>';
}

$lastType = $currentType;

?>

<tr>

<td class="ps-4 fw-medium"><?= $task['id'] ?></td>

<td>

<?php if ($isAppro): ?>
<span class="badge bg-primary">APPRO</span>
<?php elseif ($isRetour): ?>
<span class="badge bg-warning text-dark">RETOUR</span>
<?php else: ?>
<span class="badge bg-secondary">AUTRE</span>
<?php endif; ?>

</td>

<td><?= htmlspecialchars($task['title']) ?></td>

<td><?= htmlspecialchars($task['description']) ?></td>

<td>
<?= $isAppro ? $task['appro_pn'] : ($isRetour ? $task['retour_pn'] : '-') ?>
</td>

<td>

<?= $task['due_date'] ? date('d/m/Y H:i', strtotime($task['due_date'])) : '-' ?>

<?php if ($task['is_completed'] == 1): ?>
<span class="ms-2 spinner-border spinner-border-sm text-warning"></span>
<?php endif; ?>

</td>

</tr>

<tr class="table">

<td colspan="6" class="p-3">

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

function loadTasks(){

fetch(window.location.pathname + '/json')
.then(r => r.json())
.then(tasks => {

const projects = {};

tasks.forEach(task => {

if(task.is_completed === 0 || task.is_completed === 1){

if(!projects[task.project_id]){
projects[task.project_id] = {title:task.project_title,tasks:[]};
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

const typePriority = t => t.appro_pn ? 1 : t.retour_pn ? 2 : 3;

projectTasks.sort((a,b)=>{

const typeA = typePriority(a);
const typeB = typePriority(b);

if(typeA !== typeB) return typeA - typeB;

const dueA = a.due_date ? new Date(a.due_date) : new Date(8640000000000000);
const dueB = b.due_date ? new Date(b.due_date) : new Date(8640000000000000);

return dueA - dueB;

});

let lastType = 0;

projectTasks.forEach(task => {

const currentType = typePriority(task);

if(lastType && lastType !== currentType){

const typeLabels = {1:'APPRO',2:'RETOUR',3:'Tâche'};

html.push(`
<tr class="table-info">
<td colspan="6" class="text-center text-muted fw-semibold">
${typeLabels[currentType]}
</td>
</tr>
`);

}

lastType = currentType;

let rowClass = '';
let dueClass = '';

if(task.due_date){

const now = new Date();
const due = new Date(task.due_date);

const diffDays = Math.floor((due-now)/(1000*60*60*24));

if(diffDays > 7){
rowClass='table-secondary';
dueClass='text-dark';
}else if(diffDays > 0){
rowClass='table-warning';
dueClass='text-warning';
}else{
rowClass='table-danger';
dueClass='text-danger';
}

}

currentIds.push(task.id);

const typeBadge = task.appro_pn
? '<span class="badge bg-primary">APPRO</span>'
: task.retour_pn
? '<span class="badge bg-warning text-dark">RETOUR</span>'
: '<span class="badge bg-secondary">Tâche</span>';

const reference = task.appro_pn ?? task.retour_pn ?? '-';

const spinner = task.is_completed == 1
? `<span class="ms-2 spinner-border spinner-border-sm text-warning"></span>`
: '';

html.push(`

<tr class="${rowClass}">
<td>${task.id}</td>
<td>${typeBadge}</td>
<td>${task.title}</td>
<td>${task.description}</td>
<td>${reference}</td>
<td class="${dueClass}">
${task.due_date ?? '-'} ${spinner}
</td>
</tr>

<tr class="table">
<td colspan="6">

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

if(projectTasks.length === 0){

html.push(`
<tr>
<td colspan="6" class="text-center text-muted py-0">
<div class="text-center py-0">
<div class="display-1 text-muted mb-1">✔️</div>
<h2 class="text-muted small">Toutes les tâches sont terminées</h2>
</div>
</td>
</tr>
`);

}

const prev = previousTaskIds[projectId] || [];

const addedTasks = currentIds.filter(id => !prev.includes(id));
const removedTasks = prev.filter(id => !currentIds.includes(id));

if(!firstLoad && soundEnabled){

if(addedTasks.length) addSound.play().catch(()=>{});
if(removedTasks.length) removeSound.play().catch(()=>{});

}

previousTaskIds[projectId] = currentIds;

projectDiv.innerHTML = html.join('');

});

localStorage.setItem('previousTaskIds',JSON.stringify(previousTaskIds));

firstLoad=false;

})
.catch(console.error);

}

loadTasks();
setInterval(loadTasks,3000);

</script>