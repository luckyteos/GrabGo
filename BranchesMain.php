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

      function getOrderDetails($post){
        $servername = "localhost";
        $username = "root";
        $password = "";

        $myQuery = "SELECT fp.prodname AS name, o.numofprod AS quantity, fp.prodprice AS price, o.paymentType AS payment_method,
                    fp.prodprice*o.numofprod AS totalPrice
                    FROM appleseedfoodpro.foodpro fp
                    INNER JOIN appleseedorder.orders o on fp.prodno = o.prodnum
                    WHERE o.order_id = ".$post['orderNum'];

        $conn = new mysqli($servername, $username, $password);
        $result = $conn->query($myQuery);
        $temp = array();

        if ($result->num_rows > 0){
          while ($row = $result->fetch_assoc()){
            array_push($temp, $row);
          }
          echo json_encode($temp);
        } else {
          echo "Nothing's Here";
        }


      }

      function search($post){
        $servername = "localhost";
        $username = "root";
        $password = "";

        $conn = new mysqli($servername, $username, $password);
        $branchData = $_SESSION["postData"];
        $branchName = $branchData['branchName'];
        $branchQuery = "SELECT branch_id FROM appleseedcompany.branch WHERE branchadd = '".$branchName ."'";

        $branchQueryResult = $conn->query($branchQuery);
        $rowReturned = $branchQueryResult->fetch_assoc();
        $searchCategory = '';
        $addSQL = '';
        $newSearchQuery = $post['searchQuery'];

        if (isset($post['searchBy'])){

          switch ($post['searchBy']){
            case "Order Id": $searchCategory = "o.order_id = ";
                             break;
            case "Order Date": $newSearchQuery = "o.TimeDate = '" .$newSearchQuery ."'";
                               break;
            case "Order Type": $searchCategory = "o.OrderType";
                               $newSearchQuery = " = '".$newSearchQuery."'";
                               break;
            case "Customer Name": $searchCategory = "c.LName";
                                  $newSearchQuery = " Like '%".$newSearchQuery."%'";
                                  break;
            case "Product Name":  $addSQL = "INNER JOIN appleseedfoodpro.foodpro fp on fp.prodno = o.prodnum";
                                  $searchCategory = "fp.prodname";
                                  $newSearchQuery = " Like '".$newSearchQuery."%'";
                                  break;
            case "Payment Type": $searchCategory = "o.PaymentType";
                                 $newSearchQuery = " = '".$newSearchQuery."'";
                                 break;
          }
        }


        $myQuery = "SELECT o.order_id, c.LName AS custName, DATE_FORMAT(o.TimeDate,\"%Y-%m-%d\")
        AS orderDate, DATE_FORMAT(o.TimeDate, \"%H:%i:%s\") AS orderTime, o.OrderType
        FROM appleseedorder.orders o
        INNER JOIN appleseeduser.customer c on c.customer_id = o.customer_id
        INNER JOIN appleseedcompany.branch b on b.branch_id = o.branch_id " .$addSQL ." WHERE o.branch_id = ".$rowReturned['branch_id']
        ." AND " .$searchCategory .$newSearchQuery ." GROUP BY o.order_id, o.OrderType";

        $tempArray = array();

        if ($conn->query($myQuery)){
          $result = $conn->query($myQuery);
          if ($result->num_rows > 0){
            while ($row = $result->fetch_assoc()){
              array_push($tempArray, $row);
            }
            echo json_encode($tempArray);
          } else {
            $tempArray = array("Error"=>"Order Not Found");
            echo json_encode($tempArray);
          }
        } else {
          $tempArray = array("Error"=>"We could not find the item you are searching for :( Try something else?");
          echo json_encode($tempArray);
        }



      }

      if ($postData['function'] == "sendInfo"){
        sendInfo();
      } else if ($postData['function'] == "setBranchName") {
        setBranchName();
      } else if ($postData['function'] == "getOrderDetails"){
        getOrderDetails($postData);
      } else if ($postData['function'] == "search"){
        search($postData);
      }

  }

  if ($_SERVER["REQUEST_METHOD"] == "GET"){

    $servername = "localhost";
    $username = "root";
    $password = "";

    $branchData = $_SESSION["postData"];
    $branchName = $branchData['branchName'];
    $branchQuery = "SELECT branch_id FROM appleseedcompany.branch WHERE branchadd = '".$branchName ."'";

    $conn = new mysqli($servername, $username, $password);
    $branchQueryResult = $conn->query($branchQuery);
    $rowReturned = $branchQueryResult->fetch_assoc();

    $queryString = "SELECT o.order_id, c.LName AS custName, DATE_FORMAT(o.TimeDate,\"%Y-%m-%d\")
    AS orderDate, DATE_FORMAT(o.TimeDate, \"%H:%i:%s\") AS orderTime,
    o.OrderType
    FROM appleseedorder.orders o
    INNER JOIN appleseeduser.customer c on c.customer_id = o.customer_id
    INNER JOIN appleseedcompany.branch b on b.branch_id = o.branch_id
    WHERE o.branch_id = ".$rowReturned['branch_id']
    ." GROUP BY o.order_id, o.OrderType";

    $queryResult = $conn->query($queryString);
    $tempArray = array();

    if ($queryResult->num_rows > 0){
      while($row = $queryResult->fetch_assoc()){
        array_push($tempArray, $row);
      }
      echo json_encode($tempArray);
    } else {
      $tempArray = array("Error"=>"No Current Orders");
      echo json_encode($tempArray);
    }

  }
 ?>
