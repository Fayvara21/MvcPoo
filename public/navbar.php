<link rel="stylesheet" href="/style.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<style>
  /* Layout principal - Sidebar fixe à gauche */
  .app-wrapper {
    display: flex;
    min-height: 100vh;
  }

  .sidebar {
    width: 280px;
    position: fixed;
    top: 0;
    left: 0;
    height: 100vh;
    overflow-y: auto;
    background: #212529;
    z-index: 1000;
  }

  .main-content {
    flex: 1;
    margin-left: 280px;
    min-height: 100vh;
    padding: 20px;
  }

  /* Responsive pour mobile */
  @media (max-width: 768px) {
    .sidebar {
      transform: translateX(-100%);
      transition: transform 0.3s ease;
      width: 280px;
    }

    .sidebar.open {
      transform: translateX(0);
    }

    .main-content {
      margin-left: 0;
    }

    .menu-toggle {
      display: block;
      position: fixed;
      top: 15px;
      left: 15px;
      z-index: 1001;
      background: #013966;
      color: white;
      border: none;
      border-radius: 5px;
      padding: 8px 12px;
      cursor: pointer;
    }
  }

  @media (min-width: 769px) {
    .menu-toggle {
      display: none;
    }
  }
</style>

<!-- Bouton toggle pour mobile -->
<button class="menu-toggle" id="menuToggle">
  <i class="bi bi-list"></i>
</button>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
  <div class="d-flex flex-column flex-shrink-0 p-3 text-white bg-dark" style="height: 100vh;">
    <!-- Logo -->
    <a href="/" class="d-flex align-items-center mb-3 mb-md-0 text-white text-decoration-none">
      <img src="/images/ASI-blanc.png" class="logo-sidebar me-2" alt="logo" style="max-height: 40px;">
      <span class="fs-4">ASI</span>
    </a>

    <hr>

    <!-- Menu principal -->
    <ul class="nav nav-pills flex-column mb-auto">
      <li class="nav-item">
        <a href="/" class="nav-link">
          <i class="bi bi-house-door me-2"></i>
          Accueil
        </a>
      </li>
      <li class="nav-item">
        <a href="/projects/tasks/view" class="nav-link">
          <i class="bi bi-grid-3x3-gap-fill me-2"></i>
          Vue globale
        </a>
      </li>
    </ul>

    <hr>

    <!-- Section utilisateur -->
    <div class="mb-3">
      <div class="text-white small mb-2">
        Bonjour <strong><?php echo $_SESSION["user"]; ?></strong>
      </div>
    </div>

    <!-- Actions -->
    <div class="mb-3">
      <a href="/contact" class="btn btn-outline-light rounded-pill px-3 w-100 mb-2">
        <i class="bi bi-send-exclamation-fill me-1"></i>Contact
      </a>

      <a href="/register" class="btn btn-outline-light rounded-pill px-3 w-100 mb-2">
        <i class="bi bi-person-plus me-1"></i>Créer un compte
      </a>

      <a href="/logout" class="btn btn-outline-danger rounded-pill px-3 w-100">
        <i class="bi bi-box-arrow-right me-1"></i>Déconnexion
      </a>
    </div>

    <hr>

    <!-- Dropdown utilisateur -->
    <div class="dropdown mt-auto">
      <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1"
        data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-person-circle me-2" style="font-size: 1.5rem;"></i>
        <strong><?php echo $_SESSION["user"]; ?></strong>
      </a>
      <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser1">
        <li><a class="dropdown-item" href="/profile">Profil</a></li>
        <li><a class="dropdown-item" href="/settings">Paramètres</a></li>
        <li>
          <hr class="dropdown-divider">
        </li>
        <li><a class="dropdown-item" href="/logout">Déconnexion</a></li>
      </ul>
    </div>
  </div>
</div>



<script>
  // Toggle sidebar sur mobile
  document.getElementById('menuToggle')?.addEventListener('click', function () {
    document.getElementById('sidebar').classList.toggle('open');
  });

  // Fermer la sidebar quand on clique sur un lien (mobile)
  document.querySelectorAll('.sidebar .nav-link, .sidebar .btn').forEach(link => {
    link.addEventListener('click', () => {
      if (window.innerWidth <= 768) {
        document.getElementById('sidebar').classList.remove('open');
      }
    });
  });

  // Gestion de l'effet lenticulaire
  document.querySelectorAll('.lenticular').forEach(card => {
    card.addEventListener('mousemove', (e) => {
      const rect = card.getBoundingClientRect();
      const x = (e.clientX - rect.left) / rect.width - 0.5;
      const y = (e.clientY - rect.top) / rect.height - 0.5;
      card.style.transform = `perspective(1000px) rotateX(${y * 5}deg) rotateY(${x * 5}deg)`;
    });
    card.addEventListener('mouseleave', () => {
      card.style.transform = 'perspective(1000px) rotateX(0) rotateY(0)';
    });
  });
</script>