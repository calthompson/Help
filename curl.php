<?php
$app = parse_ini_file('app.ini');
$API_KEY = $app['api-key'];
$SEARCH_LIMIT = 50;
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
 * Query the Business API by business_id
 * 
 * @param    $business_id    The ID of the business to query
 * @return   The JSON response from the request 
 */
function get_business($business_id) {
    $business_path = $GLOBALS['BUSINESS_PATH'] . urlencode($business_id);
    
    return request($GLOBALS['API_HOST'], $business_path);
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
    echo tableBuilder($businesses);
}

// Function to dynamically build a table
function tableBuilder($data_array) {

        // Header Row
        $data_table = '<thead class="thead-dark">';
        $data_table .= '<tr>';
        $data_table .= '<th>Name</th>';
        $data_table .= '<th>Address</th>';
        $data_table .= '<th>Phone Number</th>';
        $data_table .= '<th>Price</th>';
        $data_table .= '</tr>';
        $data_table .= '<tbody>';

        foreach ($data_array as $key) {
            $data_table .= '<tr>';
                $data_table .= '<td><a href ="' . $key['url'] . '" target = "_blank">';
                if (isset($key['name']))
                    $data_table .= $key['name'];
                $data_table .= '</a></td>';

                $data_table .= '<td>';           
                if (isset($key['location']['address1']))
                    $data_table .= $key['location']['address1'] . '<br/>' . $key['location']['city'] . ', OH ' . $key['location']['zip_code'];
                $data_table .= '</td>';

                $data_table .= '<td>';
                if (isset($key['display_phone']))
                    $data_table .= $key['display_phone'];
                $data_table .= '</td>';

                $data_table .= '<td>';
                if (isset($key['price']))
                    $data_table .= $key['price'];
                $data_table .= '</td>';

                
            $data_table .= '</tr>';
    }

        $data_table .= '</tbody>';
  return $data_table;
}

// Function to help sanitize data
function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}
