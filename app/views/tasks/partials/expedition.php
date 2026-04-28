<div id="expeditionFields" class="card shadow-sm border-0 mb-3" style="display: none;">
    <div class="card-body">
        <div class="d-flex align-items-center gap-2 mb-3">
            <span class="badge bg-danger">EXPEDITION</span>
            <h5 class="fw-semibold mb-0">Informations Expedition</h5>
        </div>

        <button type="button" id="addExpedition" class="btn btn-sm btn-danger mb-3">
            + Ajouter Équipement
        </button>

        <div id="expeditionList"></div>

        <hr>

        <div class="mb-2">
            <label class="form-label fw-medium">Emplacement :</label>
            <input class="form-control" name="expedition_location" placeholder="Emplacement">
        </div>
        <div class="mb-2">
            <label class="form-label fw-medium">Destinataire :</label>
            <input class="form-control" name="expedition_destination" placeholder="Destinataire">
        </div>
        <div class="mb-2">
            <label class="form-label fw-medium">N° Commande :</label>
            <input class="form-control" name="expedition_order_nb" placeholder="N° Commande">
        </div>

        <!-- Account Field (hidden by default) -->
        <div class="mb-2" id="expedition_account_field" style="display: none;">
            <label class="form-label fw-medium">Compte :</label>
            <input class="form-control" name="expedition_account" placeholder="Compte">
        </div>

        <!-- Third Party Checkbox -->
        <div class="form-check mb-2">
            <input class="form-check-input" type="checkbox" name="expedition_third_party" value="1"
                id="expedition_third_party">
            <label class="form-check-label" for="expedition_third_party">
                Third Party
            </label>
        </div>

        <input type="hidden" name="expedition_account_default" value="ASI">
    </div>
</div>