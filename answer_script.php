<?php
session_start();
require_once "assets/connect/pdo.php";

if (!isset($_SESSION['Student_ID']) && !isset($_SESSION['Batch']) && !isset($_SESSION['Section'])) {
  header("Location: student_login.php");
  return;
}

// Variables declared as empty for persisting data on the form
$name = $student_id = $batch = $section = $success = $failed = $answer = '';

// errors array to put all the error message in the array
$errors = array('name' => '', 'student_id' => '', 'batch' => '', 'section' => '', 'answer' => '');




//fetching questions and other data
if (isset($_GET['id'])) {
  $question_id = $_GET['id'];
  require_once "assets/connect/pdo.php";
  $stmt = $pdo->query("SELECT * FROM question_description WHERE Question_Description_ID = $question_id");

  $infos = $stmt->fetchAll(PDO::FETCH_ASSOC);
}




//inserting answer in database
if (isset($_POST["ansSubmit"])) {

  $name = $_POST['name'];
  $student_id = $_POST['student_id'];
  $batch = $_POST['batch'];
  $section = $_POST['section'];
  $answer = $_POST['answer'];
  $question_id = $_POST['question_id'];



  //name check
  if (empty($_POST['name'])) {
    $errors['name'] = 'A name is required';
  } else {
    $name = $_POST['name'];
    if (!preg_match('/^[a-zA-Z\s]+$/', $name)) {
      $errors['name'] = 'Name must be letters and spaces only';
    }
  }

  //student id check
  if (empty($_POST['student_id'])) {
    $errors['student_id'] = 'Student ID is required.';
  } else {
    $student_id = $_POST['student_id'];
    if (!preg_match('/^[0-9]*$/', $student_id)) {
      $errors['student_id'] = 'ID must be numbers only.';
    } else if ($_POST['student_id'] != $_SESSION['Student_ID']) {
      $errors['student_id'] = 'You should put your student ID only.';
    }
  }

  //batch check
  if (empty($_POST['batch'])) {
    $errors['batch'] = 'Batch is required.';
  } else {
    $batch = $_POST['batch'];
    if (!preg_match('/^[0-9]*$/', $batch)) {
      $errors['batch'] = 'Batch must be numbers only.';
    } else if ($_POST['batch'] != $_SESSION['Batch']) {
      $errors['batch'] = 'You should put your batch only';
    }
  }

  //section check
  if (empty($_POST['section'])) {
    $errors['section'] = 'Section is required.';
  } else {
    $section = $_POST['section'];
    if (!preg_match('/^[a-zA-Z\s]+$/', $section)) {
      $errors['section'] = 'Section must be a character.';
    } else if ($_POST['section'] != $_SESSION['Section']) {
      $errors['section'] = 'You should put your section only (case-sensitive)';
    }
  }

  //answer field check
  if (empty($_POST['answer'])) {
    $errors['answer'] = 'Answer field cannot be empty.';
  } else {
    $answer = $_POST['answer'];
  }

  if (array_filter($errors)) {
    //echo 'errors in form';
  } else {

    $name = $_POST['name'];
    $student_id = $_POST['student_id'];
    $batch = $_POST['batch'];
    $section = $_POST['section'];
    $answer = $_POST['answer'];
    $question_id = $_POST['question_id'];


    try {
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $sql = "INSERT INTO student_answer (Full_Name, Student_ID, Batch, Section, Question_Description_ID, Answer) VALUES('$name', '$student_id', '$batch', '$section', '$question_id', '$answer')";
      // use exec() because no results are returned
      $pdo->exec($sql);
      $success = "<label class='alert alert-success'>Data Inserted Successfully!</label>";
    } catch (PDOException $e) {
      $err = $e->getMessage();
      $failed = "<label class='alert alert-danger'>Something went wrong. $err </label>";
    }

    $ownBatch = $_SESSION['Batch'];
    $ownSection = $_SESSION['Section'];
    $_SESSION['ExamDone'] = "Thank you for attending the Exam. Your answer script has been recieved.";
    sleep(2);
    header("Location: student_dashboard.php?batch=$ownBatch&sec=$ownSection");
  }
  // echo '<pre>';
  // var_dump($infos);
  // echo '</pre>';

}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php
  require_once 'assets/connect/head.php';
  require_once 'assets/summer_Note/summer_Note.php';
  ?>
</head>

