<?php

  //To be removed before submission
  /*ini_set('display_errors', 'On');
  error_reporting(E_ALL | E_STRICT);
  header("Content-Type: application/json; charset=UTF-8");*/
  //

  if ($_SERVER["REQUEST_METHOD"] == "POST"){
    $postData = json_decode(file_get_contents("php://input"), true);

    function search($postData){

      $servername = "localhost";
      $username = "root";
      $password = "";

      $searchQuery = $postData['query'];
      $conn = new mysqli ($servername, $username, $password);

      /*if ($conn->connect_error){
        die ("Connection Failed. Please try again" . $conn->connect_error);
      } else {
        //echo "Connected Successfully";
      }*/

      //echo $searchQuery;
      $dbquery = "select BranchAdd from AppleSeedCompany.Branch where BranchAdd Like '%" .$searchQuery."%'" . "AND company_id = 1";
      $result = $conn->query($dbquery);
      $temp = array();

      if ($result->num_rows > 0){
          while ($row = $result->fetch_assoc()){
            $temp1 = array("BranchAdd"=>$row["BranchAdd"]);
            array_push($temp, $temp1);
          }
          echo json_encode($temp);
      } else {
        echo "Something's Amiss";
      }
    }

    if ($postData['function']=="search"){
      search($postData);
    }



  }

  if($_SERVER["REQUEST_METHOD"] == "GET"){
    $servername = "localhost";
    $username = "root";
    $passWD = "";

    $conn =  new mysqli($servername, $username, $passWD);

    /*if ($conn->connect_error){
      die ("Connection Failed ". $conn->connect_error);
    }
    echo "Connection Successful";*/

    $sql = "select * from AppleSeedCompany.branch where company_id = 1";
    $result = $conn->query($sql);
    $newArray = array();

    if ($result->num_rows > 0){
      while ($row = $result->fetch_assoc()){
        $array = array("BranchAdd"=>$row["BranchAdd"]);
        array_push($newArray, $array);
      }
      echo json_encode($newArray);
    } else {
      echo "There's nothing";
    }

  }

?>
