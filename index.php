<?php

function getOriginalUrl($url) {
    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_HEADER, true);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        throw new Exception('Curl error: ' . curl_error($ch));
    }

    curl_close($ch);

    $headers = explode("\n", $response);
    foreach ($headers as $header) {
        if (stripos($header, 'Location:') !== false) {
            $originalUrl = trim(str_ireplace('Location:', '', $header));
            return $originalUrl;
        }
    }
    return $url;
}

$fileId = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_STRING);
$apikey = "AIzaSyAXaH9kS_v23i8KiXAJgt1G0JXlO88UMOk";
$url = "https://drive.google.com/uc?id=$fileId&export=download";
$originalUrl = getOriginalUrl($url);

try {
    error_reporting(0);

    $jsonData = file_get_contents("https://content.googleapis.com/drive/v2/files/$fileId?acknowledgeAbuse=true&projection=FULL&supportsAllDrives=true&supportsTeamDrives=true&updateViewedDate=true&access_token=AIzaSyAXaH9kS_v23i8KiXAJgt1G0JXlO88UMOk&alt=json&key=$apikey");

    if ($jsonData === false) {
        throw new Exception('Error getting file information from Google Drive API');
    }

    $fileInfo = json_decode($jsonData, true);

    if (json_last_error() != JSON_ERROR_NONE) {
        throw new Exception('Error decoding JSON data');
    }

    $lastModifyingUserName = $fileInfo['lastModifyingUserName'];
    $thumbnailLink = $fileInfo['thumbnailLink'];
    $fileSize = $fileInfo['fileSize'];
    $originalFilename = $fileInfo['originalFilename'];
    $durationMillis = $fileInfo['videoMediaMetadata']['durationMillis'];

    $output = [
        'lastModifyingUserName' => $lastModifyingUserName,
        'thumbnailLink' => $thumbnailLink,
        'fileSize' => $fileSize,
        'originalFilename' => $originalFilename,
        'durationMillis' => $durationMillis,
        'originalUrl' => $originalUrl,
    ];

    echo json_encode($output);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

?>
