<link rel="stylesheet" href="/style.css">                                                                                                                                                                        
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">                                                                                                    
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>


<div class="login d-flex justify-content-center align-items-center vh-100">
	
	<div class="card shadow p-4" style="width:350px;">
	
		<div class="d-flex flex-row">
			<h2 class="">Login</h2>
			<img class="ms-auto mb-3" src="/images/ASI.jpg" alt="logo asi" width="86px">
		</div>
		
		<?php session_start();?>

		<form method="POST">

			<div class="form-group form-outline mb-3">
				<input class="form-control" type="text" name="username" placeholder="Username" required>
			</div>
			
			<div class="form-group form-outline mb-4">
				<input class="form-control" type="password" name="password" placeholder="Password" required>	
			</div>
			
			<button class="btn btn-primary w-100 mb-3" type="submit">Login</button>
			
			
		</form>
		<a class="btn btn-secondary ms-auto" href="/register">Sign-in</a>
	</div>
</div>
