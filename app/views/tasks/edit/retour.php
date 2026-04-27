<div id="retourFields" class="card shadow-sm border-0" style="display: <?= $selectedType === 'retour' ? 'block' : 'none' ?>;">
    <div class="card-body">
        <div class="d-flex align-items-center gap-2 mb-3">
            <span class="badge bg-warning text-dark">RETOUR</span>
            <h5 class="fw-semibold mb-0">Informations RETOUR</h5>
        </div>

        <button type="button" id="addRetour" class="btn btn-sm btn-warning mb-3">+ Ajouter retour</button>

        <div id="retourList">
            <?php foreach ($retourList as $i => $r): ?>
                <div class="retour-item border rounded p-2 mb-2">
                    <div class="d-flex flex-column gap-2">
                        <div>
                            <label class="form-label fw-medium">PN</label>
                            <input class="form-control" name="retour[<?= $i ?>][PN]" value="<?= e($r['PN'] ?? '') ?>">
                        </div>
                        <div>
                            <label class="form-label fw-medium">Quantité</label>
                            <input class="form-control" type="number" name="retour[<?= $i ?>][nb]" value="<?= (int) ($r['nb'] ?? 1) ?>">
                        </div>
                        <div>
                            <label class="form-label fw-medium">SN</label>
                            <input class="form-control" name="retour[<?= $i ?>][sn]" value="<?= e($r['sn'] ?? '') ?>">
                        </div>
                        <div>
                            <label class="form-label fw-medium">Certification</label>
                            <input class="form-control" name="retour[<?= $i ?>][certif]" value="<?= e($r['certif'] ?? '') ?>">
                        </div>
                        <?php if (!empty($r['id'])): ?>
                            <input type="hidden" name="retour[<?= $i ?>][id]" value="<?= $r['id'] ?>">
                        <?php endif; ?>
                        <button type="button" class="btn btn-danger btn-sm remove-retour mt-1">✕</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>