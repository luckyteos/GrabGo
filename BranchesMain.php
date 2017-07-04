<?php
  session_start();
  $postData;
    if ($_SERVER["REQUEST_METHOD"] == "POST"){

      try {
        $postData = json_decode(file_get_contents("php://input"),true);
      } catch (Exception $e){
        echo "JSON Decode Error: " . $e->getMessage() . "\n";
      }

      function sendInfo(){
        $_SESSION["postData"] = json_decode(file_get_contents("php://input"),true);
        echo "Branch_Orders.html";
      }

      function setBranchName(){
        $branchData = $_SESSION["postData"];
        $branchName = $branchData['branchName'];
        echo $branchName;
      }

      if ($postData['function'] == "sendInfo"){
        sendInfo();
      } else if ($postData['function'] == "setBranchName") {
        setBranchName();
      }

  }

  if ($_SERVER["REQUEST_METHOD"] == "GET"){
    $postData = $_SESSION["branchName"];

    $servername = "localhost";
    $username = "root";
    $password = "";

    $queryString = "SELECT o.order_id, c.LName AS custName, DATE_FORMAT(o.TimeDate,\"%d-%m-%Y\")
    AS orderDate, DATE_FORMAT(o.TimeDate, \"%H:%i:%s\") AS orderTime,
    o.OrderType, fp.ProdName, o.numofprod, o.paymentType, o.numofprod*fp.prodprice AS totalPayment
    FROM appleseedorder.orders o
    INNER JOIN appleseeduser.customer c on c.customer_id = o.customer_id
    INNER JOIN appleseedfoodpro.foodpro fp on fp.prodno = o.prodnum
    INNER JOIN appleseedcompany.branch b on b.branch_id = o.branch_id
    WHERE o.branch_id = 3";

    $conn = new mysqli($servername, $username, $password);
    $queryResult = $conn->query($queryString);
    $tempArray = array();

    if ($queryResult->num_rows > 0){
      while($row = $queryResult->fetch_assoc()){
        array_push($tempArray, $row);
      }
      echo json_encode($tempArray);
    } else {
      echo "Nothing's Here";
    }

  }
 ?>
