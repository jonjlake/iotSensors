<?php
  $login_path = $_SERVER['DOCUMENT_ROOT'].'/phpSchool/login.php';
//  echo $login_path;
  require_once($login_path);

  $conn = new mysqli($hn, $un, $pw, 'sensors');
  if ($conn->connect_error) die($conn->connect_error);

//print_column_names($conn);

  echo <<<_END
    <h1>Sensors</h1>
    <table>
_END;

  $column_query = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = 'sensors' AND TABLE_NAME = 'thermistors'";

  $column_result = $conn->query($column_query);
  echo "<tr>";
  $rows = $column_result->num_rows;
//echo "Found $rows rows";
  for ($j = 0 ; $j < $rows ; $j++)
  {
//    echo "Printing row $j";
    $row = $column_result->fetch_array(MYSQLI_NUM);
    echo "<th>$row[0]</th>";
  }
  echo "</tr>";

  $query = "SELECT * FROM thermistors";
  $result = $conn->query($query);
  if (!$result) die("Database access failed: " . $conn->error);

  $rows = $result->num_rows;
  for ($j = 0 ; $j < $rows ; ++$j)
  {
//    $result->data_seek($j);
    $row = $result->fetch_array(MYSQLI_NUM);
    
    echo "<tr>";
    $num_cols = count($row);
    for ($k = 0; $k < $num_cols; ++$k) 
    {
      echo "<td>$row[$k]</td>";
    }
    echo "</tr>";
/*    echo <<<_END
  <pre>
    $row[0] $row[1] $row[2] $row[3] $row[4] $row[5] $row[6] $row[7] $row[8]
  </pre>*/
_END;
 }

  echo "</table>";
  
  $result->close();
  $conn->close();

  function print_column_names($conn)
  {
    $query = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = 'sensors' AND TABLE_NAME = 'thermistors'";
    $result = $conn->query($query);
    if (!$result) die("Database access failed: " . $conn->error);    
    $rows = $result->num_rows;
    echo "Number of rows in column query: $rows"; 
   
    echo "<pre>"; 
    for ($j = 0; $j < $rows; ++$j)
    {
      $result->data_seek($j);
      $row = $result->fetch_array(MYSQLI_NUM);
//      $num_elements = count($row);
//      echo "Number of elements in column query row $j: $num_elements<br>";
      echo $row[0]." ";
    } 
    echo "</pre>"; 
  }
?>
