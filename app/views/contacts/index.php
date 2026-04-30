<div class="container mt-5">
    <div class="card shadow border-0">
        <div class="card-body p-5">
            <?php if (!empty($contacts)): ?>
                <h1 class="mb-4">Tous les rapports d'incidents:</h1>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover align-middle">
                        <thead class="bg-secondary text-white">
                            <tr>
                                <th>ID</th>
                                <th>Email</th>
                                <th>Type</th>
                                <th>Description</th>
                                <th>Utilisateur</th>
                                <th>Dâte de création</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($contacts as $contact): ?>
                                <tr>
                                    <td><?= htmlspecialchars($contact['id'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($contact['email'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($contact['type'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td class="contactDescription"><?= nl2br(htmlspecialchars($contact['description'], ENT_QUOTES, 'UTF-8')) ?></td>
                                    <td><?= htmlspecialchars($contact['user'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($contact['created_at'], ENT_QUOTES, 'UTF-8') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    <?php else: ?>
        <div class="alert alert-info">No contacts available.</div>
    <?php endif; ?>
</div>