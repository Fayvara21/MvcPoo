<?php include __DIR__ . '/../../../public/navbar.php'; ?>
<h1 class="h1">Tâches pour le projet actuel:<?= $project['title'] ?></h1>
<!-- <a class=" h2 link-primary" href="/create">Ajouter une tâche</a> -->
<a class="h2 link-primary" href="/projects/<?= $project['id'] ?>/tasks/create">Ajouter une tâche au projet crouant</a>
<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($tasks as $task): ?>
            <tr>
                <td><?= $task['id'] ?></td>
                <td><?= $task['title'] ?></td>
                <td><?= $task['is_completed'] ? 'Terminée' : 'En cours' ?></td>
                <td>
                    <?php if (!$task['is_completed']): ?>
                        <form method="POST" action="/projects/<?= $project['id'] ?>/tasks/<?= $task['id'] ?>/complete">
                            <button class="btn btn-primary" type="submit">Marquer la tache comme terminée</button>
                        </form>
                    <?php endif; ?>
                    <form method="POST" action="/projects/<?= $project['id'] ?>/tasks/<?= $task['id'] ?>/delete">
                        <button class="btn btn-secondary" type="submit">Supprimer</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
  </tbody>
</table>
