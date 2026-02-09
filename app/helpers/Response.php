<?php
class Response {
    /**
     * Send a JSON response to the client.
     * * @param int $status HTTP status code (e.g., 200, 201, 401, 404)
     * @param mixed $data The data or message to return
     */
    public static function json($status, $data) {
        // Set response header to application/json 
        header("Content-Type: application/json; charset=UTF-8");
        
        // Set the HTTP status code
        http_response_code($status);

        // If data is an array, encode it; otherwise, wrap it in a message key
        if (is_array($data)) {
            echo json_encode($data);
        } else {
            echo json_encode(["message" => $data]);
        }
        
        // Terminate script execution after sending response
        exit();
    }
}