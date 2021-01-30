<?php
//Head Links
require_once 'assets/connect/pdo.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
<?php
require_once 'assets/connect/head.php';
?>
</head>

<body>
	<header>
		<nav class="navbar navbar-expand-lg navbar-light sticky-top">
			<a class="navbar-brand" href="#">LU EXAM HIVE</a>
			<button type="button" class="btn btn-sm btn-dark rounded-0 ml-3">
				<a href="index.php" class="text-white text-decoration-none">Back</a>

		</nav>
	</header>

	<!--Contact us Start(1712020128) -->

	<main>
		<div class="container">
			<h2 class="display-4 mt-4">Contact Us</h2>

			<div class="row">
				<div class="col mt-3">
					<p class="text-justify">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Cupiditate
						obcaecati incidunt veritatis totam!</p>
				</div>
			</div>
			<form>

				<div class="form-group">
					<div class="row">
						<div class="col">
							<input type="text" class="form-control" placeholder="Name" required>
						</div>
						<div class="col">
							<input type="email" class="form-control" placeholder="Email Address" required>
						</div>
					</div>
				</div>

				<div class="form-group">
					<div class="row">
						<div class="col">
							<div class="mb-3">
								<textarea class="form-control" id="exampleFormControlTextarea1" rows="8"
									placeholder="Your text"></textarea>
								<div class="d-flex justify-content-end">
									<input class="btn btn-dark mt-3" id="btn" type="submit" value="Submit">
								</div>
							</div>
						</div>
					</div>
				</div>

			</form>

		</div>
	</main>

	<!--Contact us End(1712020128) -->

	<?php
//Head Links
require_once 'assets/connect/footer.php';
?>
</body>

</html>