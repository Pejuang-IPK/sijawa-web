<?php
function uploadToMinioNative($bucket, $filename, $filePath, $contentType) {
    $accessKey = 'admin';
    $secretKey = 'admin123';
    $region = 'us-east-1'; // Default MinIO
    $host = 'minio';       // Nama service di docker, atau 'localhost' jika di XAMPP
    $port = '9000';
    $service = 's3';
    
    $endpoint = "http://{$host}:{$port}"; 
    
    $httpMethod = 'PUT';
    $uri = '/' . $bucket . '/' . $filename;
    $timestamp = time();
    $dateLong = gmdate('Ymd\THis\Z', $timestamp);
    $dateShort = gmdate('Ymd', $timestamp);
    $content = file_get_contents($filePath);
    $payloadHash = hash('sha256', $content);

    $headers = [
        'host' => "$host:$port",
        'x-amz-content-sha256' => $payloadHash,
        'x-amz-date' => $dateLong,
    ];

    $canonicalHeaders = '';
    $signedHeaders = '';
    foreach ($headers as $key => $value) {
        $canonicalHeaders .= $key . ':' . $value . "\n";
        $signedHeaders .= $key . ';';
    }
    $signedHeaders = rtrim($signedHeaders, ';');

    $canonicalRequest = "$httpMethod\n$uri\n\n$canonicalHeaders\n$signedHeaders\n$payloadHash";

    $algorithm = 'AWS4-HMAC-SHA256';
    $credentialScope = "$dateShort/$region/$service/aws4_request";
    $stringToSign = "$algorithm\n$dateLong\n$credentialScope\n" . hash('sha256', $canonicalRequest);

    $kSecret = 'AWS4' . $secretKey;
    $kDate = hash_hmac('sha256', $dateShort, $kSecret, true);
    $kRegion = hash_hmac('sha256', $region, $kDate, true);
    $kService = hash_hmac('sha256', $service, $kRegion, true);
    $kSigning = hash_hmac('sha256', 'aws4_request', $kService, true);
    
    $signature = hash_hmac('sha256', $stringToSign, $kSigning);

    $authorization = "$algorithm Credential=$accessKey/$credentialScope, SignedHeaders=$signedHeaders, Signature=$signature";

    $url = $endpoint . $uri;
    $ch = curl_init($url);
    
    $requestHeaders = [
        "Authorization: $authorization",
        "x-amz-date: $dateLong",
        "x-amz-content-sha256: $payloadHash",
        "Content-Type: $contentType",
        "Content-Length: " . strlen($content)
    ];

    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $requestHeaders);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if (curl_errno($ch)) {
        return ['success' => false, 'message' => 'cURL Error: ' . curl_error($ch)];
    }
    
    curl_close($ch);

    if ($httpCode == 200) {
        return ['success' => true];
    } else {
        return ['success' => false, 'message' => "MinIO Error Code: $httpCode. Response: $response"];
    }
}
?>