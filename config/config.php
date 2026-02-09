<?php
/**
 * Global Configuration File
 * Inga thaan database matum JWT settings-ai define pannuvom
 */

// 1. Database Configuration
define('DB_HOST', 'localhost');     
define('DB_NAME', 'jwt_api');        
define('DB_USER', 'root');          // Unga MySQL username
define('DB_PASS', '');              // Unga MySQL password

// 2. JWT Configuration
// Intha SECRET_KEY-ai safe-aa vachukonga, token encrypt panna ethu thaan mukkiyam
define('JWT_SECRET', 'your_super_secret_key_12345'); 
define('JWT_EXPIRY', 3600); // Token expiry (1 hour)

// 3. App Settings
define('BASE_URL', 'http://localhost/jwt_api');