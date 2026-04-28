<?php 
$host='127.0.0.1';
$user='root';
$pass='root';
$db='Stock_Portfolio';
$port=8889;

$conn=new mysqli($host,$user,$pass,$db,$port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
