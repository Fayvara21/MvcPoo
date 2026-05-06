<div class="container main-content mt-5 mb-4">
    <div class="card shadow-sm border-1">
        <div class="card-body px-2 py-4">
            <h1 class="mb-4 text-center">Créer un rapport d'incident</h1>

            <form method="POST" action="/contact">
                
                <div class="mb-3">
                    <label class="form-label">Votre email:</label>
                    <input type="email" name="email" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Type de problème:</label>
                    <select name="type" class="form-select" required>
                        <option value="">-- Problème --</option>
                        <option value="bug">Bug logiciel</option>
                        <option value="feature">Demande de modification</option>
                        <option value="crash">Erreur de demande</option>
                        <option value="other">Autre</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description:</label>
                    <textarea name="description" rows="6" class="form-control" required></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Personne ou Groupe concerné:</label>
                    <textarea name="user" rows="4" class="form-control"></textarea>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Envoyer le rapport</button>
                </div>

            </form>
        </div>
    </div>
</div>