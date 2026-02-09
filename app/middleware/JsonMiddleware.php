<?php
class JsonMiddleware {
    /**
     * Purpose: Ensure incoming requests are valid JSON and parse them
     */
    public function handle() {
        // 1. Set response header as application/json for all responses
        header("Content-Type: application/json; charset=UTF-8");

        $method = $_SERVER['REQUEST_METHOD'];

        // 2. POST, PUT, PATCH methods-ku mattum JSON check pannanum 
        if (in_array($method, ['POST', 'PUT', 'PATCH'])) {
            
            // Content-Type application/json-nu check pannanum 
            $contentType = $_SERVER["CONTENT_TYPE"] ?? '';
            if (stripos($contentType, 'application/json') === false) {
                http_response_code(400);
                echo json_encode(["message" => "Content-Type must be application/json"]);
                exit();
            }

            // 3. Read raw input from php://input
            $json = file_get_contents('php://input');

            // 4. Check if body is empty for required methods 
            if (empty($json)) {
                http_response_code(400);
                echo json_encode(["message" => "Empty JSON payload"]);
                exit();
            }

            // 5. Decode JSON safely
            $data = json_decode($json, true);

            // 6. Block if JSON is invalid 
            if (json_last_error() !== JSON_ERROR_NONE) {
                http_response_code(400);
                echo json_encode(["message" => "Invalid JSON: " . json_last_error_msg()]);
                exit();
            }

            // 7. Attach decoded data to a global request variable so controllers can use it 
            $_POST['body'] = $data;
        }
    }
}