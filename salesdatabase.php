<?php

#start PHP Session
session_start();

$state = $_POST['state'] ?? 'Back to Login';

if ($_POST['state'] == 'Back to Login' && isset($_SESSION['role'])) {
  session_destroy();
  $_SESSION = array();
}

?>
<!doctype html>
<html>
<style>
table, th, td {
   border: 1px solid black;
   border-collapse: collapse;
   text-align: center;
}
th {
  background-color: yellow;
}
</style>
<head></head>
<body>
<form method=POST action='./index.php'>

<h1>DJ Depot</h1>
<h2>The shop for all the newest electronic music</h2>
<?php

if ($state == 'Back to Login') {

$suUsername = $_POST['su_username'] ?? '';
$suFN = $_POST['su_fn'] ?? '';
$suLN = $_POST['su_ln'] ?? '';
$suEmail = $_POST['su_email'] ?? '';

echo "<p> <em>Please enter your login credentials.</em> </p>

<p>Username:
<input type='username' name='username'>
</p>

<p>Password:
<input type='password' name='password'>
</p>

<p>
  <input type='Submit' name='state' value='Login'>
</p>

<p>Don't have an account with us? Sign up below. It's free and you get access to our web store for in-store reservations!</p>

Username:
<input type='username' name='su_username' value=".$suUsername."><br>
Password:
<input type='password' name='su_password1'><br>
Verify Password:
<input type='password' name='su_password2'><br>
First Name:
<input type='username' name='su_fn' value=".$suFN."><br>
Last Name:
<input type='username' name='su_ln' value=".$suLN."><br>
Email Address:
<input type='email' name='su_email' value=".$suEmail."><br>

<p><input type='submit' name='state' value='Create Account'

";

}

if ($state == 'Login') {
  if (isset($_POST['username']) && isset($_POST['password'])) {
    if (!$_POST['username'] == '' || !$_POST['password'] == '') {
      $username = $_POST['username'];
      $hashedPassword = hash('sha224',$_POST['password']); // we convert to the hashed password using the sha2 function instead of md5
      $db = new mysqli('127.0.0.1', 'root', '', 'capstone');
      if ($db->connect_errno > 0) {
        echo "<p style='color=red'>Error: Could not connect to bake sale database to verify your login credentials.<br></p>";
        echo "<pre>\nErrno: " . $db->errno . "\n";
        echo "Error: " . $db->error . "\n</pre><br>\n";
        exit;
      }
      else {
        $loginQuery = "select u.username, concat(u.first_name,' ', u.last_name) as name, r.role_name, u.user_id
        from delegate d
        inner join users u on u.user_id=d.user_id
        inner join roles r on r.role_id=d.role_id
        where u.username = ? and u.hashpw = ?";

        $stmtLogin = $db->prepare($loginQuery);
        if ($stmtLogin) {
          $stmtLogin->bind_param("ss", $username, $hashedPassword);
          $stmtLogin->execute();
          $result = $stmtLogin->get_result();

          if ($stmtLogin->affected_rows > 0) {
            #a row was returned
            $row = $result->fetch_row();
            $role = $row[2];
            $name = $row[1];
            $_SESSION['full_name'] = $name;
            $_SESSION['user'] = $username;
            $_SESSION['role'] = $role;
            $_SESSION['user_id'] = $row[3];
          }
        }
      }
      $db->close();
    }
  }
  if (isset($_SESSION['role'])) {
    echo "<p style='color:blue;'>Welcome ".$_SESSION['full_name'].". Continue to Main Menu.</p>";
  }
  else {
    echo "<p style='color:red;'>Your credentials were invalid as entered. Please try again or contact the system administrator for assistance.</p>";
  }
  "<input type='hidden' name='su_username' value='".$_POST['su_username']."'>";
  "<input type='hidden' name='su_fn' value='".$_POST['su_fn']."'>";
  "<input type='hidden' name='su_ln' value='".$_POST['su_ln']."'>";
  "<input type='hidden' name='su_email' value='".$_POST['su_email']."'>";
}

