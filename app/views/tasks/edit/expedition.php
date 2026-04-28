<div id="expeditionFields" class="card shadow-sm border-0 mb-3"
    style="display: <?= $selectedType === 'expedition' ? 'block' : 'none' ?>;">
    <div class="card-body">
        <div class="d-flex align-items-center gap-2 mb-3">
            <span class="badge bg-danger">EXPEDITION</span>
            <h5 class="fw-semibold mb-0">Informations Tiers</h5>
        </div>

        <button type="button" id="addexpedition" class="btn btn-sm btn-danger mb-3">+ Ajouter Équipement</button>

        <div id="expeditionList">
            <?php foreach ($expeditionList as $i => $t): ?>
                <div class="expedition-item border rounded p-2 mb-2">
                    <div class="d-flex flex-column gap-2">
                        <div>
                            <label class="form-label fw-medium">PN</label>
                            <input class="form-control" name="expedition[<?= $i ?>][pn]" value="<?= e($t['pn'] ?? '') ?>">
                        </div>
                        <div>
                            <label class="form-label fw-medium">Nom / Équipement</label>
                            <input class="form-control" name="expedition[<?= $i ?>][name]"
                                value="<?= e($t['name'] ?? '') ?>">
                        </div>
                        <div>
                            <label class="form-label fw-medium">Quantité</label>
                            <input class="form-control" type="number" name="expedition[<?= $i ?>][nb]"
                                value="<?= (int) ($t['nb'] ?? 1) ?>">
                        </div>
                        <div>
                            <label class="form-label fw-medium">Localisation</label>
                            <input class="form-control" name="expedition[<?= $i ?>][location]"
                                value="<?= e($t['location'] ?? '') ?>">
                        </div>
                        <div>
                            <label class="form-label fw-medium">N° Commande</label>
                            <input class="form-control" name="expedition[<?= $i ?>][order_nb]"
                                value="<?= e($t['order_nb'] ?? '') ?>">
                        </div>
                        <div>
                            <label class="form-label fw-medium">Destination</label>
                            <input class="form-control" name="expedition[<?= $i ?>][destination]"
                                value="<?= e($t['destination'] ?? '') ?>">
                        </div>
                        <div>
                            <label class="form-label fw-medium">Compte</label>
                            <input class="form-control" name="expedition[<?= $i ?>][account]"
                                value="<?= e($t['account'] ?? '') ?>">
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="expedition[<?= $i ?>][third_party]"
                                value="1" id="third_party_<?= $i ?>" <?= isset($t['third_party']) && $t['third_party'] ? 'checked' : '' ?>>
                            <label class="form-check-label" for="third_party_<?= $i ?>">
                                Tiers
                            </label>
                        </div>
                        <?php if (!empty($t['ID'])): ?>
                            <input type="hidden" name="expedition[<?= $i ?>][ID]" value="<?= $t['ID'] ?>">
                        <?php endif; ?>
                        <button type="button" class="btn btn-danger btn-sm remove-third-party mt-1">✕</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>