<body>
  <header>
    <nav class="navbar navbar-expand-lg navbar-light sticky-top">
      <div class="container justify-content-start">
        <a class="navbar-brand" href="index.php"><img id="logo" src="assets/images/LuExamHiveLogo.png" height="30px">LU EXAM HIVE</a>
      </div>
    </nav>

  </header>
  <div class="text-center bg-light text-dark mb-5">
    <div class="bg-warning text-dark py-2">
      <h2 class="display-4">Answer Script</h2>
    </div>
    <!-- Exam Clock Start -->
    <div class="text-center bg-dark text-dark">
      <div class="bg-dark text-white py-2">
        <h2 class="display-4">
          <div id="ExamClock" class="clock" onload="showTime()"></div>
        </h2>
      </div>
    </div>
  </div>
  <!-- Exam Clock End -->

  <div class="container">
    <p class="pt-2 text-center"><b><span class="text-danger">Fill in the form first</span></b> before handing over to the answer script.</p>
    <?php echo $failed; ?>
    <?php echo $success; ?>




    <form action="answer_script.php?id=<?php echo $_GET['id']; ?>&title=<?php echo $_GET['title']; ?>&ct=<?php echo $_GET['ct']; ?>&cc=<?php echo $_GET['cc'];; ?>&batch=<?php echo $_GET['batch']; ?>&sec=<?php echo $_GET['sec'];; ?>" method="POST">
      <?php foreach ($infos as $info) { ?>

        <!-- Student info -->
        <div class="student-info shadow p-3 mb-5 bg-white rounded mt-3">
          <div class="row">
            <div class="col">
              <input type="text" name="name" class="form-control" placeholder="Name" value="<?php echo htmlspecialchars($name); ?>">
              <label class="text-danger"><?php echo $errors['name']; ?></label>
            </div>

            <div class="col">
              <input type="number" name="student_id" class="form-control" placeholder="Student ID" value="<?php echo htmlspecialchars($student_id); ?>">
              <label class="text-danger"><?php echo $errors['student_id']; ?></label>
            </div>
          </div>
          <div class="row mt-3">
            <div class="col">
              <input type="number" name="batch" class="form-control" placeholder="Batch" value="<?php echo htmlspecialchars($batch); ?>">
              <label class="text-danger"><?php echo $errors['batch']; ?></label>
            </div>

            <div class="col">
              <input type="text" name="section" class="form-control" placeholder="Section" value="<?php echo htmlspecialchars($section); ?>">
              <label class="text-danger"><?php echo $errors['section']; ?></label>
            </div>
          </div>
        </div>

        <input type="hidden" name="question_id" value="<?php echo $info['Question_Description_ID']; ?>">

        <!-- question info -->
        <div class="answers shadow-lg p-3 mb-5 bg-white rounded mt-3">
          <div class="row">
            <div class="col d-flex justify-content-center mt-5">
              <h4>Leading University</h4>
            </div>
          </div>

          <div class="row">
            <div class="col d-flex justify-content-center">
              <h6>Department of CSE</h6>
            </div>
          </div>

          <div class="row">
            <div class="col d-flex justify-content-center">
              <h6><?php echo $_GET['title']; ?></h6>
            </div>
          </div>

          <div class="row">
            <div class="col d-flex justify-content-center">
              <h6>Course Title: <?php echo $_GET['ct']; ?></h6>
            </div>
          </div>

          <div class="row">
            <div class="col d-flex justify-content-center">
              <h6>Course Code: <?php echo $_GET['cc']; ?></h6>
            </div>
          </div>

          <div class="row">
            <div class="col d-flex justify-content-center">
              <h6>Batch: <?php echo $_GET['batch']; ?></h6>
            </div>
          </div>

          <div class="row">
            <div class="col d-flex justify-content-center">
              <h6>Section: <?php echo $_GET['sec']; ?></h6>
            </div>
          </div>

          <!-- question -->
          <div class="row">
            <div class="col mt-5">
              <p class="form-group px-xs-0 px-sm-0 px-md-3 px-lg-5 px-xl-5 mx-xs-1 mx-sm-1 mx-md-3 mx-lg-5 mx-xl-5 mb-5">
                <label class="control-label col-sm-12 d-flex justify-content-center" for="Title"><?php echo $info['Content']; ?></label>

                <textarea id="summernote" name="answer" class="form-control" onkeypress='validate(event)' value="${cpCon.receiveNo}" autocomplete="off" required <?php echo $answer; ?>></textarea>
                <label class="text-danger"><?php echo $errors['answer']; ?></label>
                <script>
                  $('#summernote').summernote({
                    placeholder: '#Your Answers will be written here :)',
                    tabsize: 2,
                    height: 500
                  });
                </script>

              </p>
            </div>
          </div>

        <?php } ?>

        <div class="row">
          <div class="col d-flex justify-content-center mb-5">
            <input type="submit" class="form-group btn btn-dark px-4" name="ansSubmit" value="Submit">
          </div>
        </div>

    </form>

  </div>

  </div>

  <!--footer Start -->
  <footer>
    <div class="container">
      <div class="row pt-5">
        <div class="col-lg-4 col-md-6 mb-4 mb-lg-0">
          <h1 class="display-4 mr-2 mb-5">LU EXAM HIVE<a class="navbar-brand" href="index.php"><img id="logo2" src="assets/images/LuExamHiveLogo.png" height="60px"></a></h1>
        </div>

        <div class="col-lg-8 col-md-6 mb-4 mb-lg-0 mt-3">
          <blockquote class="blockquote">
            <p class="mb-0">If my future were determined just by my performance on a standardized test, I wouldn't be here. I guarantee you that.</p>
            <footer class="blockquote-footer"><cite title="Source Title">Michelle Obama</cite></footer>
          </blockquote>
        </div>

      </div>
    </div>

    <!-- Copyrights -->
    <div class="py-2" id="customFooter">
      <div class="container text-center">
        <p class="fw-bold mb-0 py-1 text-white">
          © 2021 LU EXAM HIVE All rights reserved.
        </p>
      </div>
    </div>
  </footer>
  <!--footer End(101) -->
  <!-- Custom JQuery file -->
  <script type="text/javascript" src="assets/js/custom.js"></script>
  <!--footer End -->
</body>

</html>