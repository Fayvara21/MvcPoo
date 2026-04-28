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

        <!-- Compte de transport tiers checkbox (controls account field visibility) -->
        <div class="form-check">
            <input class="form-check-input compte-trigger" type="checkbox" value="1" id="compte_transport_<?= $i ?>"
                <?= isset($t['account']) && $t['account'] ? 'checked' : '' ?>>
            <label class="form-check-label" for="compte_transport_<?= $i ?>">
                Compte de transport tiers
            </label>
        </div>
        
        <div class="mb-2" id="expedition_account_field" style="display: none;">
            <label class="form-label fw-medium">Compte :</label>
            <input class="form-control" name="expedition_account" placeholder="Compte">
        </div>

        <!-- Third Party Checkbox -->
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="expedition[<?= $i ?>][third_party]" value="1"
                id="third_party_<?= $i ?>" <?= isset($t['third_party']) && $t['third_party'] ? 'checked' : '' ?>>
            <label class="form-check-label" for="third_party_<?= $i ?>">
                Third Party
            </label>
        </div>

        <input type="hidden" name="expedition_account_default" value="ASI">
    </div>
</div>