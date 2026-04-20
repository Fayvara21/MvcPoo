<?php include __DIR__ . '/../../../public/navbar.php'; ?>

<div class="container">
    <h1>Demande de contact</h1>
    <form method="POST" action="/contact">
        <label>Votre email:</label>
        <input type="email" name="email" required>

        <label>Type de problème:</label>
        <select name="type" required>
            <option value="">-- Problème --</option>
            <option value="bug">Bug logiciel</option>
            <option value="feature">Demande de modification</option>
            <option value="crash">Erreur de demande</option>
            <option value="other">Autre</option>
        </select>

        <label>Description:</label>
        <textarea name="description" rows="6" required></textarea>

        <label>Personne  ou Groupe concerné:</label>
        <textarea name="user" rows="4"></textarea>

        <button type="submit">Submit Report</button>
    </form>
</div>