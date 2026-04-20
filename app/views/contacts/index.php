<?php include __DIR__ . '/../../../public/navbar.php'; ?>

<?php if (!empty($contacts)): ?>
    <h1>Contacts</h1>
    <table border="1" cellpadding="8" cellspacing="0">
        <thead>
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
<?php else: ?>
    <p>No contacts available.</p>
<?php endif; ?>