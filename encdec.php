<?php

function encrypt_id($id) {
    $secret_key = 'my_simple_secret_key_123'; // change this to something random and long
    $secret_iv = 'my_simple_iv_456';          // also change this for better randomness

    $key = hash('sha256', $secret_key); // 256-bit key
    $iv = substr(hash('sha256', $secret_iv), 0, 16); // 128-bit IV (for AES-256-CBC)

    $encrypted = openssl_encrypt($id, 'AES-256-CBC', $key, 0, $iv);
    return urlencode(base64_encode($encrypted)); // safe for URL
}

function decrypt_id($encrypted_id) {
    $secret_key = 'my_simple_secret_key_123';
    $secret_iv = 'my_simple_iv_456';

    $key = hash('sha256', $secret_key);
    $iv = substr(hash('sha256', $secret_iv), 0, 16);

    $decrypted = openssl_decrypt(base64_decode(urldecode($encrypted_id)), 'AES-256-CBC', $key, 0, $iv);
    return $decrypted;
}