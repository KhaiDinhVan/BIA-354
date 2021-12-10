<?php
// sets blank options and message variables
$invoice_id = $item = $message = "";

// imports the PHP connection file
require_once("db.php");
	
// queries all invoice id
// A dropdown menu that contains all invoice IDs.
$query = "SELECT `invoice_id` FROM `kvd58543`.`invoice` ORDER BY invoice_id";

$stmt = $conn->query($query);

  // loops through each query row and adds each dropdown option to the customer options
foreach ($stmt as $row) { 
  $invoice_id .= "<option value='{$row["invoice_id"]}'> {$row["invoice_id"]}</option>";
}

// queries all item descriptions, Why select all?
// A dropdown menu that is populated with all item descriptions.
$query = "SELECT `item_id`, `description` FROM `kvd58543`.`item` ORDER BY `description`";

$stmt = $conn->query($query);

// loops through each query row and adds each dropdown option to the product options
foreach ($stmt as $row) {
  $item .= "<option value='{$row["item_id"]}'>{$row["description"]}</option>";
}

//When the form is submitted, checks if the form has been submitted
if (isset($_POST["submit"])) {

  // Store valuable to use when execute (optional)
  $quantity = $_POST["quantity"];
  $item = $_POST["item-id"];
  $invoice_id = $_POST["invoice-id"];
  
  
  // prepares the query to query whether the item already exists by using invoice
  //  the PHP should check if the invoice already has the item on it (i.e. invoice_item) and then do one of two things:
  $query =
  	"SELECT `invoice_id`
	FROM `kvd58543`.`invoice_item`
	WHERE `item_id` = ? AND `invoice_id` = ?;";
  $stmt = $conn->prepare($query);

  // executes the query
  $stmt->execute([$item, $invoice_id]);
  

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
	$message .= "<h5>Update Invoice Item </h5> The Item <b> $item </b> has its quantity increase by <b> $quantity </b> where the invoice ID is <b> $invoice_id </b>.";
	
	
    // runs if the item doesn't exist
  } else {
    // prepares the query to insert the item and quantity
	//If the item does not exist on the invoice, the code should insert the item and quantity for the selected invoice.
	$query = "INSERT INTO `kvd58543`.`invoice_item` (`item_id`, `quantity`, `invoice_id`) VALUES (?, ?,?)";
	$stmt = $conn->prepare($query);
    // executes the query
   
	$stmt->execute([$item, $quantity, $invoice_id]);
	// add message
	$message .= "<h5>Inserted item and quantity</h5>Successfylly inserted item id <b> $item </b> and quantity <b> $quantity </b> as new item and quantity";

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

<!-- form control = make the bar span all the way of the website -->


