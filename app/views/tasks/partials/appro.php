<div id="approFields" class="card shadow-sm border-0 mb-3" style="display:none;">
    <div class="card-body">

        <div class="d-flex align-items-center gap-2 mb-3">
            <span class="badge bg-primary">APPRO</span>
            <h5 class="fw-semibold mb-0">Informations APPRO</h5>
        </div>

        <button type="button" id="addAppro" class="btn btn-sm btn-primary mb-3">
            + Ajouter PN
        </button>

        <div id="approFields" class="card shadow-sm border-0 mb-3">
            <div class="card-body">
                <div class="d-flex align-items-center gap-2 mb-3"> <span class="badge bg-primary">APPRO</span>
                    <h5 class="fw-semibold mb-0">Informations APPRO</h5>
                </div> <!-- MULTI ROW -->
                <button type="button" id="addAppro" class="btn btn-sm btn-primary mb-3" onclick="addApproItem()">
                    + Ajouter PN
                </button>
                <div id="approList">
                </div>

                <hr>

                <input class="form-control mb-2" name="appro_designation" placeholder="Désignation">
                <input class="form-control mb-2" name="appro_of" placeholder="OF" required>
                <input class="form-control mb-2" name="appro_plane" placeholder="Avion">
                <input class="form-control mb-2" name="appro_oe" placeholder="OE">

            </div>
        </div>