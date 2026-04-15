<link rel="stylesheet" href="/style.css">                                                                                                                                                                        
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">                                                                                                    
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">

<nav class="navbar navbar-expand-lg custom-navbar sticky-top px-3" data-bs-theme="dark">
  <div class="container-fluid">

    <!-- Logo -->
    <a class="navbar-brand d-flex align-items-center" href="/">
      <img src="/images/ASI-blanc.png" class="logo me-2" alt="logo" height="40">
    </a>

    <!-- Toggle -->
    <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Content -->
    <div class="collapse navbar-collapse" id="navbarMain">

      <!-- Left menu -->
      <ul class="navbar-nav me-auto mb-2 mb-lg-0 align-items-lg-center gap-lg-2">

        <li class="nav-item">
          <a href="/" class="nav-link">
            <i class="bi bi-house-door me-1"></i>Accueil
          </a>
        </li>

        <li class="nav-item">
          <a href="/projects/tasks/view" class="btn btn-outline-light rounded-pill px-3">
            <i class="bi bi-grid-3x3-gap-fill me-1"></i>Vue globale
          </a>
        </li>

      </ul>

      <!-- Right menu -->
      <div class="d-flex flex-column flex-lg-row align-items-lg-center gap-2 mt-3 mt-lg-0">

        <!-- User -->
        <span class="text-white small me-lg-2">
          Bonjour <strong><?php echo $_SESSION["user"]; ?></strong>
        </span>

        <!-- Actions -->
        <a href="/register" class="btn btn-outline-light rounded-pill px-3">
          <i class="bi bi-person-plus me-1"></i>Créer un compte
        </a>

        <a href="/logout" class="btn btn-danger rounded-pill px-3">
          <i class="bi bi-box-arrow-right me-1"></i>Déconnexion
        </a>

      </div>

    </div>
  </div>
</nav>
