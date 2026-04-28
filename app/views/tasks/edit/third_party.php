<div id="thirdPartyFields" class="card shadow-sm border-0 mb-3" style="display: <?= $selectedType === '3rd_party' ? 'block' : 'none' ?>;">
    <div class="card-body">
        <div class="d-flex align-items-center gap-2 mb-3">
            <span class="badge bg-danger">3RD PARTY</span>
            <h5 class="fw-semibold mb-0">Informations Tiers</h5>
        </div>

        <button type="button" id="addThirdParty" class="btn btn-sm btn-danger mb-3">+ Ajouter Équipement</button>

        <div id="thirdPartyList">
            <?php foreach ($thirdPartyList as $i => $t): ?>
                <div class="third-party-item border rounded p-2 mb-2">
                    <div class="d-flex flex-column gap-2">
                        <div>
                            <label class="form-label fw-medium">BP</label>
                            <input class="form-control" name="third_party[<?= $i ?>][bp]" value="<?= e($t['bp'] ?? '') ?>">
                        </div>
                        <div>
                            <label class="form-label fw-medium">Équipement</label>
                            <input class="form-control" name="third_party[<?= $i ?>][equipement]" value="<?= e($t['equipement'] ?? '') ?>">
                        </div>
                        <div>
                            <label class="form-label fw-medium">Quantité</label>
                            <input class="form-control" type="number" name="third_party[<?= $i ?>][nb]" value="<?= (int) ($t['nb'] ?? 1) ?>">
                        </div>
                        <div>
                            <label class="form-label fw-medium">Destinataire</label>
                            <input class="form-control" name="third_party[<?= $i ?>][destination]" value="<?= e($t['destination'] ?? '') ?>">
                        </div>
                        <div>
                            <label class="form-label fw-medium">N° Commande</label>
                            <input class="form-control" name="third_party[<?= $i ?>][order_nb]" value="<?= e($t['order_nb'] ?? '') ?>">
                        </div>
                        <?php if (!empty($t['ID'])): ?>
                            <input type="hidden" name="third_party[<?= $i ?>][ID]" value="<?= $t['ID'] ?>">
                        <?php endif; ?>
                        <button type="button" class="btn btn-danger btn-sm remove-third-party mt-1">✕</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>