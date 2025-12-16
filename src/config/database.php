<?php
$servername = "mysql";
$username = "appuser";
$password = "secret";
$dbname = "appdb";

$conn = new mysqli($servername, $username, $password, $dbname);

if(!$conn){
    die("Koneksi Gagal: ". mysqli_connect_error());
}