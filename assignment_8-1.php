<?php // Create a PHP page named assignment_8-1.php that when opened will display the customer name (first and last name), phone, and email for all customers.

$results = "";

require_once("db.php");

$query = 
"SELECT first_name, last_name, phone, email
FROM `kvd58543`.customer";
  
$stmt = $conn-> prepare($query);

$stmt->execute();

// have not define row
foreach ($stmt as $row) {
  
  $results .=
  "<tr>
  		<td>{$row["first_name"]}</td>
		<td>{$row["last_name"]}</td>
		<td>{$row["phone"]}</td>
		<td>{$row["email"]}</td>
  </tr>";
}

$results = 
"<h5>Customer Information </h5>
<table class='table table-bordered table-striped'>
 <tr>
 	<th>First Name</th>
	<th>Last Name</th>
	<th>Phone</th>
	<th>email</th>
	</tr>
	{$results}
</table>";
$conn = null;

?>

<!doctype html>
<html>
  <head>
	<link rel ="stylesheet"
href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
		  
  </head>
  <body>
	<div class ="jumbotron text-center">
	  <h1>
		 Customer Information
	  </h1>
	</div>
	<div class="container">
	  <?php echo $results; ?>
	</div>
  </body>
</html>
