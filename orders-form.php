<?php
// sets a blank PHP variable
$results = "";
if (isset($_POST["submit"])) {
// imports the PHP connection file
require_once("db.php");


// queries customer orders
$query = 
"SELECT o.order_id, o.order_date, p.description, p.price, ol.quantity
 FROM `lecture354b`.`customer` c
 JOIN `lecture354b`.`order` o ON c.customer_id = o.customer_id
 JOIN `lecture354b`.`order_line` ol ON ol.order_id = o.order_id
 JOIN `lecture354b`.`product` p ON p.product_id = ol.product_id
 WHERE c.name = ?
 ORDER BY o.order_id";

// prepares the query statement against SQL injections
$stmt = $conn->prepare($query);

// executes the query
$stmt->execute([$_POST["customer"] ]);

// creates a subtotal and total PHP variable with a starting value of zero
$subtotal = 0;
$total = 0;

// loops through each row of the query
foreach ($stmt as $row) {

  // gets the subtotal for a given order line (price * quantity) 
  $subtotal = $row["price"] * $row["quantity"];

  // stores each row in the content variable while setting the money formats
  $results .= 
  "<tr>
    <td>{$row["order_id"]}</td>
    <td>{$row["order_date"]}</td>
    <td>{$row["description"]}</td>
    <td>$" . number_format($row["price"], 2) . "</td>
    <td>{$row["quantity"]}</td>
    <td>$" . number_format($subtotal, 2) . "</td>
  </tr>";

  // adds the subtotal to the total for each order line
  $total = $total + $subtotal;
}

// stores the opening table tag <table> and the header row <tr> with its respective header cells <th> in the content variable
$results = 
"<h3>Customer order information for '?':</h3>
<table class='table table-bordered table-striped'>
  <tr>
    <th>Order ID</th>
    <th>Order Date</th>
    <th>Description</th>
    <th>Price</th>
    <th>Quantity</th>
    <th>Subtotal</th>
  </tr>
  {$results}
  <tr>
    <td colspan='5'><b>Total</b></td>
    <td><b>$" . number_format($total, 2) . "</b></td>
  </tr>
</table>";

// closes the connection
$conn = null;
}
?> 

<!-- begins the HTML code -->
<!doctype html>
<html>
  <head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  </head>
  <body>
    <div class="jumbotron text-center">
      <h1>Customer Orders</h1>
    </div>
    <div class="container">
      <form method="post" class="mb-3">
        <div class="form-group">
          <label>Customer: </label>
          <input class="form-control" name="customer" placeholder="Enter customer ">
        </div>
        <button type="submit" class="btn btn-primary" name="submit">Submit</button>
      </form>
      <?php echo $results; ?>
    </div>
  </body>

</html>