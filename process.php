<?php

$searchQuery = '';
$searchChoice = '';
$xmlfile = simplexml_load_file("xml_data/response_full.xml");


// Form Validation & Main Search
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $searchQuery = test_input($_POST['query']); // This will have the user's search query
    $searchQuery = ucwords(strtolower($searchQuery));
    $searchChoice = test_input($_POST['searchChoice']); // This will store the user's choice for what to search by
    if ($searchChoice == "name") {
      searchName($searchQuery,$xmlfile);
    } elseif ($searchChoice == "price") {
      searchPrice($searchQuery,$xmlfile);
    } elseif ($searchChoice == "rating") {
      searchRating($searchQuery,$xmlfile);
    } else {
      searchStyle($searchQuery,$xmlfile);
    }
}

function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}



function compareName($nameA, $nameB) {
  $retrieval = strnatcmp($nameA->name,$nameB->name);
  return $retrieval;
}

function searchStyle($search, $filename) {
  $path = "//businesses[categories/title[contains(text(),'".$search."')]]";
  $sortarray = array();
  foreach ($filename->xpath($path) as $businesses) {
    $sortarray[] = $businesses;
  }
  if (count($sortarray) != 0) {
    echo "<thead><tr><th>Business Name</th>".
    "<th>Business Address</th><th>Cuisine</th><th>Rating</th><th>Price</th></tr></thead>";
  usort($sortarray, __NAMESPACE__ . '\compareName');
  foreach ($sortarray as $businessinfo) {
    $url = $businessinfo->url;
    echo "<tr><td><a href='{$url}' target='_blank'>".$businessinfo->name."</a></td>".
    "<td>".$businessinfo->location->address1."<br/>".$businessinfo->location->city.", OH</td>".
    "<td>".$businessinfo->categories->title."</td>".
    "<td>".$businessinfo->rating."</td>".
    "<td>".$businessinfo->price."</td>".
    "</tr>";
  }} else {
    echo "<h3>No results found</h3>";
    return;
  }
  var_dump($sortarray);
}


function searchName($search,$filename) {
  $path = "//businesses[name[contains(text(),'".$search."')]]";
  $sortarray = array();
  foreach ($filename->xpath($path) as $businesses) {
    $sortarray[] = $businesses;
  }
  if (count($sortarray) != 0) {
    echo "<thead><tr><th>Business Name</th>".
    "<th>Business Address</th><th>Cuisine</th><th>Rating</th><th>Price</th></tr></thead>";
  usort($sortarray, __NAMESPACE__ . '\compareName');
  foreach ($sortarray as $businessinfo) {
    $url = $businessinfo->url;
    echo "<tr><td><a href='{$url}' target='_blank'>".$businessinfo->name."</a></td>".
    "<td>".$businessinfo->location->address1."<br/>".$businessinfo->location->city.", OH</td>".
    "<td>".$businessinfo->categories->title."</td>".
    "<td>".$businessinfo->rating."</td>".
    "<td>".$businessinfo->price."</td>".
    "</tr>";
  }} else {
    echo "<h3>No results found</h3>";
    return;
  }
  var_dump($sortarray);
}

function searchPrice($search,$filename) {
  if ($search == "") {
    $search = "$";
  }
  $path = "//businesses[price[text()='".$search."']]";
  $sortarray = array();
  foreach ($filename->xpath($path) as $businesses) {
    $sortarray[] = $businesses;
  }
  if (count($sortarray) != 0) {

  echo "<thead><tr><th>Business Name</th>".
  "<th>Business Address</th><th>Cuisine</th><th>Rating</th><th>Price</th></tr></thead>";
    usort($sortarray, __NAMESPACE__ . '\compareName');
    foreach ($sortarray as $businessinfo) {
    $url = $businessinfo->url;
    echo "<tr><td><a href='{$url}' target='_blank'>".$businessinfo->name."</a></td>".
    "<td>".$businessinfo->location->address1."<br/>".$businessinfo->location->city.", OH</td>".
    "<td>".$businessinfo->categories->title."</td>".
    "<td>".$businessinfo->rating."</td>".
    "<td>".$businessinfo->price."</td>".
    "</tr>";
  }} else {
    echo "<h3>No results found</h3>";
    return;
  }
  var_dump($sortarray);
}




function searchRating($search,$filename) {
  if ($search == "") {
    $search = 0;
  }
  $path = "//businesses[rating>=".$search."]";
  $sortarray = array();
  foreach ($filename->xpath($path) as $businesses) {
    $sortarray[] = $businesses;
  }
    usort($sortarray, __NAMESPACE__ . '\compareName');
if (count($sortarray) != 0) {
  echo "<thead><tr><th>Business Name</th>".
  "<th>Business Address</th><th>Cuisine</th><th>Rating</th><th>Price</th></tr></thead>";
    foreach ($sortarray as $businessinfo) {
    $url = $businessinfo->url;
    echo "<tbody><tr><td><a href='{$url}' target='_blank'>".$businessinfo->name."</a></td>".
    "<td>".$businessinfo->location->address1."<br/>".$businessinfo->location->city.", OH</td>".
    "<td>".$businessinfo->categories->title."</td>".
    "<td>".$businessinfo->rating."</td>".
    "<td>".$businessinfo->price."</td>".
    "</tr></tbody>";
  }} else {
    echo "<h3>No results found</h3>";
    return;
  }
  var_dump($sortarray);

}

 ?>
