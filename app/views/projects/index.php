<?php include __DIR__ . '/../../../public/navbar.php'; ?>
<h1 class="h1">Projets</h1>
<a href="/projects/create">Ajouter un nouveau projet</a>
<ul>
    <?php foreach ($projects as $project): ?>
        <li>
            <a class="h2" href="/projects/<?= $project['id'] ?>/tasks"><?= $project['title'] ?></a>
        </li>
    <?php endforeach; ?>
</ul>
