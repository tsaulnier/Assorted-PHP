<!doctype=html>

<html>
<head></head>
<body>
<style>
table, th, td {
  border: 2px solid black;
  width:100px;
  text-align: center;
}
</style>
<h1>Let's play Tic Tac Toe!</h1><br>
<table>
  <tr><td id=cellTL onClick="changeme(this.id);">&nbsp</td><td id=cellTM onClick="changeme(this.id);">&nbsp</td><td id=cellTR onClick="changeme(this.id);">&nbsp</td></tr>
  <tr><td id=cellML onClick="changeme(this.id);">&nbsp</td><td id=cellMM onClick="changeme(this.id);">&nbsp</td><td id=cellMR onClick="changeme(this.id);">&nbsp</td></tr>
  <tr><td id=cellBL onClick="changeme(this.id);">&nbsp</td><td id=cellBM onClick="changeme(this.id);">&nbsp</td><td id=cellBR onClick="changeme(this.id);">&nbsp</td></tr>
</table>
<script>

function changeme(id_of_box_clicked) {

  state=document.getElementById('state').value;
  if (state == 'X') {
    document.getElementById(id_of_box_clicked).innerHTML='X';
    document.getElementById('state').value='O';
  }
  else {
    document.getElementById(id_of_box_clicked).innerHTML='O';
    document.getElementById('state').value='X';
  }
}

</script>

<input type='hidden' name='XorO' value='X' id=state>

</body>
</html>
