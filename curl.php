<?php
$app = parse_ini_file('app.ini');
$API_KEY = $app['api-key'];
$SEARCH_LIMIT = 10;
$API_HOST = "https://api.yelp.com";
$SEARCH_PATH = "/v3/businesses/search";
$BUSINESS_PATH = "/v3/businesses/";

/** 
 * Makes a request to the Yelp API and returns the response
 * 
 * @param    $host    The domain host of the API 
 * @param    $path    The path of the API after the domain.
 * @param    $url_params    Array of query-string parameters.
 * @return   The JSON response from the request      
 */
function request($host, $path, $url_params = array()) {
    // Send Yelp API Call
    try {
        $curl = curl_init();
        if (FALSE === $curl)
            throw new Exception('Failed to initialize');

        $url = $host . $path . "?" . http_build_query($url_params);
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,  // Capture response.
            CURLOPT_ENCODING => "",  // Accept gzip/deflate/whatever.
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "authorization: Bearer " . $GLOBALS['API_KEY'],
                "cache-control: no-cache",
            ),
        ));

        $response = curl_exec($curl);

        if (FALSE === $response)
            throw new Exception(curl_error($curl), curl_errno($curl));
        $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if (200 != $http_status)
            throw new Exception($response, $http_status);

        curl_close($curl);
    } catch(Exception $e) {
        trigger_error(sprintf(
            'Curl failed with error #%d: %s',
            $e->getCode(), $e->getMessage()),
            E_USER_ERROR);
    }

    return $response;
}

/**
 * Query the Search API by a search term and location 
 * 
 * @param    $term        The search term passed to the API 
 * @param    $location    The search location passed to the API 
 * @return   The JSON response from the request 
 */
function search($term, $location) {
    $url_params = array();
    
    $url_params['term'] = $term . ' ' . 'restaurant';
    $url_params['location'] = $location;
    $url_params['limit'] = $GLOBALS['SEARCH_LIMIT'];
    
    return request($GLOBALS['API_HOST'], $GLOBALS['SEARCH_PATH'], $url_params);
}

/**
 * Query the Business Review API by business_id
 * 
 * @param    $business_id    The ID of the business to query
 * @return   The JSON response from the request 
 */
function get_top_review($business_id) {
    $business_path = $GLOBALS['BUSINESS_PATH'] . urlencode($business_id) . '/reviews';
    
    return json_decode(request($GLOBALS['API_HOST'], $business_path),true);
}

/**
 * Queries the API by the input values from the user 
 * 
 * @param    $term        The search term to query
 * @param    $location    The location of the business to query
 */
function query_api($term, $location) {     
    $response = json_decode(search($term, $location),true);
    $businesses = $response['businesses'];
    echo cardBuilder($businesses);
}

// Function to dynamically build a table
function cardBuilder($data_array) {
    $card_info = "<h1>Results</h1>";
        foreach ($data_array as $key) {
           $card_info .= '<div class="card mb-3 shadow border-dark"><div class = "row no-gutters"><div class="col-md-4">';

           if (isset($key['image_url'])) {
            $card_info .= '<img src="' . $key['image_url'] .'" class="card-img"/>';
           }
           $card_info .= '</div><div class = "col-md-8"><div class="card-body">';

           if (isset($key['name'])) {
            $card_info .=   '<div class="card-title"><h4><a href ="' . $key['url'] . '" target = "_blank">'. $key['name'].'</a></h4>';  
           }

           if (isset($key['location']['address1'])) {
            $card_info .= '<h5 class="card-subtitle text-muted mb-3">' . $key['location']['address1'] . '<br/>' . $key['location']['city'] . ', OH ' . $key['location']['zip_code'];
            $card_info .=  '</h5>';
        }
            $card_info .= '<h5 class="mb-3">' . $key['categories'][0]['title'] . '</h5>';
            $card_info .= '<p class = "card-text">"' . get_top_review($key['id'])['reviews'][0]['text'] . '"</p>';
            $card_info .= '</div></div></div></div></div>';
            
    }

  return $card_info;
}

// Function to help sanitize data
function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}
