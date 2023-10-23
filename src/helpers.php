<?php

if ( !function_exists('uuid') ) {
    /**
     * @param string|null $seed
     * @return string
     * @throws Exception
     */
    function uuid(?string $seed = null): string
    {
        if ($seed && ($len = strlen($seed)) != 16) {
            throw new Exception("Seed must contain 16 bytes, $len given.");
        }

        $seed = $seed ?: random_bytes(16);

        $seed[6] = chr(ord($seed[6]) & 0x0f | 0x40);
        $seed[8] = chr(ord($seed[8]) & 0x3f | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($seed), 4));
    }
}