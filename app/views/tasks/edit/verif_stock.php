<div id="verifStockFields" class="card shadow-sm border-1 mb-3" style="display: <?= $selectedType === 'verif_stock' ? 'block' : 'none' ?>;">
    <div class="card-body">
        <div class="d-flex align-items-center gap-2 mb-3">
            <span class="badge bg-success">VERIF STOCK</span>
            <h5 class="fw-semibold mb-0">Informations Vérification Stock</h5>
        </div>

        <button type="button" id="addVerifStock" class="btn btn-sm btn-success mb-3">+ Ajouter PN</button>

        <div id="verifStockList">
            <?php foreach ($verifStockList as $i => $v): ?>
                <div class="verif-stock-item border rounded p-2 mb-2">
                    <div class="d-flex flex-column gap-2">
                        <div>
                            <label class="form-label fw-medium">PN</label>
                            <input class="form-control" name="verif_stock[<?= $i ?>][pn]" value="<?= e($v['pn'] ?? '') ?>" required>
                        </div>
                        <div>
                            <label class="form-label fw-medium">Quantité</label>
                            <input class="form-control" type="number" name="verif_stock[<?= $i ?>][nb]" value="<?= (int) ($v['nb'] ?? 1) ?>">
                        </div>
                        <div>
                            <label class="form-label fw-medium">Libellé</label>
                            <input class="form-control" name="verif_stock[<?= $i ?>][name]" value="<?= e($v['name'] ?? '') ?>">
                        </div>
                        <div>
                            <label class="form-label fw-medium">Emplacement</label>
                            <input class="form-control" name="verif_stock[<?= $i ?>][location]" value="<?= e($v['location'] ?? '') ?>">
                        </div>
                        <div>
                            <label class="form-label fw-medium">Remarques</label>
                            <input class="form-control" name="verif_stock[<?= $i ?>][remarks]" value="<?= e($v['remarks'] ?? '') ?>">
                        </div>
                        <?php if (!empty($v['id'])): ?>
                            <input type="hidden" name="verif_stock[<?= $i ?>][id]" value="<?= $v['id'] ?>">
                        <?php endif; ?>
                        <button type="button" class="btn btn-danger btn-sm remove-verif-stock mt-1">✕</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>