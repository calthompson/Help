<?php
// initializing the variables
$searchQuery = '';
$searchChoice = '';
$xmlfile = simplexml_load_file("xml_data/response_full.xml");


// Form Validation & Main Search
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $searchQuery = test_input($_POST['query']); // This will have the user's search query
    $searchQuery = ucwords(strtolower($searchQuery));
    $searchChoice = test_input($_POST['searchChoice']); // This will store the user's choice for what to search by

// Execute different functions based on radio button choice
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

// Function to help sanitize data
function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}


// Function to help sort the array alphabetically
function compareName($nameA, $nameB) {
  $retrieval = strnatcmp($nameA->name,$nameB->name);
  return $retrieval;
}

// Search by Cuisine
function searchStyle($search, $filename) {
  // xpath search for categories with the search query, and put results into an array
  $path = "//businesses[categories/title[contains(text(),'".$search."')]]";
  $sortarray = array();
  foreach ($filename->xpath($path) as $businesses) {
    $sortarray[] = $businesses;
  }
  // if a match is found, output an html table
  if (count($sortarray) != 0) {
    echo "<thead><tr><th>Business Name</th>".
    "<th>Business Address</th><th>Cuisine</th><th>Rating</th><th>Price</th></tr></thead>";
    usort($sortarray, __NAMESPACE__ . '\compareName'); // initial alphabetical sort

    foreach ($sortarray as $businessinfo) {
      $url = $businessinfo->url;
      echo "<tr><td><a href='{$url}' target='_blank'>".$businessinfo->name."</a></td>".
      "<td>".$businessinfo->location->address1."<br/>".$businessinfo->location->city.", OH</td>".
      "<td>".$businessinfo->categories->title."</td>".
      "<td>".$businessinfo->rating."</td>".
      "<td>".$businessinfo->price."</td>".
      "</tr>";
    }
  } else {  // if nothing is found, let the user know there were no results
      echo "<h3>No results found</h3>";
      return;
    }
  var_dump($sortarray);
}

// Search by Restaurant Name
function searchName($search,$filename) {
  // xpath search for categories with the search query, and put results into an array
  $path = "//businesses[name[contains(text(),'".$search."')]]";
  $sortarray = array();
  foreach ($filename->xpath($path) as $businesses) {
    $sortarray[] = $businesses;
  }
  // if a match is found, output an html table
  if (count($sortarray) != 0) {
    echo "<thead><tr><th>Business Name</th>".
    "<th>Business Address</th><th>Cuisine</th><th>Rating</th><th>Price</th></tr></thead>";
    usort($sortarray, __NAMESPACE__ . '\compareName'); // initial alphabetical sort

    foreach ($sortarray as $businessinfo) {
      $url = $businessinfo->url;
      echo "<tr><td><a href='{$url}' target='_blank'>".$businessinfo->name."</a></td>".
      "<td>".$businessinfo->location->address1."<br/>".$businessinfo->location->city.", OH</td>".
      "<td>".$businessinfo->categories->title."</td>".
      "<td>".$businessinfo->rating."</td>".
      "<td>".$businessinfo->price."</td>".
      "</tr>";
    }
  } else {  // if nothing is found, let the user know there were no results
      echo "<h3>No results found</h3>";
      return;
    }
  var_dump($sortarray);
}
// Search by Restaurant Price
function searchPrice($search,$filename) {
  if ($search == "") {
    $search = "$";
  }
  // xpath search for categories with the search query, and put results into an array
  $path = "//businesses[price[text()='".$search."']]";
  $sortarray = array();
  foreach ($filename->xpath($path) as $businesses) {
    $sortarray[] = $businesses;
  }
  // if any matches are found, output an html table
  if (count($sortarray) != 0) {
    echo "<thead><tr><th>Business Name</th>".
    "<th>Business Address</th><th>Cuisine</th><th>Rating</th><th>Price</th></tr></thead>";
    usort($sortarray, __NAMESPACE__ . '\compareName'); // initial alphabetical sort

    foreach ($sortarray as $businessinfo) {
      $url = $businessinfo->url;
      echo "<tr><td><a href='{$url}' target='_blank'>".$businessinfo->name."</a></td>".
      "<td>".$businessinfo->location->address1."<br/>".$businessinfo->location->city.", OH</td>".
      "<td>".$businessinfo->categories->title."</td>".
      "<td>".$businessinfo->rating."</td>".
      "<td>".$businessinfo->price."</td>".
      "</tr>";
    }
  } else { // if nothing is found, let the user know there were no results
      echo "<h3>No results found</h3>";
      return;
    }
  var_dump($sortarray);
}



// Search by Restaurant Rating (out of 5)
function searchRating($search,$filename) {
  if ($search == "") {
    $search = 0;
  }
  // xpath search for categories with the search query, and put results into an array
  $path = "//businesses[rating>=".$search."]";
  $sortarray = array();
  foreach ($filename->xpath($path) as $businesses) {
    $sortarray[] = $businesses;
  }

  // if any matches are found, output an html table
  if (count($sortarray) != 0) {
    echo "<thead><tr><th>Business Name</th>".
    "<th>Business Address</th><th>Cuisine</th><th>Rating</th><th>Price</th></tr></thead>";
    usort($sortarray, __NAMESPACE__ . '\compareName'); // initial alphabetical sort

    foreach ($sortarray as $businessinfo) {
      $url = $businessinfo->url;
      echo "<tbody><tr><td><a href='{$url}' target='_blank'>".$businessinfo->name."</a></td>".
      "<td>".$businessinfo->location->address1."<br/>".$businessinfo->location->city.", OH</td>".
      "<td>".$businessinfo->categories->title."</td>".
      "<td>".$businessinfo->rating."</td>".
      "<td>".$businessinfo->price."</td>".
      "</tr></tbody>";
    }
  } else {  // if nothing is found, let the user know there were no results
      echo "<h3>No results found</h3>";
      return;
    }
  var_dump($sortarray);

}

 ?>
