<?php

# ============================================
# init functions
# ============================================
function gameState() {
  # get the state of the current game.  $state is an integer 0 to 3

  global $cash, $caseCash, $state, $caseState;
  # $cash is an array of available cash values in the caseState
  # $caseCash is an array of the dollar amount in the case number
  # $state is the state number
  # $caseState is an array with a key for if they case is open, closed or selected

  # list the avilable cash values
  #     (these are randominzed in cases later )
  $cash = [1,3,5, 7,9,11, 13,15,20];  # 3x3 is 9  xxxxxxxxxxx

  if ($state==0) {
    # initialize the state variables
    $caseCash=$cash;      # put the cash values into a case
    # xxxxx($caseCash);     # now shuffle the case order.

    # make a new array of same length as $cash for the STATE of each bcase
    #  "." means closed, "o" = opened, "prize" means selected first
    $caseState = [".",".",".",".",".",".",".",".","."]; # make this a loop
    #  heres one way: loop over this statement:
    # xxxxxxxxxxxxxxxx
    #  $caseState[$i] = "."

  }
  else {
    # read in the HIDDEN strings from $_POST and make into php array
    #$caseCash = explode(",", $_POST['xxxxxx']);
    #$caseState = explode(",", $_POST['xxxxxx']);
    $caseCash = $cash;   # fake - debugging - non-shuffled
    $caseState = [".",".",".",".",".",".",".",".","."]; # fake - debugging
  }

}


# =================================================
# Board and briefcase functions
# =================================================
function cashInCase($case) {
  global $caseCash;
  return "$".number_format($caseCash[$case]);
}

function showCash() {
  # this prints out a table of the avilable cash
  # I like 2 rows.
  # if the cash is showing from an open case then darken the background

  global $cash, $caseState, $caseCash;
  $ncases = count($caseCash);

  # How can I know if a dollar vale is showing or not????
  #  I have to know the briefcase of the dollar value -
  #  but I "shuffled" them!  So...
  #  I need a reverse-hash -- $dollar value to state
  # a. loop over all the cases
  foreach (range(0,$ncases-1) as $n) {
    $dollar=$caseCash[$n];      # b. what is the dollar for that case?  $1, $10, $25...
    $state=$caseState[$n];      # c. is the case open or closed?  . or o or prize
    $cashHash[$dollar]=$state;  # d. a hash array of dollar amount to open or closed:
                                # example:  $10->o, $100->., $200->Prize
  }

  # header and start HTML table
  print "<br><br>(showCash function)
  <b>Cash Values still available in these $ncases cases</b></br>
  <table>\n";

  $i=0;
  # loop over the $cash array
  #   - use $cashHash to look up the case status (open/closed) for that cash
  #   - if open, color the background
  print("<tr><td>xxxxxxx</td><td>show</td><td>cash</td><td>values</td></tr>");

  print "</table>";
}

function showOutsideCase($i,$input) {
  # this function prints the value for ONE case -- case $i
  #  - the $input argument tells us if we should put a html
  #    in the cell also

  global $caseState, $caseCash;

  # cash (if opened)
  $cashInCaseStr = "$".number_format($caseCash[$i]);

  # background -- depends if open or closed
  $bgclr = ($caseState[$i] == ".") ? "" : "bgcolor='#DDD'";
  $bgclr = ($caseState[$i] == "prize") ? "bgcolor='#DDF'" : $bgclr;

  # what to display       open?             if opened      : if closed
  $caseString = ($caseState[$i] == "o") ? "$cashInCaseStr" : "Case #$i" ;

  # print the ONE html table CELL: <td> to </td> only
  print "<td align=center $bgclr> $caseString ";
  if ($caseState[$i] == ".") {  # not opened -- still in play
    if ($input == "r"){  #radio
      print "<input type=radio name=bc value=$i>";   }
    else if ($input == "c") { # checkbox
      print "<input type=checkbox name=box$i>";       }
  }
  print "</td>";
}


function showBoard($input) {
  # this makes the table of the cases
  #
  print "<br><br>(showBoard function)<table>\n";
  # loop over rows
    # loop over cols
      # print inside the cell
      echo "<tr>";
      #  xxxxxxx  make this a loop for your game  xxxxxxxxxxxxxxxxx
      showOutsideCase(0,$input); # what goes in the cell of 1 case
      showOutsideCase(1,$input); # what goes in the cell of 1 case
      showOutsideCase(2,$input); # what goes in the cell of 1 case
      echo "</tr><tr>";
      showOutsideCase(3,$input); # what goes in the cell of 1 case
      showOutsideCase(4,$input); # what goes in the cell of 1 case
      showOutsideCase(5,$input); # what goes in the cell of 1 case
      echo "</tr>";
  print "</table>";
}


# ---------------------------------------
function casesSelectedByUser($max) {
  # this reads in all the variables from the HTML form $_POST
  #  - if the letters "box" is in the key, then this is a briefcase
  #  - a "box" is here because the player wants it OPENED
  global $caseState;
  $m=0;
  foreach ($_POST as $k => $v) {
    if(substr($k, 0, 3) != "box") { continue; }  # a box or not?
    $m++; # how many have I opened???
    $bc = substr($k,3);  # get the briefcase number from a substr
    # print "k=$k, v=$v, bc=$bc<br>\n";  # debug
    $caseState[$bc]="o"; # open that Briefcase !!
    if ($m == $max) { break; }  # no more than the max number
  }
  return $m;  # return how many breifcases were opened
}


function openedCases() {
  # loop over all the briefcases, and count the number of OPENED ones
  global $caseState;
  $o=0;
  foreach ($caseState as $k => $v) {
    if ($v == "o"){$o++;}   # increment if open
  }
  return $o;   # number of open cases
}


function bankerOffer() {
  # this returns the dollar value of the banker offer to buy
  # selected briefcase
  # You CANNOT just look at selected briefcase -- that is cheating
  global $caseState, $caseCash;
  $sum=0;
  $n=0;
  # xxxxxxxxxxxxxxxxxxxx
  # a good choice is the average of non-opened cases
  return 5;   # this is a dummy value
}


function playerCase (){
  # this returns an array of the selected winning briefcase
  #   array --> the case number and dollar value in that case
  global $caseState, $caseCash;

  foreach ($caseCash as $k => $v) {
    if ($caseState[$k]=="prize"){
      # xxxxxxxxxxxxxxx    what to do if you find the winning case
    }
  }
  return [2, 63];  # dummy numbers
}




?>
