<!doctype html>
<html>
<head></head>
<style>
table, th, td {
   border: 1px solid black;
   border-collapse: collapse;
}
</style>
<body>
<h1> Project 1 - DOND (deal or no deal) template</h1>

<form method=POST action="/Library/WebServer/Documents/index.php">

<?php
# global vars and lib
global $cash, $caseCase, $caseState, $state;
require("/Library/WebServer/Documents/library.php");

# how many other cases the Player gets to open in state 1
$numOpen = 2; ## 6  xxxxxxxx

# what is the current game state?  I am using numbers for this game
$state = $_POST['state'] ?? 0;  # default is 0
if (isset($_POST['reset']) && $_POST['reset']=="Start Over") {
    $state=0;
}
gameState();
print "<em>dbug: current state = $state (at beginning of this round)</em><br><br>\n\n";

# ======================================================
# Play the Game!
# =====================================================
#  here is a big if,elseif,else statement for game state
# state  description
#   0 = beginning (start or restart)
#   1 = user picked the winning breifcase
#   2 = player picks 6 other cases
#      (2b - show them the money in the selected cases )
#   3 = the banker makes an offer; player must accept or reject
#   4 = the big reveal; did player win?

# ------------------------------   state 0 = beginning (start or restart)
if ($state == 0){
  print "This is State 0.<br>
Welcome... <br>
Instructions...
";
  showBoard("r");  # 'r' to put a radio button next to each case
  showCash();
  $state++;  # Now in state 1
} # 0

# ------------------------------  state 1 = user picked the winning breifcase
elseif ($state == 1){
  print "<b>This is state 1 </b><br><br>\n\n";

  $bc = $_POST['bc'] ?? "";   # the briefcase (bc) selected by the player
  if ($bc == "") {
    print "You didn't select a Breifcase!<br>
    Click Submit to redo previous step<br>\n";
    $state=0;
  }
  else {
    print "That's a great case to pick for a prize!<br>\n";
    $caseState[$bc] = "prize";  # the initial selection for prize
    print "Now, Select $numOpen other cases to open and reveal the content.<br>\n\n";
    showBoard("c");  # 'c' for a checkbox next to each case
    showCash();
    $state++; # now state 2
  }
} #1

# ------------------------------------   # state 2 = player picks 6 other cases
elseif ($state == 2) {
  print "<b>This is state 2 </b><br><br>\n\n";
  $n = openedCases();
  $m = casesSelectedByUser($numOpen - $n);  # n is max

  $n = openedCases();
  if ($n==$numOpen) {
    #      (2b - show them the money in the selected cases )
    print "Yeah, you selected $numOpen cases<br>";
    showBoard("");
    $state++; # now update the state
  }
  else if ($n < 6) {
    $d = 6 - $n;
    print "You have $d more to open. <br> Please finish.<br> ";
    # do NOT update the state here
    showBoard("c");
  }

  showCash();
} #2

# -----------------------------------------    # 3. the banker makes an offer
elseif ($state == 3) {
  print "<b>This is state 3</b><br>\n";
  showCash();

  $offer=bankerOffer();
  $offerStr="$".number_format($offer,0);
  print "<br>The Banker offers you <b>$offerStr</b>.  Will you take it?<br><br>";

  $takeOffer = $_POST['takeOffer'] ?? "";  # the player's choice
  list($prizeCase, $prizeCash)=playerCase();
  $prizeCashStr = "$".number_format($prizeCash,0);

  # 4. The big reveal  --> did the player accept or reject the offer?
  if ($takeOffer=='y') {
    print "He takes the Banker's offer! <br>
    You leave the briefcase. <br>
    Your briefcase #$prizeCase was worth: <b>$prizeCashStr</b><br><br>\n\n";

    # did the player win or loose?
    if ($prizeCash < $offer) { print "Good for you!<br>\n";   }
    else { print "So Sad!<br>\n"; }
    $state++;
  }
  elseif ($takeOffer=='n'){
    print "You reject the Banker's offer! <br>
    You keep the briefcase. <br>
    Your briefcase #$prizeCase was worth: <b>$prizeCashStr</b><br><br>\n\n";

    # did the player win or loose?
    if ($prizeCash > $offer) { print "Good for you!<br>\n";   }
    else { print "So Sad!<br>\n"; }
    $state++;
  }
  else {
    print "<input type=radio name=takeOffer value=y> Oh yeah, <b>Deal!</b>  I'll take the Banker's offer <br>
<input type=radio name=takeOffer value=n> <b> No Deal! </b> I'll keep my briefcase.<br>
<br>
  ";
    }

} # 3

# ---------------------------------------------
if ($state < 4) {
  # at the end of each round...
  print "<br><br> <input type=submit name=submit value='submit'>\n\n";
}


# last thing -- and always do -- store the state!
echo "<input type=hidden name=state value=$state>";
#  use implode to convert arrays to strings


# prof's faviorte debugging
echo "<pre>
current state = $state (at end of this round)
";
print_r($_POST);
echo "</pre>";

?>

<br><br><input type=submit name=reset value="Start Over">
</form>
</body>
</html>
