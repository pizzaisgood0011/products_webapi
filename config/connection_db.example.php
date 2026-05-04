<?php
$hostname = "localhost";
$username = "your_mysql_username";
$password = "your_mysql_password";
$dbname = "webapi_db";
$port = 3306;

$conn = new mysqli($hostname, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    http_response_code(500);
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}