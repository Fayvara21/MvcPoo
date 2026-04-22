<div id="retourFields" class="card shadow-sm border-0" style="display:none;">
    <div class="card-body">

        <div class="d-flex align-items-center gap-2 mb-3">
            <span class="badge bg-warning text-dark">RETOUR</span>
            <h5 class="fw-semibold mb-0">Informations RETOUR</h5>
        </div>

        <button type="button" id="addRetour" class="btn btn-sm btn-warning mb-3">
            + Ajouter PN
        </button>

        <div id="retourFields" class="card shadow-sm border-0" style="display:none;">
            <div class="card-body">
                <div class="d-flex align-items-center gap-2 mb-3"> <span
                        class="badge bg-warning text-dark">RETOUR</span>
                    <h5 class="fw-semibold mb-0">Informations RETOUR</h5>
                </div> <button type="button" id="addRetour" class="btn btn-sm btn-warning mb-3"> + Ajouter PN </button>
                <!-- MULTI ROW -->
                <div id="retourList">
                    <div class="border rounded p-2 mb-2">
                        <div class="row g-2">
                            <div class="col"> <input class="form-control" name="retour[0][pn]" placeholder="PN"
                                    required> </div>
                            <div class="col"> <input class="form-control" type="number" name="retour[0][nb]" value="1">
                            </div>
                            <div class="col-auto"> <button type="button" class="btn btn-danger remove">✕</button> </div>
                        </div>
                    </div>
                </div>

                <hr>

                <input class="form-control mb-2" name="retour_sn">
                <input class="form-control mb-2" name="retour_certif">

            </div>
        </div>