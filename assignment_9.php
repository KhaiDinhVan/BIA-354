<?php
// sets blank options and message variables
$invoice_id = $item = $message = "";

// imports the PHP connection file
require_once("db.php");

// queries all invoice id 
$stmt = $conn->query("SELECT `invoice_id` FROM `kvd58543`.`invoice` ORDER BY invoice_id");

// loops through each query row and adds each dropdown option to the customer options
foreach ($stmt as $row) { 
  $invoice_id .= "<option value='{$row["invoice_id"]}'> {$row["invoice_id"]}</option>";
}

// queries all item descriptions, Why select all?
$stmt = $conn->query("SELECT * FROM `kvd58543`.`item` ORDER BY `description`");

// loops through each query row and adds each dropdown option to the product options
foreach ($stmt as $row) {
  $item .= "<option value='{$row["item_id"]}'>{$row["description"]}</option>";
}

// checks if the form has been submitted
if (isset($_POST["submit"])) {

  // Store valuable
  $quantity = $_POST["quantity"];
  $item = $_POST["item-id"];
  $invoice_id = $_POST["invoice-id"];


  // prepares the query to query whether the item already exists by using invoice
  $query =
	"SELECT `invoice_id`
	FROM `kvd58543`.`invoice_item`
	WHERE `item_id` = ?;";
  $stmt = $conn->prepare($query);

  // executes the query
  $stmt->execute([$item]);


  // checks if item exists and runs if the item does exists
  if ($stmt->rowCount() > 0) {
	$query = "UPDATE `kvd58543`.`invoice_item`
		SET `quantity` = `quantity` + ?
		WHERE item_id = ?
		AND invoice_id = ?";
	$stmt = $conn->prepare($query);

	//execute the query 
	$stmt->execute([$quantity, $item, $invoice_id]);

	//Add message show quanity has been updated 
	$message .= "<h5>Update Invoice Item</h5>The Item <b> $item </b> has its quantity increase by <b> $quantity </b> where the invoice ID is <b> $invoice_id </b>.";


	// runs if the item doesn't exist
  } else {
	// prepares the query to insert the item and quantity

	$query = "INSERT INTO `kvd58543`.`invoice_item` (`item_id`, `quantity`) VALUES (?, ?";
	$stmt = $conn->prepare($query);
	// executes the query

	$stmt->execute([$item, $quantity]);
	// add message
	$message .= "<h5>Inserted item and quantity</h5>Successfylly inserted <b> $item </b> and <b> $quantity </b> as new item and quantity";

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
	  <h1>Item and Invoice</h1>
	</div>
	<div class="container">
	  <form method="post" class="mb-3">
		<div class="form-group">
		  <label>Invoice ID</label>
		  <select class="form-control" name="invoice-id"><?php echo $invoice_id; ?></select></div>
		<div class="form-group">
		  <label>Item descriptions: </label>
		  <select class="form-control" name="item-id"><?php echo $item; ?></select>
		</div>
		<div class="form-group">
		  <label>Quantity: </label>
		  <input class="form-control" type="number" name="quantity">
		</div>
		<button type="submit" class="btn btn-primary" name="submit">Submit</button>
	  </form>
	  <?php echo $message; ?>
	</div>
  </body>
</html>




