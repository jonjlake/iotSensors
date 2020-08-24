<?php
  $login_path = $_SERVER['DOCUMENT_ROOT'].'/phpSchool/login.php';
  require_once($login_path);

  $conn = new mysqli($hn, $un, $pw, 'sensors');
  if ($conn->connect_error) die($conn->connect_error);

  print_db_table("thermistors", $conn);
  print_db_table("test_measurements", $conn); 

  $conn->close();

  function print_db_table($table_name, $conn)
  {
    echo <<<_END
      <h1>$table_name</h1>
      <table>
  _END;

    $column_query = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = 'sensors' AND TABLE_NAME = '$table_name'";
    $column_result = $conn->query($column_query);
    print_table_header($column_result);

    $query = "SELECT * FROM $table_name";
    $result = $conn->query($query);
    if (!$result) die("Database access failed: " . $conn->error);

    print_table_body($result);

    echo "</table>";
    
    $result->close();
    $column_result->close(); 
  }

  function print_table_header($column_result)
  {
    echo "<tr>";
    $rows = $column_result->num_rows;
    for ($j = 0 ; $j < $rows ; $j++)
    {
      $row = $column_result->fetch_array(MYSQLI_NUM);
      echo "<th>$row[0]</th>";
    }
    echo "</tr>";
  }
  
  function print_table_body($result)
  {
    $rows = $result->num_rows;
    for ($j = 0 ; $j < $rows ; ++$j)
    {
      $row = $result->fetch_array(MYSQLI_NUM);
    
      echo "<tr>";
      $num_cols = count($row);
      for ($k = 0; $k < $num_cols; ++$k) 
      {
        echo "<td>$row[$k]</td>";
      }
      echo "</tr>";
    }  
  }

?>
