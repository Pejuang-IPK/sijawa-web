<?php
// Fungsi helper untuk upload ke MinIO tanpa library (Native cURL)
function uploadToMinioNative($bucket, $filename, $filePath, $contentType) {
    // 1. Konfigurasi (Sesuaikan dengan docker-compose)
    $accessKey = 'admin';
    $secretKey = 'admin123';
    $region = 'us-east-1'; // Default MinIO
    $host = 'minio';       // Nama service di docker, atau 'localhost' jika di XAMPP
    $port = '9000';
    $service = 's3';
    
    // URL Endpoint
    // Jika jalan di dalam docker container yang satu network, gunakan nama service
    $endpoint = "http://{$host}:{$port}"; 
    
    // 2. Persiapan Data
    $httpMethod = 'PUT';
    $uri = '/' . $bucket . '/' . $filename;
    $timestamp = time();
    $dateLong = gmdate('Ymd\THis\Z', $timestamp);
    $dateShort = gmdate('Ymd', $timestamp);
    $content = file_get_contents($filePath);
    $payloadHash = hash('sha256', $content);

    // 3. Persiapan Header Canonical
    // Header harus diurutkan secara alfabetis (lowercase)
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

    // 4. Buat Canonical Request
    $canonicalRequest = "$httpMethod\n$uri\n\n$canonicalHeaders\n$signedHeaders\n$payloadHash";

    // 5. Buat String to Sign
    $algorithm = 'AWS4-HMAC-SHA256';
    $credentialScope = "$dateShort/$region/$service/aws4_request";
    $stringToSign = "$algorithm\n$dateLong\n$credentialScope\n" . hash('sha256', $canonicalRequest);

    // 6. Hitung Signature Key (HMAC Recursion)
    $kSecret = 'AWS4' . $secretKey;
    $kDate = hash_hmac('sha256', $dateShort, $kSecret, true);
    $kRegion = hash_hmac('sha256', $region, $kDate, true);
    $kService = hash_hmac('sha256', $service, $kRegion, true);
    $kSigning = hash_hmac('sha256', 'aws4_request', $kService, true);
    
    // 7. Hitung Final Signature
    $signature = hash_hmac('sha256', $stringToSign, $kSigning);

    // 8. Buat Header Authorization
    $authorization = "$algorithm Credential=$accessKey/$credentialScope, SignedHeaders=$signedHeaders, Signature=$signature";

    // 9. Eksekusi cURL
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
    curl_setopt($ch, CURLOPT_HEADER, true); // Untuk debug response header

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    // Error handling cURL dasar
    if (curl_errno($ch)) {
        return ['success' => false, 'message' => 'cURL Error: ' . curl_error($ch)];
    }
    
    curl_close($ch);

    // Cek HTTP Code (200 OK)
    if ($httpCode == 200) {
        return ['success' => true];
    } else {
        return ['success' => false, 'message' => "MinIO Error Code: $httpCode. Response: $response"];
    }
}
?>