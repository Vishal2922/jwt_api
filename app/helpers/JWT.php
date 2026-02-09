<?php
class JWT {
    private static $secret = "your_super_secret_key"; // Load from .env later 

    public static function generate($data) {
        $header = base64_encode(json_encode(['alg' => 'HS256', 'typ' => 'JWT'])); 
        $payload = base64_encode(json_encode(array_merge($data, [
            'iat' => time(), 
            'exp' => time() + 3600 
        ])));

        $signature = hash_hmac('sha256', "$header.$payload", self::$secret, true);
        $signature = base64_encode($signature);

        return "$header.$payload.$signature";
    }

    public static function verify($token) {
        $parts = explode('.', $token); 
        if (count($parts) != 3) return false;

        list($header, $payload, $signature) = $parts;
        $validSig = base64_encode(hash_hmac('sha256', "$header.$payload", self::$secret, true)); 

        if ($signature !== $validSig) return false; 

        $data = json_decode(base64_decode($payload), true);
        if ($data['exp'] < time()) return false; // Token expired 

        return $data; 
    }
}