if ($state == "Create Account") {
  if (!$_POST['su_username']==''||!$_POST['su_password1']==''||!$_POST['su_password2']==''||
  !$_POST['su_fn']==''||!$_POST['su_ln']==''||!$_POST['su_email']=='') {
    if ($_POST['su_password1'] == $_POST['su_password2']) {
      $suUsername = $_POST['su_username'];
      $hashedsuPassword = hash('sha224',$_POST['su_password']); // we convert to the hashed password using the sha2 function instead of md5
      $suFirstName = $_POST['su_fn'];
      $suLastName = $_POST['su_ln'];
      $suEmail = $_POST['su_email'];
      $db = new mysqli('127.0.0.1', 'root', '', 'capstone');
      if ($db->connect_errno > 0) {
        echo "<p style='color=red'>Error: Could not connect to store database to complete your account registration.<br></p>";
        echo "<pre>\nErrno: " . $db->errno . "\n";
        echo "Error: " . $db->error . "\n</pre><br>\n";
        exit;
      }
      else {
        $queryNewUser = "insert into users (username, first_name, last_name, email, hashpw) values (?,?,?,?,?)";

        $stmtLogin = $db->prepare($queryNewUser);
        if ($stmtLogin) {
          $stmtLogin->bind_param("sssss", $suUsername, $suFirstName, $suLastName, $suEmail, $hashedsuPassword);
          $stmtLogin->execute();
          $result = $stmtLogin->get_result();

          if ($stmtLogin->affected_rows > 0) {
            #a row was returned
            $_SESSION['full_name'] = $suFirstName." ".$suLastName;
            $_SESSION['user'] = $suUsername;
            $_SESSION['role'] = 2;

            $lastId = $db->insert_id; //gets the id just assigned to the user
            $_SESSION['user_id'] = $lastId;
            $queryNewDelegation = "insert into delegate values (".$lastId.",2)"; //adds the role of customer to the new user

            $resultDelegate = $db->query($queryNewDelegation);
            if (!$resultDelegate) {
              echo "There was a problem assigning your new account to the database. Please contact the system administrator for more assistance.";
            }
            else {
              echo "Thanks for creating an account, ".$suFirstName."! Click the button below to continue to the main menu.";
            }
          }
          else {
            echo "<p style='color:red'>Your connection to the database was successful, but there was a problem signing you up. Please contact the system administrator for more information.";
          }
        }
      }
      $db->close();
    }
    else {
      echo "<p style='color:red'>Your password verification did not match your first entered password. Please try again.";
    }
  }
  else {
    echo "<p style='color:red'>There was information missing from your input. Please click the button below to return to the main login screen and try again.";
  }
}

if ($state == "Main Menu") {
  echo "<p style='color:blue'>You are logged in as ".$_SESSION['full_name'].".";
  echo "<p><em>Please select from the following options.</em></p>";
  echo "<p><input type='Submit' name='state' value='Reserve Music For Store Pickup'></p>";
  echo "<p><input type='Submit' name='state' value='Add New Inventory'>(ADMIN OR MANAGER ONLY)</p>";
  echo "<p><input type='Submit' name='state' value='View Sales Reports'>(ADMIN ONLY)</p>";
}


////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////
//////////////////////////////////////////////////
///////////////////////////////////////
//////////////////////////////
//////////////////////
///////////////


