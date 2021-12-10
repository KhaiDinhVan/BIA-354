<?php
// sets a PHP variables
$message = "";

// imports the PHP connection file
require_once("db.php");

// queries all employees
$query =
"SELECT *
FROM lecture354b.product_line
WHERE name = ?";

// prepares the query statement against SQL injections
$stmt = $conn->prepare($query);

// executes the query

$stmt->execute();

// loops through each row of the query
foreach ($stmt as $row) { 

  // adds a new option for the drop-down into the employees variable
  $employees .= "<option value='{$row["employee_id"]}'>{$row["first_name"]} {$row["last_name"]}</option>";
}

// checks if the form has been submitted
if (isset($_POST["submit"])) {

  // creates the query to check if the employee already exists
  $query =
  "SELECT * 
  FROM lecture354b.employee 
  WHERE first_name = ? 
  AND last_name = ? 
  AND birth_date = ?";

  // prepares the query statement against SQL injections
  $stmt = $conn->prepare($query);

  // executes the query with the necessary placeholders
  $stmt->execute([ $_POST["fname"], $_POST["lname"], $_POST["dob"] ]);

  // checks if no rows were returned from the query
  if ($stmt->rowCount() == 0) {

    // creates the query to insert the new employee
    $query =
    "INSERT INTO lecture354a.employee (first_name, last_name, street, city, state, zipcode, birth_date, date_hired, supervisor) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    // prepares the query statement against SQL injections
    $stmt = $conn->prepare($query);

    // executes the query with the necessary placeholders
    $stmt->execute([ $_POST["fname"], $_POST["lname"], $_POST["street"], $_POST["city"], $_POST["state"], $_POST["zipcode"], $_POST["dob"], $_POST["hired"], $_POST["supervisor"] ]);

    // displays a message that the employee was successfully inserted
    $message = "<div class='alert alert-success'>Successfully added <b>{$_POST["fname"]} {$_POST["lname"]}</b> as an employee.</div>";

    // runs if any rows were returned in the query
  } else {

    // displays a message that the employee already exists
    $message = "<div class='alert alert-danger'>The employee <b>{$_POST["fname"]} {$_POST["lname"]}</b> already exists.</div>";
  }
}

// closes the connection
$conn = null;
?>

<!-- begins the HTML code -->
<!doctype html>
<html>
  <head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  </head>
  <body>
    <div class="jumbotron text-center">
      <h1>Insert a New Employee</h1>
    </div>
    <div class="container">
      <form method="post" class="mb-3">
        <div class="form-group">
          <label>First Name:</label>
          <input class="form-control" type="text" name="fname">
        </div>
        <div class="form-group">
          <label>Last Name:</label>
          <input  class="form-control" type="text" name="lname">
        </div>
        <div class="form-group">
          <label>Street:</label>
          <input  class="form-control" type="text" name="street">
        </div>
        <div class="form-group">
          <label>City:</label>
          <input  class="form-control" type="text" name="city">
        </div>
        <div class="form-group">
          <label>State:</label>
          <input  class="form-control" type="text" name="state">
        </div>
        <div class="form-group">
          <label>Zipcode:</label>
          <input  class="form-control" type="text" name="zipcode">
        </div>
        <div class="form-group">
          <label>Birth Date: </label>
          <input  class="form-control" type="date" name="dob">
        </div>
        <div class="form-group">
          <label>Hire Date: </label>
          <input  class="form-control" type="date" name="hired">
        </div>
        <div class="form-group">
          <label>Supervisor: </label>
          <select class="form-control" name="supervisor"><?php echo $employees; ?></select>
        </div>        
        <button type="submit" class="btn btn-primary" name="submit">Submit</button>
      </form>
      <?php echo $message; ?>
    </div>
  </body>
</html>