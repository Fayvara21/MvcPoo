<h1 class="h1">Projets</h1>
<a href="/projects/create">Ajouter un nouveau projet</a>
<ul>
    <?php foreach ($projects as $project): ?>
        <li>
            <a href="/projects/<?= $project['id'] ?>/tasks"><?= $project['name'] ?></a>
        </li>
    <?php endforeach; ?>
</ul>
