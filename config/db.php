<?php

$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "MindMerge";

$connect = mysqli_connect($servername, $username, $password, $dbname);

if (!$connect) {
    die("Connection failed: " . mysqli_connect_error());
}

mysqli_connect($connect, 'utf8');
