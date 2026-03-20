<link rel="stylesheet" href="/style.css">                                                                                                                                                                        
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">                                                                                                    
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">

<nav class="navbar navbar-expand-lg custom-navbar sticky-top" data-bs-theme="light">
  <div class="container-fluid">
	<a class="navbar-brand" href="/"> <img class="logo" src="/images/ASI-blanc.png"></a>
    <!-- <a class="navbar-brand text-white fw-bold" href="/"><h3 class="h3 fw-bold">CB12</h3></a> -->
    <p class="text-white mb-0">Bienvenue <?php echo $_SESSION["user"] ?> !</p>
    <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" 
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
	<i class="bi bi-house-door me-1"></i>Accueil
      </a>
    </li>
	<li>
		<a href="/projects/tasks/view" class="btn btn-outline-light rounded-pill px-4 mx-1">
			<i class="bi bi-grid-3x3-gap-fill me-2"></i>Vue globale
        </a>
	</li>
    <li class="nav-item">
      <a href="/register" class="btn btn-outline-light rounded-pill px-4 mx-1">
        <i class="bi bi-person-plus me-1"></i>Créer un compte
      </a>
    </li>		
    <li class="nav-item">
      <a href="/logout" class="btn btn-outline-light rounded-pill px-4 mx-1">
        <i class="bi bi-box-arrow-right me-1"></i>Déconnexion
      </a>
    </li>
	
  </ul> <!--
	<form class="d-flex align-items-center me-3" role="search"> 	  <input class="form-control mx-1 rounded-pill me-2" type="search" placeholder="Search" aria-label="Search">
	  <button class="btn btn-outline-light rounded-pill px-4" type="submit">
		<i class="bi bi-search"></i>  
	  </button>
	</form> -->
</div>

<!-- Add Bootstrap Icons if not already included -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<style>

</style>
	
  </div>
</nav>
