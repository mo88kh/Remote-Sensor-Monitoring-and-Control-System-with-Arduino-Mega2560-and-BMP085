<?php
  include 'database.php';
  
  if (!empty($_POST)) {
    $id = $_POST['id'];
    
    $myObj = (object)array();
    
    $pdo = Database::connect();
    $sql = 'SELECT * FROM mega2560_table_bmp085_leds_update WHERE id="' . $id . '"';
    foreach ($pdo->query($sql) as $row) {
      $date = date_create($row['date']);
      $dateFormat = date_format($date,"d-m-Y");
      
      $myObj->id = $row['id'];
      $myObj->temperature = $row['temperature'];
      $myObj->pressure = $row['pressure'];
      $myObj->altitude = $row['altitude'];
      $myObj->LED_01 = $row['LED_01'];
      $myObj->LED_02 = $row['LED_02'];
      $myObj->ls_time = $row['time'];
      $myObj->ls_date = $dateFormat;
      
      $myJSON = json_encode($myObj);
      
      echo $myJSON;
    }
    Database::disconnect();

  }

?>