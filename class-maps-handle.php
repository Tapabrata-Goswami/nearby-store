<?php



// API call to fetch nearby installers from an external service
function nl_get_nearby_installers(WP_REST_Request $request) {
    // Get data from POST or GET request (if any)
    $heat_pump = $request->get_param('heat_pump') ?? false; // Default to false if not provided
    $city = $request->get_param('city') ?? esc_attr(get_option('store_name'));  // Default to Warsaw if not provided

    // API URL
    $url = "https://europe-west3-strefa-gree.cloudfunctions.net/utils/findNearbyInstallers";

    // Prepare the request headers
    $headers = [
        "Content-Type: application/json",
        "Access-Control-Allow-Origin: *",
        "Access-Control-Allow-Methods: GET, POST"
    ];

    // Prepare the request body
    $body = json_encode([
        "heat_pump" => $heat_pump,  // true for heat pumps, false for air conditioners
        "city" => $city             // City provided in the request
    ]);

    // Initialize cURL
    $ch = curl_init();

    // Set cURL options
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Execute the request and capture the response
    $response = curl_exec($ch);

    // Check for cURL errors
    if (curl_errno($ch)) {
        return new WP_Error('curl_error', curl_error($ch), array('status' => 500));
    }

    // Get HTTP status code
    $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // If successful, return the data
    if ($httpStatusCode >= 200 && $httpStatusCode < 300) {
        $data = json_decode($response, true);
        return $data;
    } else {
        return new WP_Error('api_error', 'API request failed', array('status' => $httpStatusCode));
    }
}

?>
