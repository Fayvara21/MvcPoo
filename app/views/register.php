<link rel="stylesheet" href="/style.css">                                                                                                                                                                        
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">                                                                                                    
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<div class="register d-flex justify-content-center align-items-center vh-100">

	<div class="card shadow p-4" style="width:350px;">
	
		<div class="d-flex flex-row">
			<h2 class="">Créer un compte</h2>
			<img class="ms-auto mb-3" src="/images/ASI.jpg" alt="logo asi" width="86px">
		</div>

		<?php session_start(); ?>

		<?php if (isset($_SESSION['error'])): ?>
		    <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
		<?php endif; ?>

		<?php if (isset($_SESSION['success'])): ?>
		    <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
		<?php endif; ?>


		<form method="POST">

			<div class="form-group form-outline mb-3">
				<input class="form-control" type="text" name="username" placeholder="Nom d'utilisateur" required>
			</div>

			<div class="form-group form-outline mb-3">
				<input class="form-control" type="password" name="password" placeholder="Mot de passe" required>    
			</div>

			<div class="form-group form-outline mb-4">
				<select class="form-select" name="part" required>
					<option value="" disabled selected>-- Groupe --</option>
					<option value="magasin">Magasin</option>
					<option value="adv">ADV</option>
					<option value="part145">PART145</option>
					<option value="part21">PART21</option>
					<option value="qualite">Qualité</option>
					<option value="be">BE</option>

					<?php if ($_SESSION['group'] === 'admin'): ?>
						<option value="admin">Admin</option>
					<?php endif; ?>
				</select>
			</div>

			<button class="btn btn-primary w-100 mb-3" type="submit">Créer un compte</button>

		</form>

		<a class="btn btn-secondary ms-auto" href="/login">Connexion</a>

	</div>

</div>


