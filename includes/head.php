<?php
// includes/head.php
// Usage: set $page_title before including this file.
// Example: $page_title = "Home"; include "includes/head.php";

if (!isset($page_title)) {
    $page_title = "Don Macchiatos";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> | Don Macchiatos</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- FontAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@100;300;400;700;900&display=swap" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">

    <link rel="shortcut icon" href="assets/img/favicon.ico">

    <!-- Leaflet (FREE map, no API key) -->
    <link
    rel="stylesheet"
    href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    />
    <script
    src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    defer
    ></script>

</head>
<body>
