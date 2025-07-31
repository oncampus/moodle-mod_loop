<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Debug script to check what Moodalis API returns
 * @package mod_loop
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

$jsonresult = curl_exec($ch);

if (curl_errno($ch)) {
    echo "cURL Error: " . curl_error($ch) . "\n";
} else {
    echo "HTTP Status: " . curl_getinfo($ch, CURLINFO_HTTP_CODE) . "\n";
    echo "Response Length: " . strlen($jsonresult) . " bytes\n\n";

    echo "Raw Response:\n";
    echo $jsonresult . "\n\n";

    $loops = json_decode($jsonresult, true);

    if ($loops === null) {
        echo "JSON Decode Error: " . json_last_error_msg() . "\n";
    } else {
        echo "Decoded Loops:\n";
        echo "Number of loops: " . count($loops) . "\n";
        print_r($loops);
    }
}

curl_close($ch);
