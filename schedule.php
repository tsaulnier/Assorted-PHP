<!doctype html>
<html>
<head></head>
<style>
table, th, td {
   border: 1px solid black;
   border-collapse: collapse;
}
td {
  text-align: center;
}
</style>
<body>

<form method=POST action='./index.php'>


<?php

$state = $_POST['state'] ?? "Request Another";

//state 1

if ($state == "Request Another" || $state == "Return to Main Menu") {

  echo "<h1> Request Course Schedule</h1> <br>";
  echo "<p> <em> Select the student's and click Submit to obtain their schedule. </em> </p>";
  echo "<p> <em> Get schedule for: </em>";

  echo "<select name='student_name'>
    <option value='choose_a_student'>Choose A Student</option>
    <option value='benjamin_hawthorne'>Benjamin Hawthorne</option>
    <option value='gabrielle_huleimani'>Gabrielle Huleimani</option>
    <option value='franklin_ford'>Franklin Ford</option>
    <option value='helena_hilmisdottir'>Helena Hilmisdottir</option>
    <input type='submit' name='state' value='Submit'>";

}
//state 2

if ($state == "Submit" && isset($_POST['student_name'])) {
  if ($_POST['student_name'] == 'choose_a_student') {
    echo "<p>You didn't choose a name in the dropdown.<br></p>";
    echo "<input type='submit' name='state' value='Return to Main Menu'>";
  }
  else {
    echo "<h1> Course Schedule Results</h1> <br>";
    //mysqli
    $db = new mysqli('127.0.0.1', 'root', '', 'lab07_task2');
    if ($db->connect_errno > 0) {
      echo "<p>Error: Could not connect to schedule database.<br></p>";
      echo "<pre>\nErrno: " . $db->errno . "\n";
      echo "Error: " . $db->error . "\n</pre><br>\n";
      exit;
    }
    else {
      //Handle the data. No fancy user input (dropdown)so
      //no need to account for injection
      $studentName;
      $studentId;
      switch($_POST['student_name']) {

        case "benjamin_hawthorne" : $studentId = 1001; $studentName = "Benjamin Hawthorne"; break;
        case "gabrielle_huleimani" : $studentId = 1002; $studentName = "Gabrielle Huleimani"; break;
        case "franklin_ford" : $studentId = 1003; $studentName = "Franklin Ford"; break;
        case "helena_hilmisdottir" : $studentId = 1004; $studentName = "Helena Hilmisdottir"; break;

      }
      echo "<p> <em> Here is <b>".$studentName."</b>'s schedule. </em> </p><br><br>";

      $query = "select concat(c.department,c.course_number,': ',c.name) as course,
      concat(ci.days_scheduled,' ', ci.startTime, '-', ci.endTime, ' ', ci.semester) as time,
      i.name as instructor_name, concat(i.office, ', ', i.office_hours) as office_hours, i.email as contact
      from ClassInstantiation ci
      inner join course c on c.course_id=ci.course_id
      inner join instructor i on i.instructor_id=ci.instructor_id
      inner join enrolled e on e.instantiation_id=ci.instantiation_id
      inner join student s on s.student_id=e.student_id
      where s.student_id =".$studentId;

      //send query to mysql and collect result
      $result = $db->query($query);
      if (!$result) {
        echo "There was a problem processing your request.";
        echo "<pre>\nErrno: ".$db->errno."\n";
        echo "Error: ".$db->error."\n</pre><br>\n";
      }
      else {
        echo "<table>";
        $headers = ['Course', 'Time', 'Instructor', 'Office Hours', 'Contact'];
        for ($iHeader = 0; $iHeader < 5; $iHeader++) {
          echo "<th bgcolor=#CEF>".$headers[$iHeader]."</th>";
        }
        echo "</th>";
        while($row = mysqli_fetch_array($result)){
          echo "<tr><td>" . $row['course'] . "</td><td>"
          . $row['time'] . "</td><td>" . $row['instructor_name']
          . "</td><td> " . $row['office_hours'] . "</td><td>"
          . $row['contact'] . "</td>";
        }

        echo "</table><br><br>";
        mysqli_close($db);

      }


      echo "<input type='submit' name='state' value='Return to Main Menu'>";
    }
  }
}

?>
</form>
</body>
</html>
