<?php
// sets blank options and message variables
$customers = $products = $message = "";

// imports the PHP connection file
require_once("db.php");

// queries all customer IDs and names
$stmt = $conn->query("SELECT `customer_id`, `name` FROM `lecture354b`.`customer` ORDER BY `name`");

// loops through each query row and adds each dropdown option to the customer options
foreach ($stmt as $row) { 
  $customers .= "<option value='{$row["customer_id"]}'>{$row["name"]}</option>";
}

// queries all products IDs and descriptions
$stmt = $conn->query("SELECT `product_id`, `description` FROM `lecture354b`.`product` ORDER BY `description`");

// loops through each query row and adds each dropdown option to the product options
foreach ($stmt as $row) { 
  $products .= "<option value='{$row["product_id"]}'>{$row["description"]}</option>";
}

// checks if the form has been submitted
if (isset($_POST["submit"])) {

  // prepares the query to query whether the order already exists
  $query =
  "SELECT `order_id` 
   FROM `lecture354b`.`order`
   WHERE `order_id` = ?";
  $stmt = $conn->prepare($query);

  // executes the query
  $stmt->execute([ $_POST["order-id"] ]);

  // checks if order exists and runs if the order exists
  if ($stmt->rowCount() > 0) {

    // gets the result and then the order ID from the result
    $row = $stmt->fetch();
    $orderID = $row["order_id"];

    // runs if the order doesn't exist
  } else {

    // prepares the query to insert the new order
    $stmt = $conn->prepare("INSERT INTO `lecture354b`.`order` (`customer_id`, `order_date`) 
                            VALUES (?, Now())");

    // executes the query
    $stmt->execute([ $_POST["customer-id"] ]);

    // gets the ID of the newly inserted record
    $orderID = $conn->lastInsertId();

    // adds the header and success message
    $message .= "<div class='alert alert-success'>Successfully inserted the order <b>#{$_POST["order-id"]}</b>.</div>";
  }

  // prepares the query to insert the new order line
  $stmt = $conn->prepare("INSERT INTO `lecture354b`.`order_line` (`product_id`, `order_id`, `quantity`)
                          VALUES (?, ?, ?)");

  // executes the query
  $stmt->execute([ $_POST["product-id"], $orderID, $_POST["quantity"] ]);

  // adds the header and success message
  $message .= "<div class='alert alert-success'>Successfully inserted the product <b>#{$_POST["product-id"]}</b> on order <b>#{$_POST["order-id"]}</b> for the customer <b>#{$_POST["customer-id"]}</b>.</div>";
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
      <h1>Add Product to Order</h1>
    </div>
    <div class="container">
      <form method="post" class="mb-3">
        <div class="form-group">
          <label>Customer: </label>
          <select class="form-control" name="customer-id"><?php echo $customers; ?></select>
        </div>
        <div class="form-group">
          <label>Order: </label>
          <input class="form-control" type="text" name="order-id">
        </div>
        <div class="form-group">
          <label>Product: </label>
          <select class="form-control" name="product-id"><?php echo $products; ?></select>
        </div>
        <div class="form-group">
          <label>Quantity: </label>
          <input class="form-control" type="text" name="quantity">
        </div>
        <button type="submit" class="btn btn-primary" name="submit">Submit</button>
      </form>
      <?php echo $message; ?>
    </div>
  </body>
</html>