if ($state == "Reserve Music For Store Pickup") {

  $stateReserve = $_POST['reserve_chosen'] ?? '';
  $desiredProductID = $_POST['items_available'] ?? '';
  $desiredProduct = $_POST[$desiredProductID];
  $confirmedOrder = $_POST['confirmed'] ?? '';
  $declinedOrder = $_POST['decline_reservation'] ?? '';


  if ($stateReserve == 'Reserve This Item For Me' && $declinedOrder == '') {
    if ($confirmedOrder == 'Yes, Reserve It For Me') {

      $db = new mysqli('127.0.0.1', 'root', '', 'capstone');
      if ($db->connect_errno > 0) {
        echo "<p style='color=red'>Error: Could not connect to store database to reserve your item.<br></p>";
        echo "<pre>\nErrno: " . $db->errno . "\n";
        echo "Error: " . $db->error . "\n</pre><br>\n";
        exit;
      }
      else {

        $queryReserve = "insert into sales (product_id, user_id) values (?,?)";

        $stmtReserve = $db->prepare($queryReserve);
        if ($stmtReserve) {
          $stmtReserve->bind_param("ii", $desiredProductID, $_SESSION['user_id']);
          $stmtReserve->execute();
          $result = $stmtReserve->get_result();

          if ($stmtReserve->affected_rows > 0) {
            echo "Your request was received! We will hold your order until you arrive, or for 21 days.";

            $queryRemoveSingleItem = "update inventory set quantity = quantity - 1 where quantity > 0 and product_id = ".$desiredProductID;
            $db->query($queryRemoveSingleItem);
            $db->close();
          }
          else {
            echo "<p style='color=red'>There was a problem processing your request.<br></p>";
            echo "<pre>\nErrno: " . $db->errno . "\n";
            echo "Error: " . $db->error . "\n</pre><br>\n";
          }
        }
        echo "<input type='hidden' name='items_available' value=".$desiredProductID.">";
        echo "<input type='hidden' name='state' value='Reserve Music For Store Pickup'>";
        echo "<input type='hidden' name='reserve_chosen' value='Reserve This Item For Me'>";
        echo "<input type='hidden' name='confirmed' value='Yes, Reserve It For Me'>";
      }
      echo "<input type='hidden' name='items_available' value=".$desiredProductID.">";
      echo "<input type='hidden' name='state' value='Reserve Music For Store Pickup'>";
      echo "<input type='hidden' name='reserve_chosen' value='Reserve This Item For Me'>";
      echo "<input type='hidden' name='confirmed' value='Yes, Reserve It For Me'>";
    }
    else {
      echo "Are you sure you want to reserve a copy of <b>".$desiredProduct."?</b><br><br>";
      echo "<input type='hidden' name='items_available' value=".$desiredProductID.">";
      echo "<input type='submit' name='confirmed' value='Yes, Reserve It For Me'><br>";
      echo "<input type='submit' name='decline_reservation' value='No, Go Back'><br>";
      echo "<input type='hidden' name='reserve_chosen' value='Reserve This Item For Me'><br>";
    }
    echo "<input type='hidden' name='state' value='Reserve Music For Store Pickup'>";
    echo "<input type='hidden' name='items_available' value=".$desiredProductID.">";
    echo "<input type='hidden' name='reserve_chosen' value='Reserve This Item For Me'><br>";
  }
  else {
    echo "<h2>Reserve Music For Store Pickup</h2>
    <p> <em> Please select among the available options to reserve inventory for your next visit. </em><br>";

      $queryAvailable = "select p.product_id, p.artist_name, p.album_name, p.media_format, p.release_date, i.quantity
      from inventory i
      inner join products p on p.product_id = i.product_id
      where quantity > 0";

      $db = new mysqli('127.0.0.1', 'root', '', 'capstone');
      if ($db->connect_errno > 0) {
        echo "<p style='color=red'>Error: Could not connect to store database to get available items.<br></p>";
        echo "<pre>\nErrno: " . $db->errno . "\n";
        echo "Error: " . $db->error . "\n</pre><br>\n";
        exit;
      }
      else {
        $resultsAvailable = $db->query($queryAvailable);

        if ($resultsAvailable == 0) {
          echo "There are no items in stock. Please visit us later!.";
        }
        else {
          echo "<select name='items_available'>";
          echo "<option value='default'>Select a Product</option>";
          $cycleCount = 0;
          while ($row = mysqli_fetch_assoc($resultsAvailable)) {
            echo "<option value=".$row['product_id'].">".$row['artist_name']." – ".$row['album_name']." [".$row['media_format'].", released on ".$row['release_date']."] "."/// Quantity Remaining: ".$row['quantity']."</option>";
            $itemToSave = $row['artist_name']."–".$row['album_name'];
            $echoHidden[$cycleCount] = "<input type='hidden' name=".$row['product_id']." value=".$itemToSave.">";
            $cycleCount++;
          }
          for ($i = 0; $i < $cycleCount; $i++) {
            echo $echoHidden[$i];
          }
          echo "</select><br>";
          echo "<input type='submit' name='reserve_chosen' value='Reserve This Item For Me'><br>";
        }
      }
      echo "<input type='hidden' name='state' value='View Sales Reports'>";
  }
  echo "<input type='hidden' name='state' value='Reserve Music For Store Pickup'>";
}

