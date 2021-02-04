<?php
session_start();
require_once "assets/connect/pdo.php";
include 'assets/inc/validation_helper.php';

if (!isset($_SESSION['Teacher_ID'])) {
    die("Not logged in");
}

$status = false;

if (isset($_SESSION['status'])) {
    $status = htmlentities($_SESSION['status']);
    $status_color = htmlentities($_SESSION['color']);

    unset($_SESSION['status']);
    unset($_SESSION['color']);
}

$_SESSION['color'] = 'red';

// Check to see if we have some POST data, if we do process it
if (isset($_POST['Course_Code']) && isset($_POST['Course_Name']) && isset($_POST['Batch']) && isset($_POST['Section']) && isset($_POST['Title'])) {

    if (!validationHelper()) {
        header("Location: question_description.php");
        return;
    }

    $Course_Code = htmlentities($_POST['Course_Code']);
    $Course_Name = htmlentities($_POST['Course_Name']);
    $Batch = htmlentities($_POST['Batch']);
    $Section = htmlentities($_POST['Section']);
    $Title = htmlentities($_POST['Title']);

    $stmt = $pdo->prepare("
        INSERT INTO question_description (Teacher_ID, Course_Code, Course_Name, Batch, Section, Title)
        VALUES (:Teacher_ID, :Course_Code, :Course_Name, :Batch, :Section, :Title)
    ");

    $stmt->execute([
        ':Teacher_ID' => $_SESSION['Teacher_ID'],
        ':Course_Code' => $Course_Code,
        ':Course_Name' => $Course_Name,
        ':Batch' => $Batch,
        ':Section' => $Section,
        ':Title' => $Batch,
    ]);

    $Question_Description_ID = $pdo->lastInsertId();

    for ($i = 1; $i <= 9; $i++) {
        if (!isset($_POST['Question' . $i])) {
            continue;
        }
        $Question = htmlentities($_POST['Question' . $i]);

        $stmt = $pdo->prepare("
            INSERT INTO question (Question_Description_ID, Question)
            VALUES (:Question_Description_ID,:Question)
        ");

        $stmt->execute([
            ':Question_Description_ID' => $Question_Description_ID,
            ':Question' => $Question,
        ]);

    }

    $_SESSION['status'] = 'Record added';
    $_SESSION['color'] = 'green';

    header('Location: teacher_dashboard.php');
    return;

}

?>
<!DOCTYPE html>
<html>
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
				<a href="teacher_dashboard.php" class="text-white text-decoration-none">Back</a>
		</nav>

	</header>
    <div class="card text-center bg-light text-dark ">
            <div class="card-header bg-secondary text-white ">
                <h2 class="display-4">Create Question</h2>
            </div>
            <div class="row">
                <div class="col d-flex justify-content-center mt-3">
                    <p class="">Fill the fields below to create question !!!</p>
                </div>
            </div>
<?php
if ($status !== false) {
    // Look closely at the use of single and double quotes
    echo (
        '<p style="color: ' . $status_color . ';" class="col-sm-10 col-sm-offset-2">' .
        htmlentities($status) .
        "</p>\n"
    );
}
?>
        <div class="container col-xl-7 col-lg-7 col-md-7">
            <form method="post" class="form-horizontal">
                <div class="form-group input-group input-group-lg">
                    <label class="control-label col-sm-12 d-flex justify-content-left" for="Course_Code"><b>Course Code:</b></label>
                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <input class="form-control" type="text" name="Course_Code" id="Course_Code">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-12 d-flex justify-content-left" for="Course_Name"><b>Course Name:</b></label>
                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <input class="form-control" type="text" name="Course_Name" id="Course_Name">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-12 d-flex justify-content-left" for="Batch"><b>Batch:</b></label>
                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <input class="form-control" type="text" name="Batch" id="Batch">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-12 d-flex justify-content-left " for="Section"><b>Section:</b></label>
                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <input class="form-control" type="text" name="Section" id="Section">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-12 d-flex justify-content-left " for="Title"><b>Title:</b></label>
                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <input class="form-control" type="text" name="Title" id="Title">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-12 d-flex justify-content-left"><b>Add Question:</b></label>
                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <button id="addPos" class="btn btn-success btn-md btn-block"><i class="fas fa-pencil-alt"></i></button>
                    </div>
                </div>
                <div id="position_fields">

                </div>
                <div class="form-group d-flex justify-content-center">
                    <div class="col-sm-2 col-sm-offset-2">
                        <input class="btn btn-dark btn-block mb-5" type="submit" value="Save">

                    </div>
                </div>
            </form>
        </div>
        </div>

        <script>
            countPos = 0;

            // http://stackoverflow.com/questions/17650776/add-remove-html-inside-div-using-javascript
            $(document).ready(function(){
                window.console && console.log('Document ready called');
                $('#addPos').click(function(event){
                    // http://api.jquery.com/event.preventdefault/
                    event.preventDefault();
                    if ( countPos >= 9 ) {
                        alert("Maximum of nine position entries exceeded");
                        return;
                    }
                    countPos++;
                    window.console && console.log("Adding position "+countPos);

                    $('#position_fields').append(
                   '<div id="position'+countPos+'">\
   <div class="form-group"> \
   <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12"> \
   <button class="btn btn-danger btn-block" \
   onclick="$(\'#position'+countPos+'\').remove();return false;" \
   ><i class="fas fa-eraser"></i></button> \
   </div> \
   </div> \
   <div class="form-group"> \
   <label class="control-label col-sm-2"></label> \
   <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12"> \
   <textarea class="form-control" name="Question'+countPos+'" rows="4" ></textarea> \
   </div> \
   </div> \
   </div>'
                    );
                });
            });
        </script>
    </body>
    <?php
require_once 'assets/connect/footer.php';
?>
</html>
