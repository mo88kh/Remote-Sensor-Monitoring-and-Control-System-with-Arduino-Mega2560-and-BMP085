<?php
  require 'database.php';

  if (!empty($_POST)) {
    $id = $_POST['id'];
    $temperature = $_POST['temperature'];
    $pressure = $_POST['pressure'];
    $altitude = $_POST['altitude'];
    $LED_01 = $_POST['LED_01'];
    $LED_02 = $_POST['LED_02'];

    date_default_timezone_set("Asia/Jakarta");
    $tm = date("H:i:s");
    $dt = date("Y-m-d");

    $pdo = Database::connect();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $sql = "UPDATE mega2560_table_bmp085_leds_update SET temperature = ?, pressure = ?, altitude = ?, time = ?, date = ? WHERE id = ?";
    $q = $pdo->prepare($sql);
    $q->execute(array($temperature, $pressure, $altitude, $tm, $dt, $id));

    $id_key = generate_string_id(10);
    $board = $id;

    $sql = 'SELECT * FROM mega2560_table_bmp085_leds_record WHERE id = ?';
    $q = $pdo->prepare($sql);

    do {
      $q->execute(array($id_key));
      $found_empty = !$q->fetch();
      if (!$found_empty) {
        $id_key = generate_string_id(10);
      }
    } while (!$found_empty);

    // Insert data into mega2560_table_bmp085_leds_record
    $sql = "INSERT INTO mega2560_table_bmp085_leds_record (id, board, temperature, pressure, altitude, LED_01, LED_02, time, date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $q = $pdo->prepare($sql);
    $q->execute(array($id_key, $board, $temperature, $pressure, $altitude, $LED_01, $LED_02, $tm, $dt));

    Database::disconnect();
  }

  function generate_string_id($strength = 16) {
    $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $input_length = strlen($permitted_chars);
    $random_string = '';
    for($i = 0; $i < $strength; $i++) {
        $random_character = $permitted_chars[mt_rand(0, $input_length - 1)];
        $random_string .= $random_character;
    }
    return $random_string;
  }
?>
