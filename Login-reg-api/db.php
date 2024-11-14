<?php
$username = "root";
$password = "";
$host = "localhost";
$db = "auth-db";

$con = mysqli_connect($host, $username, $password, $db);
if($con){
    echo "Database connected successfully...!!";
}
else{
    echo "Error... Did not connect...!!";
}




?>