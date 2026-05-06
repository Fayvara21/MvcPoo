<link rel="stylesheet" href="/style.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">

<style>
  .sidebar {
    width: 280px;
    background: #212529;
    transition: all 0.3s;
  }
  
  .sidebar .nav-link {
    color: #fff;
    border-radius: 0.5rem;
    transition: all 0.2s;
  }
  
  .sidebar .nav-link:hover {
    background: rgba(255,255,255,0.1);
    transform: translateX(5px);
  }
  
  .sidebar .nav-link.active {
    background: #0d6efd;
    color: white;
  }
  
  .logo-sidebar {
    max-height: 40px;
    width: auto;
  }
  
  .dropdown-menu-dark {
    background-color: #2b3035;
    border-color: #373b3e;
  }
  
  .dropdown-menu-dark .dropdown-item:hover {
    background-color: #0d6efd;
  }
  
  .btn-sidebar-action {
    width: 100%;
    margin-bottom: 0.5rem;
  }
  
  @media (max-width: 768px) {
    .sidebar {
      width: 100%;
      position: relative;
    }
  }
</style>

<div class="d-flex flex-column flex-shrink-0 p-3 text-white bg-dark sidebar" style="width: 280px; min-height: 100vh;">
  
  <!-- Logo -->
  <a href="/" class="d-flex align-items-center mb-3 mb-md-0 text-white text-decoration-none">
    <img src="/images/ASI-blanc.png" class="logo-sidebar me-2" alt="logo">
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
    <a href="/contact" class="btn btn-outline-light rounded-pill px-3 btn-sidebar-action">
      <i class="bi bi-send-exclamation-fill me-1"></i>Contact
    </a>
    
    <a href="/register" class="btn btn-outline-light rounded-pill px-3 btn-sidebar-action">
      <i class="bi bi-person-plus me-1"></i>Créer un compte
    </a>
    
    <a href="/logout" class="btn btn-outline-danger rounded-pill px-3 btn-sidebar-action">
      <i class="bi bi-box-arrow-right me-1"></i>Déconnexion
    </a>
  </div>
  
  <hr>
  
  <!-- Dropdown utilisateur (optionnel) -->
  <div class="dropdown mt-auto">
    <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
      <i class="bi bi-person-circle me-2" style="font-size: 1.5rem;"></i>
      <strong><?php echo $_SESSION["user"]; ?></strong>
    </a>
    <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser1">
      <!-- <li><a class="dropdown-item" href="/profile">Profil</a></li>
      <li><a class="dropdown-item" href="/settings">Paramètres</a></li>
      <li><hr class="dropdown-divider"></li> -->
      <li><a class="dropdown-item" href="/logout">Déconnexion</a></li>
    </ul>
  </div>
</div>

<script>
  // Gestion de l'effet lenticulaire sur les éléments avec la classe .lenticular
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
  
  // Marquer le lien actif dans la sidebar
  document.addEventListener('DOMContentLoaded', () => {
    const currentPath = window.location.pathname;
    document.querySelectorAll('.sidebar .nav-link').forEach(link => {
      if (link.getAttribute('href') === currentPath) {
        link.classList.add('active');
      }
    });
  });
</script>