/////////////
/////////////////////////
///////////////////////////////////
//////////////////////////////////////////////
////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($state == "Add New Inventory" || $state == "Change Entry") {

  $stateAdd = $_POST['state_add'] ?? '';
  $stateAddSubmit = $_POST['state_add_submit'] ?? '';

  $artist = $_POST['artist_add'] ?? '';
  $album = $_POST['album_add'] ?? '';
  $format = $_POST['format_add'] ?? '';
  $date = $_POST['date_add'] ?? '';

  if ($state == "Change Entry") {
    $stateAdd = '';
  }

  if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'manager') {

    if ($stateAdd == "Add New Release") {
      if ($stateAddSubmit == "Add To Database") {

        $db = new mysqli('127.0.0.1', 'root', '', 'capstone');
        if ($db->connect_errno > 0) {
          echo "<p style='color=red'>Error: Could not connect to store database to complete the product registration.<br></p>";
          echo "<pre>\nErrno: " . $db->errno . "\n";
          echo "Error: " . $db->error . "\n</pre><br>\n";
          exit;
        }
        else {
          $queryAddNewRelease = "insert into products (artist_name, album_name, media_format, release_date) values (?,?,?,?)";

          $stmtAddNewRelease = $db->prepare($queryAddNewRelease);
          if ($stmtAddNewRelease) {
            $stmtAddNewRelease->bind_param("ssss", $artist, $album, $format, $date);
            $stmtAddNewRelease->execute();
            $result = $stmtAddNewRelease->get_result();

            if ($stmtAddNewRelease->affected_rows > 0) {
              echo "Your addition has been recorded.";
              $lastId = $db->insert_id; //gets the id just assigned to the product
              $queryInventoryInitialize = "insert into inventory values ($lastId,0)"; //adds the role of customer to the new user

              $resultInventoryInitialize = $db->query($queryInventoryInitialize);
            }
            else {
              echo "<p style='color=red'>There was a problem processing your request.<br></p>";
              echo "<pre>\nErrno: " . $db->errno . "\n";
              echo "Error: " . $db->error . "\n</pre><br>\n";
            }
          }
        }
        echo "<input type='hidden' name='state' value='Add New Inventory'>";
        echo "<input type='hidden' name='state_add' value='Add New Release'>";
        echo "<input type='hidden' name='state_add_submit' value='Add To Database'>";

        echo "<input type='hidden' name='artist_add' value='" . $_POST['artist_name'] . "'>";
        echo "<input type='hidden' name='album_add' value='" . $album . "'>";
        echo "<input type='hidden' name='format_add' value=" . $format . ">";
        echo "<input type='hidden' name='date_add' value=" . $date . ">";



      }
      else {
        echo "You wish to add the following release to the product database:<br><br>";
        echo"<b>Artist:</b> ".$_POST['artist_name']."<br>
        <b>Album:</b> ".$_POST['album_name']."<br>
        <b>Media Format:</b> ".$_POST['media_format']."<br>
        <b>Release Date:</b> ".$_POST['release_date']."<br><br>
        Is this correct?<br><br>";

        echo "<input type='submit' name='state_add_submit' value='Add To Database'><br><br>";
        echo "<input type='submit' name='state' value='Change Entry'><br><br>";
        echo "<input type='hidden' name='state' value='Add New Inventory'>";
        echo "<input type='hidden' name='state_add' value='Add New Release'>";
      }
      echo "<input type='hidden' name='artist_add' value='" . $_POST['artist_name'] . "'>";
      echo "<input type='hidden' name='album_add' value='" . $_POST['album_name'] . "'>";
      echo "<input type='hidden' name='format_add' value=" . $_POST['media_format'] . ">";
      echo "<input type='hidden' name='date_add' value=" . $_POST['release_date'] . ">";
    }

    else {
      echo "Fill in this form to add any new releases to the database:<br><br>";
      echo "Artist: <input type='text' name='artist_name'><br>";
      echo "Album: <input type='text' name='album_name'><br>";
      echo "Media Format: <select name='media_format'>
            <option value='default'>Choose A Format</option>
            <option value='vinyl'>Vinyl</option>
            <option value='cd'>CD</option>
            <option value='cassette'>Cassette</option></select><br>";
      echo "Release Date: <input type='date' name='release_date'><br>";
      echo "<input type='submit' name='state_add' value='Add New Release'><br><br>";

      //here's where we query the database for available releases to update the stock only

      echo "Select an item below (new items are low-stock items) to update its quantity:<br>";

      $quantityUpdate = $_POST['quantity_update'] ?? '';

      $queryAvailable = "select p.product_id, p.artist_name, p.album_name, p.media_format, p.release_date, i.quantity
      from inventory i
      inner join products p on p.product_id = i.product_id
      where quantity < 5";

      $db = new mysqli('127.0.0.1', 'root', '', 'capstone');
      if ($db->connect_errno > 0) {
        echo "<p style='color=red'>Error: Could not connect to store database to get available items.<br></p>";
        echo "<pre>\nErrno: " . $db->errno . "\n";
        echo "Error: " . $db->error . "\n</pre><br>\n";
        exit;
      }
      else {
        $resultsAvailable = $db->query($queryAvailable);

        if ($resultsAvailable == 0) {
          echo "There are no items in the inventory with fewer than 5 copies.";
        }
        else {
          echo "<select name='items_available'>";
          echo "<option value='default'>Select a Product</option>";
          while ($row = mysqli_fetch_array($resultsAvailable)) {
            echo "<option value=".$row['product_id'].">".$row['artist_name']." – ".$row['album_name']." [".$row['media_format'].", released on ".$row['release_date']."] "."/// Quantity Remaining: ".$row['quantity']."</option>";
          }
          echo "</select><br>";
        }
        echo "Quantity to add: <input type='number', name='quantity_to_add'><br>
        <input type='submit' name='quantity_update' value='Add Quantity To Inventory'>";
      }

      $quantityToAdd = $_POST['quantity_to_add'] ?? '';

      if ($quantityUpdate != '') {
        if (is_numeric($quantityToAdd) && $quantityToAdd > 0) {
          $queryQuantity = "update inventory set quantity = quantity + ? where product_id = ".$_POST['items_available'];
          $db = new mysqli('127.0.0.1', 'root', '', 'capstone');
          if ($db->connect_errno > 0) {
            echo "<p style='color=red'>Error: Could not connect to store database to get available items.<br></p>";
            echo "<pre>\nErrno: " . $db->errno . "\n";
            echo "Error: " . $db->error . "\n</pre><br>\n";
            exit;
          }
          else {
            $stmtQuantity = $db->prepare($queryQuantity);
            if ($stmtQuantity) {
              $stmtQuantity->bind_param("i", $quantityToAdd);
              $stmtQuantity->execute();
              $resultsQuantity = $stmtQuantity->get_result();

              if (!$resultsQuantity->affected_rows == 0) {
                echo "There are no items in the inventory with fewer than 5 copies.<br>";
              }
              else {
                echo "The inventory has been updated with the specified quantity.";
              }
            }
          }
        }
        else {
          echo "You did not enter a numeric value greater than 0 for the quantity.";
        }
      }
    }

  }
  else {
    echo "<p style='color:red'>You do not have permission to access this option. Please contact your system administrator.</p><br>";
  }
  echo "<input type='hidden' name='state' value='Add New Inventory'>";
}

