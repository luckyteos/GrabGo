<?php
  if ($_SERVER["REQUEST_METHOD"] == "POST"){
    $postData = json_decode(file_get_contents("php://input"),true);
    echo $postData["branchName"];
  }
 ?>
