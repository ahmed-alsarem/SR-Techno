

<?php
$servername = "localhost";
$username = "root"; // غيره حسب إعدادك
$password = "";     // ضع باسورد mysql إذا عندك
$dbname = "sr_techno";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
}

?>