//of course redo this display of sales reports
if ($state == "View Sales Reports") {
  if ($_SESSION['role'] == 'admin') {
    $report = $_POST['report'] ?? 'Generate Another Report';
    if ($report == 'Generate Another Report') {
      echo "<h2>View DJ Depot Reports</h2>";
      echo "<p>Select which report to generate among the following options:</p>";
      echo "<p><input type='Submit' name='report' value='Reserved Items By Customer'></p>";
      echo "<p><input type='Submit' name='report' value='Inventory By Artist'></p>";
      echo "<p><input type='Submit' name='report' value='Inventory By Format'></p>";
    }
    if ($report != '') {
      $queryReport;
      if ($report == 'Reserved Items By Customer') {
        $queryReport = "select concat(u.first_name, ' ',u.last_name) as customer, concat(p.artist_name,' – ',p.album_name) as reserved_item
        from sales s
        inner join users u on u.user_id=s.user_id
        inner join products p on p.product_id=s.product_id
        order by customer desc";
      }
      elseif ($report == 'Inventory By Artist') {
        $queryReport = "select p.artist_name as artist, i.quantity
        from inventory i
        inner join products p on p.product_id=i.product_id
        group by artist
        order by quantity desc";
      }
      elseif ($report == 'Inventory By Format') {
        $queryReport = "select p.media_format as format, i.quantity
        from inventory i
        inner join products p on p.product_id=i.product_id
        group by format
        order by quantity desc";
      }
      if (!empty($_POST['report']) && $_POST['report'] != "Generate Another Report") {
        $db = new mysqli('127.0.0.1', 'root', '', 'capstone');
        if ($db->connect_errno > 0) {
          echo "<p style='color=red'>Error: Could not connect to bake sale database.<br></p>";
          echo "<pre>\nErrno: " . $db->errno . "\n";
          echo "Error: " . $db->error . "\n</pre><br>\n";
          exit;
        }
        else {
          $result = $db->query($queryReport);

          if ($result->num_rows > 0) {
            switch ($report) {
              case 'Reserved Items By Customer' : echo "<table><th>Customer</th><th>Reserved Item</th>"; break;
              case 'Inventory By Artist' : echo "<table><th>Artist</th><th>Quantity In Stock</th>"; break;
              case 'Inventory By Format' : echo "<table><th>Format</th><th>Quantity In Stock</th>";
                break;
            }
            // output data of each row
            while($row = $result->fetch_assoc()) {
              switch ($report) {
                case 'Reserved Items By Customer' : echo "<tr><td>".$row['customer']."</td><td>".$row['reserved_item']."</td></tr>"; break;
                case 'Inventory By Artist' : echo "<tr><td>".$row['artist']."</td><td>".$row['quantity']."</td></tr>"; break;
                case 'Inventory By Format' : echo "<tr><td>".$row['format']."</td><td>".$row['quantity']."</td></tr>"; break;
              }
            }
            echo "</table>";
          }
          else {
            echo "There are no results to be shown.<br>";
          }
          $db->close();
          echo "<input type='submit' name='report' value='Generate Another Report'>";
        }
      }
    }
    echo "<input type='hidden' name='state' value='View Sales Reports'>";
  }
  else {
    echo "<p style='color:red'>You do not have permission to access this option. Please contact your system administrator.</p><br>";
  }
}

echo "<pre>";
print_r($_POST);
echo "<br>";
print_r($_SESSION);
echo "</pre><br><br>";

if (isset($_SESSION['role'])) {
  echo "<input type='submit' name='state' value='Main Menu'>";
}

?>

<p><input type='Submit' name='state' value='Back to Login'></p>

</form>
</body>
</html>
