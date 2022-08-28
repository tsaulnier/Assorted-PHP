<!doctype html>
<html>
<head></head>
<body>

<?php

$state = $_POST['state'] ?? '';
$sku = $_POST['sku'] ?? '';
$quantity = $_POST['quantity'] ?? '';
$date = $_POST['date'] ?? '';

 ?>

<form method=POST action='./index.php'>

<h1>Sell a Baked Good</h1>
<p> <em> Enter the item being sold. </em>

<p>Item Number [SKU]:
<input type='text' name='sku' value='<?=$sku?>'>

<p>Quantity:
<input type-'text' name='quantity' value='<?=$quantity?>'>

<p>Sold on this day of the week:
<input type='text' name='date' value='<?=$date?>'><br><br>

<input type='submit' name='state' value='Calculate Sale'>

<?php

if ($state == "Calculate Sale") {
  if ($_POST['sku'] == '' || $_POST['quantity'] == '' || $_POST['date'] == '') {
      echo "<p style='color:red'>There is something wrong with your input. Please try again.<br></p>";
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

      $skuComponents = explode('-', $sku); //parse the two-part sku to get studentId and bakedGoodId
      $studentId = $skuComponents[0];
      $bakedGoodId = $skuComponents[1];

      $queryInsert = "insert into bakesale (student_id, baked_goods_id, quantity_sold, day_sold) values (?,?,?,?)"; //sale add query

      $querySelect =  "select bg.description, concat(s.first_name, ' ', s.last_name) as name,
      bg.category, bg.unit_price
      from bakesale bs
      inner join bakedgoods bg on bg.baked_goods_id = bs.baked_goods_id
      inner join students s on s.student_id = bs.student_id
      where bs.student_id = ? and bs.baked_goods_id = ?"; //to display our data

      //send query to mysql and collect result
      $stmtInsert = $db->prepare($queryInsert);
      if ($stmtInsert) {
        $stmtInsert->bind_param("iiis", $studentId, $bakedGoodId, $quantity, $date);
        $stmtInsert->execute();
      }

      if (!($stmtInsert->affected_rows > 0)) {
        echo "<p style='color=red'>There was a problem processing your request
        (in particular, the sale insert into the database).<br>Your request has not been processed.</p>";
        echo "<pre>\nErrno: ".$db->errno."\n";
        echo "Error: ".$db->error."\n</pre><br>\n";
      }

      else {
        $stmtSelect = $db->prepare($querySelect);
        if ($stmtSelect) {
          $stmtSelect->bind_param("ii", $studentId, $bakedGoodId);
          $stmtSelect->execute();
          $stmtSelect->bind_result($description, $name, $category, $price);
        }
        // if (($stmtSelect->affected_rows > 0)) {
        //   echo "<p style='color=red'>There was a problem processing your request
        //   (in particular, the request for data from the sale).<br>Your request has not been processed.</p>";
        //   echo "<pre>\nErrno: ".$db->errno."\n";
        //   echo "Error: ".$db->error."\n</pre><br>\n";
        // }
      //  else {
          while ($stmtSelect->fetch()) {
            echo "<p>Thank you for purchasing a <b>$description</b> from <b>$name</b>.
            Each <b>$category</b> costs <b>\$$price</b>.
            Your purchase of <b>$quantity</b> of them costs <b>\$".number_format($price*$quantity,2)."</b>.</p>";
            exit;
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
