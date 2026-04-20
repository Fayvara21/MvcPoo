<?php include __DIR__ . '/../../../public/navbar.php'; ?>

<div class="container mt-5">
    <?php if (!empty($contacts)): ?>
        <h1 class="mb-4">Contacts</h1>

        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Email</th>
                        <th>Type</th>
                        <th>Description</th>
                        <th>User</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($contacts as $contact): ?>
                        <tr>
                            <td><?= htmlspecialchars($contact['id'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($contact['email'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($contact['type'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= nl2br(htmlspecialchars($contact['description'], ENT_QUOTES, 'UTF-8')) ?></td>
                            <td><?= htmlspecialchars($contact['user'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($contact['created_at'], ENT_QUOTES, 'UTF-8') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    <?php else: ?>
        <div class="alert alert-info">No contacts available.</div>
    <?php endif; ?>
</div>