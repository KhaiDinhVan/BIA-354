<?php
// sets blank options and message variables
$content = $net_id = $receptionist_id = $message = "";

// imports the PHP connection file
require_once("db.php");
	
// A list of all students sorted by last name. This drop-down should display the student's full name
$query = "SELECT net_id, last_name, first_name FROM `kvd58543`.student ORDER BY last_name";

$stmt = $conn->query($query);

  // (last name should be displayed first with a comma between the last name and first name) but pass the net ID when the form in submitted.
foreach ($stmt as $row) { 
  $net_id .= "<option value='{$row["net_id"]}'> {$row["last_name"]}, {$row["first_name"]}</option>";
}

// a list of all receptionists sorted by last name.
$query = "SELECT receptionist_id, last_name, first_name FROM `kvd58543`.receptionist ORDER BY last_name";

$stmt = $conn->query($query);

// (last name should be displayed first with a comma between the last name and first name) but pass the receptionist ID when the form in submitted.
foreach ($stmt as $row) {
  $receptionist_id .= "<option value='{$row["receptionist_id"]}'>{$row["last_name"]}, {$row["first_name"]}</option>";
}

//1. When the form is submitted, checks if the form has been submitted
if (isset($_POST["submit"])) {

  // Store valuable to use when execute (optional)
  $net_id = $_POST["net-id"];
  $receptionist_id = $_POST["receptionist-id"];
  $rental= $_POST["rental-name"];
  $replace_cost = $_POST["replace-cost"];
  $fees = $_POST["fees"];


  // prepares the query to check if the equipment name already exists.
  $query =
	"SELECT equipment_id, name FROM `kvd58543`.equipment WHERE name = ?";

  $stmt = $conn->prepare($query);

  // executes the query
  $stmt->execute([$rental]);


  // 2. Check if the equipment name already exists.
  if ($stmt->rowCount() > 0) {
	
  // 3. If the equipment exists, grab the ID of the equipment and store in a PHP variable.
	$row = $stmt->fetch();
    $equipmentID = $row["equipment_id"];
 
	// 3. If the equipment doesn't exist, insert the new equipment with the name and replacement cost from the form 
  } else {
	$query = "INSERT INTO `kvd58543`.equipment (name, replacement_cost) VALUES (?, ?)";

	$stmt = $conn->prepare($query);

	$stmt->execute([$rental, $replace_cost]);

	//store the newly inserted equipment ID in a PHP variable.
	$equipmentID =  $conn -> lastInsertId();

	//Add message show new equipment has been inserted 
	$message .= "<h5>Insert new equipment </h5> <b> $rental </b> has its replacement cost of<b> $replace_cost </b>, where the equipment ID is <b> $equipmentID </b>.";


	}
  	// 4. Using the equipment ID from the previous step, the current date and time for check out, and the student ID, receptionist ID, and fees from the form, insert a new rental.
	$query = "INSERT INTO `kvd58543`.rental (check_out, net_id, receptionist_id, fees, equipment_id) VALUES (Now(), ? , ?, ?, ?)";

	$stmt = $conn->prepare($query);

	$stmt->execute([$net_id, $receptionist_id, $fees, $equipmentID]);

	//Add message show new rental has been inserted 
	$message .= "<h5>Insert new rental</h5> with student ID <b> $net_id </b> with receptionist ID <b> $receptionist_id </b> with fees of <b> $fees </b>.";
	
	// 5. display in table format the equipment name, total number of times the equipment item has been checked out (use the alias total_check_outs), and total amount of fees (use the alias total_fees) for the student submitted in the form. 
	
	$query = 
	"SELECT name, COUNT(check_out) AS total_check_outs, SUM(fees) AS total_fees 
	FROM `kvd58543`.rental
	INNER JOIN `kvd58543`.equipment USING (equipment_id)
	INNER JOIN `kvd58543`.student USING (net_id)
	WHERE net_id = ?
	GROUP BY equipment_id";
	
	$stmt = $conn->prepare($query);
	
	$stmt->execute([$net_id]);
	
	foreach ($stmt as $row) {
	  $content .=
	  "<tr>
  		<td>{$row["name"]}</td>
		<td>{$row["total_check_outs"]}</td>
		<td>{$row["total_fees"]}</td>
  	  </tr>";
	}

	// Build table
	$content = 
	" 
		<h5>Rental Information for Student ID $net_id </h5>
		<table class='table table-bordered table-striped'>
		 <tr>
 			<th>Equipment Name</th>
			<th>Total Check Out</th>
			<th>Total Fees</th>
		</tr>
	    {$content}
	 </table>";
	
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
      <h1>Fitness and Wellness Log</h1>
    </div>
    <div class="container">
      <form method="post" class="mb-3"> 
        <div class="form-group"> 
          <label>Student Name:</label>
          <select class="form-control" name="net-id"><?php echo $net_id; ?></select></div>
        <div class="form-group">
          <label>Receptionist Name: </label>
          <select class="form-control" name="receptionist-id"><?php echo $receptionist_id; ?></select></div>
		<div class="form-group">
          <label>Equipment Rental: </label>
          <input class="form-control" type="text" name="rental-name">
        </div>
        <div class="form-group">
          <label>Replacement Cost: </label>
          <input class="form-control" type="number" step = ".01" name="replace-cost">
        </div>
		 <div class="form-group">
          <label>Fees: </label>
          <input class="form-control" type="number" step = ".01" name="fees">
        </div>
        <button type="submit" class="btn btn-primary" name="submit">Submit</button>
	  </form>
	  <div>
		<?php echo $message; ?>
	  </div>
	  <?php echo $content; ?>
    </div>
  </body>
</html>

