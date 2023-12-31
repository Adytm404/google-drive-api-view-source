<?php

function encrypt($data, $key, $iv) {
    $cipherMethod = 'aes-256-cbc';
    $options = 0;
    $encryptedData = openssl_encrypt($data, $cipherMethod, $key, $options, $iv);

    return base64_encode($encryptedData);
}

function decrypt($encryptedData, $key, $iv) {
    $cipherMethod = 'aes-256-cbc';
    $options = 0;
    $decryptedData = openssl_decrypt(base64_decode($encryptedData), $cipherMethod, $key, $options, $iv);

    return $decryptedData;
}

$key = 'maklo'; 
$iv = openssl_random_pseudo_bytes(16); 

$dataToEncrypt = '16Mpk9QD-UgCt4p-HuDo1CexsNB2NDztu';
$encryptedData = encrypt($dataToEncrypt, $key, $iv);

echo 'Original Data: ' . $dataToEncrypt . PHP_EOL;
echo 'Encrypted Data: ' . $encryptedData . PHP_EOL;

$decryptedData = decrypt($encryptedData, $key, $iv);
echo 'Decrypted Data: ' . $decryptedData . PHP_EOL;

?>
