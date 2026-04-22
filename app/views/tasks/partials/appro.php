<div id="approFields" class="card shadow-sm border-0 mb-3" style="display:none;">
    <div class="card-body">

        <div class="d-flex align-items-center gap-2 mb-3">
            <span class="badge bg-primary">APPRO</span>
            <h5 class="fw-semibold mb-0">Informations APPRO</h5>
        </div>

        <button type="button" id="addAppro" class="btn btn-sm btn-primary mb-3">
            + Ajouter PN
        </button>

        <div id="approFields" class="card shadow-sm border-0 mb-3" style="display:none;">
            <div class="card-body">
                <div class="d-flex align-items-center gap-2 mb-3"> <span class="badge bg-primary">APPRO</span>
                    <h5 class="fw-semibold mb-0">Informations APPRO</h5>
                </div> <!-- MULTI ROW --> <button type="button" id="addAppro" class="btn btn-sm btn-primary mb-3"> +
                    Ajouter PN </button>
                <div id="approList">
                    <div class="border rounded p-2 mb-2">
                        <div class="row g-2">
                            <div class="col"> <input class="form-control" name="appro[0][pn]" placeholder="PN" required>
                            </div>
                            <div class="col"> <input class="form-control" name="appro[0][location]"
                                    placeholder="Emplacement"> </div>
                            <div class="col"> <input class="form-control" type="number" name="appro[0][nb]" value="1">
                            </div>
                            <div class="col-auto"> <button type="button" class="btn btn-danger remove">✕</button> </div>
                        </div>
                    </div>
                </div>

                <hr>

                <input class="form-control mb-2" name="appro_designation">
                <input class="form-control mb-2" name="appro_of" required>
                <input class="form-control mb-2" name="appro_plane">
                <input class="form-control mb-2" name="appro_oe">

            </div>
        </div>