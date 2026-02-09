<?php
class AuthMiddleware {
    /**
     * Intha static variable thaan database query-ku email-ai anuppum
     */
    public static $currentUserEmail;

    public function handle() {
        $headers = getallheaders(); 
        
        if (!isset($headers['Authorization'])) {
            Response::json(401, "Unauthorized: No token provided");
            exit();
        }

        $token = str_replace('Bearer ', '', $headers['Authorization']);
        
        // JWT logic verification
        $userData = JWT::verify($token);

        if (!$userData) {
            Response::json(401, "Unauthorized: Invalid or expired token");
            exit();
        }

        /**
         * Debugged: JWT verify object-a thirumba thunthaalum array-a thunthaalum handle pannum
         */
        if (is_array($userData)) {
            self::$currentUserEmail = $userData['email'] ?? null;
        } elseif (is_object($userData)) {
            self::$currentUserEmail = $userData->email ?? null;
        }

        // Email extract aagalaina process-ai ingaye nippatum
        if (empty(self::$currentUserEmail)) {
            Response::json(401, "Unauthorized: Token payload missing email context");
            exit();
        }
    }
}