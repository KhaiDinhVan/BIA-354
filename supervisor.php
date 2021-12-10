<?php // begins php code

// sets the blank PHP variables
$results = "";

// imports the PHP connection file
require_once("db.php");

// queries employees supervised by employee #3
$query = 
"SELECT *
FROM `lecture354b`.`employee`
WHERE `supervisor` = 3";

// prepares the query statement against SQL injections
$stmt = $conn->prepare($query);

// executes the query
$stmt->execute();

// loops through each row of the query
foreach ($stmt as $row) {

  // adds a new row with each cell <td> of data to the results variable
  $results .= 
  "<tr>
     <td>{$row["employee_id"]}</td>
     <td>{$row["first_name"]}</td>
     <td>{$row["last_name"]}</td>
   </tr>";
} // closes the foreach loop

// stores the opening table tag <table> and the header row <tr> with its respective header cells <th> in the results variable
$results = 
"<h5>Employees supervised by employee ID #3:</h5>
 <table class='table table-bordered table-striped'>
   <tr>
     <th>Employee ID</th>
     <th>First Name</th>
     <th>Last Name</th>
   </tr>
   {$results}
 </table>";

// closes the connection
$conn = null;

// ends php code
?> 

<!-- indicates that this page is an html document -->
<!doctype html>

<!-- starts the HTML code -->
<html>

  <!-- starts the page head -->
  <head>
    
    <!-- imports the bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

  <!-- closes the head tag -->
  </head>

  <!-- begins the body of the web page -->
  <body>
    
    <!-- adds the jumbotron (i.e. title of the page) -->
    <div class="jumbotron text-center">
      <h1>Supervisor Subordinates</h1>
    </div>
    
    <!-- adds the container div for formatting with CSS -->
    <div class="container">
      
      <!-- adds the results to be viewed in the browser -->
      <?php echo $results; ?>

    <!-- closes the opened div tag -->
    </div>

  <!-- closes the body of the web page -->
  </body>

<!-- closes the HTML -->
</html>