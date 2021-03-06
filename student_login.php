<?php
session_start();
require_once "assets/connect/pdo.php";

//Form Validation
if (isset($_POST['Student_Email']) && isset($_POST['Password']) && isset($_POST['login'])) {

    //Checks length of email & password
    if (strlen($_POST['Student_Email']) < 1 || strlen($_POST['Password']) < 1) {
        $_SESSION['error'] = "User name and password are required";
        header("Location: student_login.php");
        return;

        //Checks email format.
    } else if (strpos($_POST['Student_Email'], "@") === false) {
        $_SESSION['error'] = "Email must have an at-sign (@)";
        header("Location: student_login.php");
        return;

        //If Credencials are Correct:
    } else {

        $salt = '8JDs,=-w^q;-57Jc,ZP:g[=8[r+=FC';
        $Password = md5($salt . $_POST['Password']);
        $Student_Email_Status = 'verified';

        $stmt = $pdo->prepare('SELECT Student_ID, Batch, Section, FirstName, LastName FROM student WHERE Student_Email = :em AND Password = :pw AND Student_Email_Status = :ses');
        $stmt->execute(array(':em' => $_POST['Student_Email'], ':ses' => $Student_Email_Status, ':pw' => $Password));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row !== false) {
            $_SESSION['Student_ID'] = $row['Student_ID'];
            $_SESSION['Batch'] = $row['Batch'];
            $_SESSION['Section'] = $row['Section'];
						$_SESSION['FirstName'] = $row['FirstName'];
						$_SESSION['LastName'] = $row['LastName'];
            $batch = $row['Batch'];
            $sec = $row['Section'];
            header("Location: student_dashboard.php?batch=$batch&sec=$sec");
            return;
        } else {
            $_SESSION['error'] = "Incorrect password or Email";
            header("Location: student_login.php");
            return;
        }
    }
}
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
			<div class="container justify-content-start">
			<a class="navbar-brand" href="index.php"><img id="logo" src="assets/images/LuExamHiveLogo.png" height="30px"> LU EXAM HIVE</a>
				<a type="button" href="index.php" class="btn btn-sm btn-outline-dark ml-3"><i class="fas fa-arrow-left"></i> Go Back</a>
			</div>
		</nav>

	</header>

	<!--Student Login/Registration Start -->
	<main>
	<div class="container">
  <div class="row">

    <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 my-5 align-self-center order-xl-2 order-lg-2 order-md-1 order-sm-1 order-xs-1">
	<img src="assets/images/student.jpg" class="card-img" alt="Student">
	<h1 class="my-2 display-4 d-flex justify-content-center">Student Login</h1>
	<p class='ml-2 d-flex justify-content-center'> User passwords are encrypted.</p>
	<p class="my-3 ml-2 d-flex justify-content-center">New user? <a href="student_Registration.php"> Get started with LU Exam Hive</a></p>
	</div>

    <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-5 align-self-center order-xl-1 order-lg-1 order-md-2 order-sm-2 order-xs-2">

	<?php
if (isset($_SESSION['error'])) {
    echo ('<p class="alert alert-danger">' . htmlentities($_SESSION['error']) . "</p>\n");
    unset($_SESSION['error']);
} else if (isset($_SESSION['registered'])) {
    echo ('<p class="alert alert-success">' . htmlentities($_SESSION['registered']) . "</p>\n");
    unset($_SESSION['registered']);
}
?>
					<form method="POST" action="student_login.php">
						<div class="form-group ">
							<input type="email" name="Student_Email" class="form-control " id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Email...">
							<small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone
								else.</small>
						</div>
						<div class="form-group">
							<div class="input-group">
								<input type="password" name="Password" class="form-control" id="exampleInputPassword1" placeholder="Password... ">
								<div class="input-group-append">
									<span class="input-group-text bg-transparent border-left-0" onclick = "togglePassword()"><i id="hide1" class="fa fa-eye" style="display:none" ></i>
									<i id="hide2" class="fa fa-eye-slash"></i></span>
								</div>								
							</div>
						</div>
						<div class="d-flex justify-content-end">
							<button type="submit" class="btn btn-dark"><a class="text-white text-decoration-none" href="student_Registration.php">Register <i class="fas fa-user-plus"></i></a></button>
							<button type="submit" name="login" class="btn btn-dark ml-2">Login <i class="fas fa-sign-in-alt"></i></button>
						</div>

						<div class="d-flex justify-content-end mt-5">
							<a href="request_reset_password.php">Forgot Your Password?</a>
						</div>
					</form>
	</div>


  </div>
</div>

</div>


	</main>
	<!--Student Login/Registration Start -->

	<!--footer Start -->
	<?php
require_once 'assets/connect/footer.php';
?>
	<!--footer End -->
</body>

</html>