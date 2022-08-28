<!doctype=html>

<html>
<head></head>
<body>
<style>
table, th, td {
  border: 2px solid black;
  text-align: center;
}
</style>
<h1>Welcome to Math Table v3.0!</h1><br>
<h2>Please specify the desired dimension you'd like for your table. It is a square table with dimension NxN.</h2><br><br>

Number of rows and columns: <input type='text' id='dimension' onchange='mathFunction();'><br><br>

Select the math function you'd like for your table.<br><br>

Addition: <input type='radio' id='addition' name='math' onClick='mathFunction();'><br>
Multiplication: <input type='radio' id='multiplication' name='math' onClick='mathFunction();'><br>
Modulo: <input type='radio' id='modulo' name='math' onClick='mathFunction();'><br><br>

<div id='result'></div>

<script>

function mathFunction() {

  mathFunct = "";
  dimension=document.getElementById('dimension').value;

  if (document.getElementById('addition').checked) {
    mathFunct='addition';
  }
  else if (document.getElementById('multiplication').checked) {
    mathFunct='multiplication';
  }
  else if (document.getElementById('modulo').checked) {
    mathFunct='modulo';
  }

  xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      document.getElementById('result').innerHTML = this.responseText;
    }
  };
  xhttp.open('GET', './lab11_AJAX.php?dimension='+dimension+'&mathFunct='+mathFunct, true);
  xhttp.send();
}

</script>



</body>
</html>
