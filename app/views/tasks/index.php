<h1 class="h1">Liste des tâches</h1>
<a class=" h2 link-primary" href="/create">Ajouter une tâche</a>
<table class="table">
   <thead>
      <tr>
         <th>ID</th>
         <th>Titre</th>
         <th>Statut</th>
         <th>Action</th>
      </tr>
   </thead>
   <tbody>
      <?php foreach ($tasks as $task): ?>
      <tr>
         <td><?= $task['id'] ?></td>
         <td><?= $task['title'] ?></td>
         <td><?= $task['is_completed'] ? 'Terminée' :'En cours' ?></td>
         <td>
            <?php if (!$task['is_completed']): ?>
            <form method="POST" action="/complete">
               <input type="hidden" name="id" value="<?= $task['id'] ?>">
               <button class="btn btn-primary" type="submit">Marquer comme terminée</button>
            </form>
            <?php endif; ?>
            <form method="POST" action="/delete">
               <input type="hidden" name="id" value="<?= $task['id'] ?>">
               <button class="btn btn-secondary" type="submit">Supprimer</button>
            </form>
         </td>
      </tr>
      <?php endforeach; ?>
   </tbody>
</table>

<h1 class="h1">Tâches pour le projet actuel:</h1>
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
                    <form method="POST" action="/complete">
                        <input type="hidden" name="id" value="<?= $task['id'] ?>">
                        <button class="btn btn-primary" type="submit">Marquer comme terminée</button>
                    </form>
                    <form method="POST" action="/delete">
                        <input type="hidden" name="id" value="<?= $task['id'] ?>">
                        <button class="btn btn-secondary" type="submit">Supprimer</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
