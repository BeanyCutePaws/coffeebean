<?php
// config.php (root /coffeebean/config.php)
// DB connection (mysqli)

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$db_host = "localhost";
$db_user = "root";
$db_pass = "Pokemon2003";          // XAMPP default
$db_name = "coffeebean";

try {
    $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
    $mysqli->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    exit("Database connection failed.");
}

// Optional: base URL helper (handy for links)
$BASE_URL = "/coffeebean";
