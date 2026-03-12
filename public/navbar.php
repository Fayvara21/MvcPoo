<link rel="stylesheet" href="/style.css">                                                                                                                                                                        
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">                                                                                                    
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<nav class="navbar navbar-expand-lg custom-navbar" data-bs-theme="light">
  <div class="container-fluid">
    <a class="navbar-brand text-white fw-bold" href="/">ASI CB12</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" 
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    
<div class="collapse navbar-collapse" id="navbarSupportedContent">
  <ul class="navbar-nav me-auto mb-2 mb-lg-0 align-items-center">
    <li class="logo me-2">
      <!-- Logo placeholder -->
    </li>
    <li class="nav-item">
      <a href="/" class="btn btn-link nav-link text-decoration-none" aria-current="page">
        <i class="bi bi-house-door me-1"></i>Home
      </a>
    </li>       
    <li class="nav-item">
      <a href="/register" class="btn btn-outline-light rounded-pill px-4 mx-1">
        <i class="bi bi-person-plus me-1"></i>Register
      </a>
    </li>		
    <li class="nav-item">
      <a href="/logout" class="btn btn-outline-light rounded-pill px-4 mx-1">
        <i class="bi bi-box-arrow-right me-1"></i>Logout
      </a>
    </li>		
  </ul>
  <form class="d-flex" role="search">
    <input class="form-control me-2 rounded-pill" type="search" placeholder="Search" aria-label="Search">
    <button class="btn btn-outline-light rounded-pill px-4" type="submit">
      <i class="bi bi-search me-1"></i>Search
    </button>
  </form>
</div>

<!-- Add Bootstrap Icons if not already included -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<style>
/* Keep nav-link style for Home to maintain consistency */
.navbar .btn-link {
  color: rgba(255,255,255,0.85);
  text-decoration: none;
  font-weight: 500;
}

.navbar .btn-link:hover {
  color: white;
  background-color: rgba(255,255,255,0.1);
}

/* Button styling */
.navbar .btn-outline-light {
  border-width: 1px;
  transition: all 0.2s ease;
}

.navbar .btn-outline-light:hover {
  background-color: white;
  color: #0d6efd !important;
  transform: translateY(-1px);
}

/* Adjust spacing */
.navbar-nav {
  gap: 4px;
}

@media (max-width: 991px) {
  .navbar-nav .btn {
    margin: 4px 0 !important;
    width: 100%;
    text-align: left;
  }
  
  .navbar-nav {
    align-items: flex-start !important;
  }
}
</style>
	
  </div>
</nav>