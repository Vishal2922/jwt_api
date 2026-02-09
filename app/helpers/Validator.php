<?php
class Validator {
    // Gmail Validator
    public static function email($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) && str_contains($email, '@gmail.com');
    }

    // Phone: Only 0-9 and exactly 10 digits
    public static function phone($phone) {
        return preg_match('/^[0-9]{10}$/', $phone);
    }

    // Password: Aa@1 format and length >= 8
    public static function password($password) {
        $hasUpper = preg_match('@[A-Z]@', $password);
        $hasLower = preg_match('@[a-z]@', $password);
        $hasNumber = preg_match('@[0-9]@', $password);
        $hasSpecial = preg_match('@[^\w]@', $password);
        return $hasUpper && $hasLower && $hasNumber && $hasSpecial && strlen($password) >= 8;
    }
}