<?php // Create a PHP page named assignment_8-2.php where a user enters a quantity and submits the form. After the user submits the form, display on the same page the customer name (first and last name), the invoice ID, the quantity, and the item description for all dry cleaning where the quantity from the database exceeds the quantity entered into the form.

$results = "";

if (isset($_POST["submit"])) {
  require_once("db.php");

  $quantity = $_POST["quantity"];
  $query = 
  "SELECT first_name, last_name, invoice_id, quantity, description
  FROM `kvd58543`.invoice
  INNER JOIN `kvd58543`.customer USING (customer_id)
  INNER JOIN `kvd58543`.invoice_item USING (invoice_id)
  INNER JOIN `kvd58543`.item USING (item_id)
  WHERE quantity > ?";

  $stmt = $conn-> prepare($query);

  $stmt->execute([$quantity]);


  foreach ($stmt as $row) {

	$results .=
	"<tr>
		  <td>{$row["first_name"]}</td>
		  <td>{$row["last_name"]}</td>
		  <td>{$row["invoice_id"]}</td>
		  <td>{$row["quantity"]}</td>
		  <td>{$row["description"]}</td>
	</tr>";
  }

  $results = 
  "<h5>List the information for quantity more than $quantity </h5>
  <table class='table table-bordered table-striped'>
   <tr>
	  <th>First Name</th>
	  <th>Last Name</th>
	  <th>Invoice ID</th>
	  <th>quantity</th>
	  <th>description</th>
	  </tr>
	  {$results}
  </table>";
  $conn = null;
}
?>

<!doctype html>
<html>
  <head>
	<link rel ="stylesheet"
href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
		  
  </head>
  <body>
	<div class ="jumbotron text-center">
	</div>
	<div class="container">
	  <h3>
		Enter the quantity 
	  </h3>
	  <form method="post">
	  	<input type="number" placeholder="Enter the quantity" name="quantity">
	  	<input type="submit" name="submit"> 
		// or use button
		<button class='btn btn-primary' name='submit'>Submit</button>
	  </form>
	   <div>
		  <?php echo $results; ?>
	  </div>
	</div>
  </body>
</html>
