<div id="approFields" class="card shadow-sm border-0 mb-3" style="display: <?= $selectedType === 'appro' ? 'block' : 'none' ?>;">
    <div class="card-body">
        <div class="d-flex align-items-center gap-2 mb-3">
            <span class="badge bg-primary">APPRO</span>
            <h5 class="fw-semibold mb-0">Informations APPRO</h5>
        </div>

        <button type="button" id="addAppro" class="btn btn-sm btn-primary mb-3">+ Ajouter PN</button>

        <div id="approList">
            <?php foreach ($approList as $i => $a): ?>
                <div class="appro-item border rounded p-2 mb-2">
                    <div class="d-flex flex-column gap-2">
                        <div>
                            <label class="form-label fw-medium">PN</label>
                            <input class="form-control" name="appro[<?= $i ?>][pn]" value="<?= e($a['pn'] ?? '') ?>" required>
                        </div>
                        <div>
                            <label class="form-label fw-medium">Quantité</label>
                            <input class="form-control" type="number" name="appro[<?= $i ?>][nb]" value="<?= (int)($a['nb'] ?? 1) ?>">
                        </div>
                        <div>
                            <label class="form-label fw-medium">Désignation</label>
                            <input class="form-control" name="appro[<?= $i ?>][designation]" value="<?= e($a['designation'] ?? '') ?>">
                        </div>
                        <div>
                            <label class="form-label fw-medium">OF</label>
                            <input class="form-control" name="appro[<?= $i ?>][of]" value="<?= e($a['of'] ?? '') ?>">
                        </div>
                        <div>
                            <label class="form-label fw-medium">Emplacement</label>
                            <input class="form-control" name="appro[<?= $i ?>][location]" value="<?= e($a['location'] ?? '') ?>">
                        </div>
                        <div>
                            <label class="form-label fw-medium">Avion</label>
                            <input class="form-control" name="appro[<?= $i ?>][plane]" value="<?= e($a['plane'] ?? '') ?>">
                        </div>
                        <div>
                            <label class="form-label fw-medium">OE</label>
                            <input class="form-control" name="appro[<?= $i ?>][oe]" value="<?= e($a['oe'] ?? '') ?>">
                        </div>
                        <?php if (!empty($a['id'])): ?>
                            <input type="hidden" name="appro[<?= $i ?>][id]" value="<?= $a['id'] ?>">
                        <?php endif; ?>
                        <button type="button" class="btn btn-danger btn-sm remove-appro mt-1">✕</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>