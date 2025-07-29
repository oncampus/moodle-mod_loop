<?php
/**
 * Debug script to check what Moodalis API returns
 */

require_once('../../config.php');

$token = get_config('mod_loop', 'token');

echo "Token: " . $token . "\n";
echo "API URL: https://moodalis.oncampus.de/files/lms_loops.php?token=" . $token . "\n\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, ('https://moodalis.oncampus.de/files/lms_loops.php?token=' . $token));
curl_setopt($ch, CURLOPT_ENCODING, "UTF-8");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$json_result = curl_exec($ch);

if (curl_errno($ch)) {
    echo "cURL Error: " . curl_error($ch) . "\n";
} else {
    echo "HTTP Status: " . curl_getinfo($ch, CURLINFO_HTTP_CODE) . "\n";
    echo "Response Length: " . strlen($json_result) . " bytes\n\n";

    echo "Raw Response:\n";
    echo $json_result . "\n\n";

    $loops = json_decode($json_result, true);

    if ($loops === null) {
        echo "JSON Decode Error: " . json_last_error_msg() . "\n";
    } else {
        echo "Decoded Loops:\n";
        echo "Number of loops: " . count($loops) . "\n";
        print_r($loops);
    }
}

curl_close($ch);
