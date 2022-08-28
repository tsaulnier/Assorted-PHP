<?php

$dimension = $_GET['dimension'] ?? '';
$mathFunct = $_GET['mathFunct'] ?? '';

if (!is_numeric($dimension)) {
  echo "<span style='color: red'> Your specified table size is not a number. Please enter a numeric value between 2 and 20 inclusive.</span>";
  exit;
}

if ($dimension < 2 || $dimension > 20) {
  echo "<span style='color: red'>Your specified dimension is outside of the allowed range of 2-20. Please enter a valid number within range.</span>";
  exit;
}

$operator = "";

if ($mathFunct == 'addition') {
  $operator = "+";
}
elseif ($mathFunct == 'multiplication') {
  $operator = "X";
}
elseif ($mathFunct == 'modulo') {
  $operator = "%";
}

if ($operator == "") {
  echo "<span style='color: red'>The math function is unspecified. Please select one of the three options to generate your table.</span>";
}

$altCounterA = 0;
$altCounterB = 0;
echo "<table>";
echo "<tr><th style='width:30px' bgcolor='#CBB'> $operator </th>";
for ($iColumnHeader=1; $iColumnHeader<=$dimension; $iColumnHeader++) {
  echo "<th style='width:30px' bgcolor='#CEE'> $iColumnHeader </th>";
}
echo "</tr>";

for ($iRow=1; $iRow<=$dimension; $iRow++) {
  echo "<tr><th bgcolor='#CEE'>$iRow</th>";
  for ($iColumn=1; $iColumn<=$dimension; $iColumn++) {
    $iSum = $iRow + $iColumn;
    $iProduct = $iRow * $iColumn;
    $iMod = $iRow % $iColumn;
    switch ($operator) {
      case "+" : $iResult = $iSum; break;
      case "X" : $iResult = $iProduct; break;
      case "%" : $iResult = $iMod; break;
    }
    $altCounterA++;
    $cellShade = ($iRow +$altCounterA + $altCounterB) % 2 == 1 ? "bgcolor='#AAA'" : "bgcolor:'#FEF'";
    echo "<td style='text-align:center;'$cellShade>$iResult</td>";
  }
  $dimension % 2 == 1 ? $altCounterB++ : "";
  echo "</tr>\n";

}

echo "</table>";

?>
