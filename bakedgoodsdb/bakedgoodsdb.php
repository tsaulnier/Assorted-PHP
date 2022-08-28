<!doctype html>
<html>
<head></head>
<body>

<?php

$state = $_POST['state'] ?? '';
$studentId = $_POST['student_id'] ?? '';
$typeBG = $_POST['type_bg'] ?? '';
$desBG = $_POST['des_bg'] ?? '';
$quantity = $_POST['quantity'] ?? '';
$price = $_POST['price'] ?? '';
$lastId; //special variable we'll use to insert into the fact table

 ?>

<form method=POST action='./index.php'>

<h1>Add a Baked Good</h1>
<p> <em> Enter the item brought to be sold at our bake sale. </em>

<p>Student ID:
<input type='text' name='student_id' value='<?=$studentId?>'>
<!-- we'll represent the student bringing a new item in the fact table
it will appear as a "0 sold" transac on date undefined
since our structure does not allow us to declare who brought what baked good,
just who sold it. -->


<p>Type of Baked Good:
<select name='type_bg'>
<option value='choose_a_bg'>Choose A Baked Good</option>
<option value='muffin'>Muffin</option>
<option value='tart'>Tart</option>
<option value='roll'>Roll</option>
<option value='cookie'>Cookie</option></select>

<p>Qualification of Baked Good:
<input type='text' name='des_bg' value='<?=$desBG?>'>

<p>Quantity:
<input type-'text' name='quantity' value='<?=$quantity?>'>

<p>Price Per Unit:
<input type='text' name='price' value='<?=$price?>'><br><br>

<input type='submit' name='state' value='Add Baked Good'>

<?php

if ($state == "Add Baked Good") {
  if ($_POST['student_id'] == '' || $_POST['des_bg'] == '' || $_POST['quantity'] == ''
    || $_POST['price'] == '' || $_POST['type_bg'] == 'choose_a_bg') {
      echo "<p style='color:red'>There is something wrong with your input. Please try again<br></p>";
  }
  else {
    //mysqli
    $db = new mysqli('127.0.0.1', 'root', '', 'task1');
    if ($db->connect_errno > 0) {
      echo "<p style='color=red'>Error: Could not connect to bake sale database.<br></p>";
      echo "<pre>\nErrno: " . $db->errno . "\n";
      echo "Error: " . $db->error . "\n</pre><br>\n";
      exit;
    }
    else {

      $queryBG = "insert into bakedgoods (category, description, initial_quantity, unit_price) values (?,?,?,?)"; //baked good addition

      $queryStudent = "insert into bakesale (student_id, baked_goods_id, quantity_sold, day_sold)
      values (?,?,'0','N/A')"; //to keep track of which student brought which baked good

      //send query to mysql and collect result
      $stmtBG = $db->prepare($queryBG);
      if ($stmtBG) {
        $stmtBG->bind_param("ssid", $typeBG, $desBG, $quantity, $price);
        $stmtBG->execute();
      }

      if (!($stmtBG->affected_rows > 0)) {
        echo "<p style='color=red'>There was a problem processing your request
        (in particular, the new baked good).<br>Your request has not been processed.</p>";
        echo "<pre>\nErrno: ".$db->errno."\n";
        echo "Error: ".$db->error."\n</pre><br>\n";
      }

      else {
        $lastId = $db->insert_id; //gets our baked good id to use in the fact table
        $stmtSale = $db->prepare($queryStudent);
        if ($stmtSale) {
          $stmtSale->bind_param("ii", $studentId, $lastId);
          $stmtSale->execute();
        }
        if (!($stmtSale->affected_rows > 0)) {
          echo "<p style='color=red'>There was a problem processing your request
          (in particular, the student/baked good record).<br>Your request has not been processed.</p>";
          echo "<pre>\nErrno: ".$db->errno."\n";
          echo "Error: ".$db->error."\n</pre><br>\n";
        }
        else {
          echo "<p>Your addition has been recorded in the database.</p>";
        }
      }
    }
  }
}
echo "<pre>";
print_r($_POST);
echo "</pre>";
?>
</form>
</body>
</html>
