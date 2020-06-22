<?php
require 'curl.php';
// initializing the variables
$searchQuery = '';



// Form Validation & Main Search
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $searchQuery = test_input($_POST['query']); // This will have the user's search query
    $searchQuery = ucwords(strtolower($searchQuery));
    query_api($searchQuery, "Cleveland, OH");
}

