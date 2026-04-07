<?php
class TokenGenerator {
    public static function generate($length = 32) {
        return bin2hex(random_bytes($length));
    }

    public static function generateExpiryTime($hours = 24) {
        return date('Y-m-d H:i:s', strtotime("+$hours hours"));
    }
}
