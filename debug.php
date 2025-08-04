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
 * Advanced debugging page for Loop module
 *
 * @package    mod_loop
 * @author     Marc Vorreiter <marc.vorreiter@oncampus.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->dirroot . '/mod/loop/locallib.php');

// Security check - only allow access for administrators and debugging
require_login();
$context = context_system::instance();
require_capability('moodle/site:config', $context);

// Check if debugging is enabled
if (!debugging('', DEBUG_DEVELOPER)) {
    throw new moodle_exception('debuggingnotenabled', 'error');
}

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/mod/loop/debug.php'));
$PAGE->set_title(get_string('debugging', 'admin') . ' - Loop Module');
$PAGE->set_heading(get_string('debugging', 'admin') . ' - Loop Module');
$PAGE->set_pagelayout('admin');

// Initialize output
$output = $PAGE->get_renderer('core');
echo $output->header();

// Debug information container
echo html_writer::start_div('debug-container', ['id' => 'debug-container']);

// Navigation tabs
echo html_writer::start_div('debug-tabs');
$tabs = [
    'api' => 'API Testing',
    'config' => 'Configuration',
    'database' => 'Database',
    'systems' => 'Loop Systems',
    'logs' => 'Logs',
];

foreach ($tabs as $tab => $label) {
    $active = ($tab === 'api') ? 'active' : '';
    echo html_writer::tag('button', $label, [
        'class' => 'debug-tab ' . $active,
        'data-tab' => $tab,
        'onclick' => 'switchTab("' . $tab . '")',
    ]);
}
echo html_writer::end_div();

// API Testing Tab
echo html_writer::start_div('debug-content', ['id' => 'api-content']);
echo html_writer::tag('h3', 'API Testing', ['class' => 'debug-section-title']);

// API Test Form
echo html_writer::start_tag('form', ['method' => 'POST', 'id' => 'api-test-form']);
echo html_writer::tag('input', '', [
    'type' => 'hidden',
    'name' => 'sesskey',
    'value' => sesskey(),
]);

echo html_writer::start_div('form-group');
echo html_writer::label('Test Type:', 'test-type');
echo html_writer::select([
    'moodalis' => 'Moodalis API',
    'structure' => 'Loop Structure API',
    'themes' => 'Loop Themes API',
], 'test_type', 'moodalis', false, ['id' => 'test-type']);
echo html_writer::end_div();

echo html_writer::start_div('form-group');
echo html_writer::label('Loop URL (for structure/themes):', 'loop-url');
echo html_writer::tag('input', '', [
    'type' => 'text',
    'name' => 'loop_url',
    'id' => 'loop-url',
    'placeholder' => 'e.g., moodalis.oncampus.de',
]);
echo html_writer::end_div();

echo html_writer::tag('button', 'Run Test', [
    'type' => 'submit',
    'class' => 'btn btn-primary',
    'name' => 'run_test',
]);
echo html_writer::end_tag('form');

// API Test Results
if (optional_param('run_test', false, PARAM_BOOL) && confirm_sesskey()) {
    echo html_writer::tag('h4', 'Test Results', ['class' => 'debug-results-title']);

    $testtype = optional_param('test_type', 'moodalis', PARAM_TEXT);
    $loopurl = optional_param('loop_url', '', PARAM_TEXT);

    echo html_writer::start_div('debug-results');

    switch ($testtype) {
        case 'moodalis':
            testMoodalisAPI();
            break;
        case 'structure':
            testStructureAPI($loopurl);
            break;
        case 'themes':
            testThemesAPI($loopurl);
            break;
    }

    echo html_writer::end_div();
}

echo html_writer::end_div();

// Configuration Tab
echo html_writer::start_div('debug-content', ['id' => 'config-content', 'style' => 'display: none;']);
echo html_writer::tag('h3', 'Configuration', ['class' => 'debug-section-title']);

// Plugin Configuration
echo html_writer::tag('h4', 'Plugin Configuration', ['class' => 'debug-subtitle']);
$token = get_config('mod_loop', 'token');
echo html_writer::start_div('config-item');
echo html_writer::tag('strong', 'API Token: ');
echo html_writer::tag('span', $token ? substr($token, 0, 10) . '...' : 'Not set', [
    'class' => $token ? 'config-value' : 'config-error',
]);
echo html_writer::end_div();

// Moodle Configuration
echo html_writer::tag('h4', 'Moodle Configuration', ['class' => 'debug-subtitle']);
echo html_writer::start_div('config-item');
echo html_writer::tag('strong', 'Site URL: ');
echo html_writer::tag('span', $CFG->wwwroot, ['class' => 'config-value']);
echo html_writer::end_div();

echo html_writer::start_div('config-item');
echo html_writer::tag('strong', 'Debug Level: ');
echo html_writer::tag('span', debugging('', DEBUG_DEVELOPER) ? 'Developer' : 'Normal', ['class' => 'config-value']);
echo html_writer::end_div();

echo html_writer::end_div();

// Database Tab
echo html_writer::start_div('debug-content', ['id' => 'database-content', 'style' => 'display: none;']);
echo html_writer::tag('h3', 'Database', ['class' => 'debug-section-title']);

// Loop Systems
echo html_writer::tag('h4', 'Loop Systems', ['class' => 'debug-subtitle']);
$systems = $DB->get_records('loop_systems');
if ($systems) {
    echo html_writer::start_tag('table', ['class' => 'debug-table']);
    echo html_writer::start_tag('thead');
    echo html_writer::start_tag('tr');
    echo html_writer::tag('th', 'ID');
    echo html_writer::tag('th', 'Name');
    echo html_writer::tag('th', 'URL');
    echo html_writer::tag('th', 'External ID');
    echo html_writer::end_tag('tr');
    echo html_writer::end_tag('thead');
    echo html_writer::start_tag('tbody');

    foreach ($systems as $system) {
        echo html_writer::start_tag('tr');
        echo html_writer::tag('td', $system->id);
        echo html_writer::tag('td', $system->name);
        echo html_writer::tag('td', $system->url);
        echo html_writer::tag('td', $system->externalid);
        echo html_writer::end_tag('tr');
    }

    echo html_writer::end_tag('tbody');
    echo html_writer::end_tag('table');
} else {
    echo html_writer::tag('p', 'No loop systems found in database.', ['class' => 'debug-info']);
}

echo html_writer::end_div();

// Loop Systems Tab
echo html_writer::start_div('debug-content', ['id' => 'systems-content', 'style' => 'display: none;']);
echo html_writer::tag('h3', 'Loop Systems', ['class' => 'debug-section-title']);

// Refresh Systems Button
echo html_writer::start_tag('form', ['method' => 'POST']);
echo html_writer::tag('input', '', [
    'type' => 'hidden',
    'name' => 'sesskey',
    'value' => sesskey(),
]);
echo html_writer::tag('button', 'Refresh Loop Systems', [
    'type' => 'submit',
    'class' => 'btn btn-secondary',
    'name' => 'refresh_systems',
]);
echo html_writer::end_tag('form');

if (optional_param('refresh_systems', false, PARAM_BOOL) && confirm_sesskey()) {
    echo html_writer::tag('h4', 'Refresh Results', ['class' => 'debug-results-title']);
    echo html_writer::start_div('debug-results');

    try {
        $result = get_allowed_loops();
        echo html_writer::tag('p', 'Loop systems refreshed successfully.', ['class' => 'debug-success']);
    } catch (Exception $e) {
        echo html_writer::tag('p', 'Error refreshing loop systems: ' . $e->getMessage(), ['class' => 'debug-error']);
    }

    echo html_writer::end_div();
}

echo html_writer::end_div();

// Logs Tab
echo html_writer::start_div('debug-content', ['id' => 'logs-content', 'style' => 'display: none;']);
echo html_writer::tag('h3', 'Logs', ['class' => 'debug-section-title']);

// Recent Logs
echo html_writer::tag('h4', 'Recent Logs', ['class' => 'debug-subtitle']);
$logs = $DB->get_records_sql(
    "SELECT * FROM {log} WHERE module = 'loop' ORDER BY time DESC LIMIT 20"
);

if ($logs) {
    echo html_writer::start_tag('table', ['class' => 'debug-table']);
    echo html_writer::start_tag('thead');
    echo html_writer::start_tag('tr');
    echo html_writer::tag('th', 'Time');
    echo html_writer::tag('th', 'User');
    echo html_writer::tag('th', 'Action');
    echo html_writer::tag('th', 'Info');
    echo html_writer::end_tag('tr');
    echo html_writer::end_tag('thead');
    echo html_writer::start_tag('tbody');

    foreach ($logs as $log) {
        echo html_writer::start_tag('tr');
        echo html_writer::tag('td', date('Y-m-d H:i:s', $log->time));
        echo html_writer::tag('td', $log->userid);
        echo html_writer::tag('td', $log->action);
        echo html_writer::tag('td', $log->info);
        echo html_writer::end_tag('tr');
    }

    echo html_writer::end_tag('tbody');
    echo html_writer::end_tag('table');
} else {
    echo html_writer::tag('p', 'No recent logs found.', ['class' => 'debug-info']);
}

echo html_writer::end_div();

echo html_writer::end_div(); // debug-container

// JavaScript for tab switching
echo html_writer::script('
function switchTab(tabName) {
    // Hide all content
    const contents = document.querySelectorAll(".debug-content");
    contents.forEach(content => content.style.display = "none");

    // Remove active class from all tabs
    const tabs = document.querySelectorAll(".debug-tab");
    tabs.forEach(tab => tab.classList.remove("active"));

    // Show selected content and activate tab
    document.getElementById(tabName + "-content").style.display = "block";
    document.querySelector(\'[data-tab="\' + tabName + \'"]\').classList.add("active");
}
');

// CSS Styles
echo html_writer::tag('style', '
.debug-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.debug-tabs {
    display: flex;
    border-bottom: 2px solid #ddd;
    margin-bottom: 20px;
}

.debug-tab {
    padding: 10px 20px;
    border: none;
    background: #f5f5f5;
    cursor: pointer;
    margin-right: 5px;
    border-radius: 5px 5px 0 0;
}

.debug-tab.active {
    background: #007bff;
    color: white;
}

.debug-section-title {
    color: #333;
    border-bottom: 2px solid #007bff;
    padding-bottom: 10px;
}

.debug-subtitle {
    color: #666;
    margin-top: 20px;
}

.debug-results-title {
    color: #007bff;
    margin-top: 20px;
}

.debug-results {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 5px;
    padding: 15px;
    margin-top: 10px;
}

.debug-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}

.debug-table th,
.debug-table td {
    border: 1px solid #dee2e6;
    padding: 8px;
    text-align: left;
}

.debug-table th {
    background: #f8f9fa;
    font-weight: bold;
}

.debug-success {
    color: #28a745;
    font-weight: bold;
}

.debug-error {
    color: #dc3545;
    font-weight: bold;
}

.debug-info {
    color: #6c757d;
    font-style: italic;
}

.config-item {
    margin: 10px 0;
    padding: 5px 0;
}

.config-value {
    color: #28a745;
    font-family: monospace;
}

.config-error {
    color: #dc3545;
    font-weight: bold;
}

.form-group {
    margin: 15px 0;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}

.form-group input,
.form-group select {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    margin: 5px;
}

.btn-primary {
    background: #007bff;
    color: white;
}

.btn-secondary {
    background: #6c757d;
    color: white;
}
');

echo $output->footer();

/**
 * Test Moodalis API
 */
function testmoodalisapi(): void {
    global $CFG;

    $token = get_config('mod_loop', 'token');

    echo html_writer::start_div('api-test-section');
    echo html_writer::tag('h5', 'API Details', ['class' => 'debug-subtitle']);
    echo html_writer::tag('p', 'Token: ' . ($token ? substr($token, 0, 10) . '...' : 'Not set'));
    echo html_writer::tag('p', 'API URL: https://moodalis.oncampus.de/files/lms_loops.php?token=' . $token);

    if (!$token) {
        echo html_writer::tag('p', 'Error: No API token configured.', ['class' => 'debug-error']);
        return;
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://moodalis.oncampus.de/files/lms_loops.php?token=' . $token);
    curl_setopt($ch, CURLOPT_ENCODING, "UTF-8");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

    $jsonresult = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlerror = curl_error($ch);

    echo html_writer::tag('h5', 'Response Details', ['class' => 'debug-subtitle']);
    echo html_writer::tag('p', 'HTTP Status: ' . $httpcode);
    echo html_writer::tag('p', 'Response Length: ' . strlen($jsonresult) . ' bytes');

    if (curl_errno($ch)) {
        echo html_writer::tag('p', 'cURL Error: ' . $curlerror, ['class' => 'debug-error']);
    } else {
        echo html_writer::tag('h5', 'Raw Response', ['class' => 'debug-subtitle']);
        echo html_writer::tag('pre', htmlspecialchars($jsonresult), ['class' => 'debug-code']);

        $loops = json_decode($jsonresult, true);

        if ($loops === null) {
            echo html_writer::tag('p', 'JSON Decode Error: ' . json_last_error_msg(), ['class' => 'debug-error']);
        } else {
            echo html_writer::tag('h5', 'Parsed Data', ['class' => 'debug-subtitle']);
            echo html_writer::tag('p', 'Number of loops: ' . count($loops));
            echo html_writer::tag('pre', htmlspecialchars(print_r($loops, true)), ['class' => 'debug-code']);
        }
    }

    curl_close($ch);
    echo html_writer::end_div();
}

/**
 * Test Structure API
 */
function teststructureapi(string $url): void {
    if (empty($url)) {
        echo html_writer::tag('p', 'Error: No loop URL provided.', ['class' => 'debug-error']);
        return;
    }

    echo html_writer::start_div('api-test-section');
    echo html_writer::tag('h5', 'Testing Structure API for: ' . $url, ['class' => 'debug-subtitle']);

    try {
        $structure = get_loop_structure($url);

        if ($structure === false) {
            echo html_writer::tag('p', 'Error: Failed to retrieve structure.', ['class' => 'debug-error']);
        } else {
            echo html_writer::tag('h5', 'Structure Data', ['class' => 'debug-subtitle']);
            echo html_writer::tag('pre', htmlspecialchars($structure), ['class' => 'debug-code']);

            $decoded = json_decode($structure, true);
            if ($decoded) {
                echo html_writer::tag('p', 'Number of structure items: ' . count($decoded));
            }
        }
    } catch (Exception $e) {
        echo html_writer::tag('p', 'Exception: ' . $e->getMessage(), ['class' => 'debug-error']);
    }

    echo html_writer::end_div();
}

/**
 * Test Themes API
 */
function testthemesapi(string $url): void {
    if (empty($url)) {
        echo html_writer::tag('p', 'Error: No loop URL provided.', ['class' => 'debug-error']);
        return;
    }

    echo html_writer::start_div('api-test-section');
    echo html_writer::tag('h5', 'Testing Themes API for: ' . $url, ['class' => 'debug-subtitle']);

    try {
        $themes = get_loop_themes($url);

        if ($themes === false) {
            echo html_writer::tag('p', 'Error: Failed to retrieve themes.', ['class' => 'debug-error']);
        } else {
            echo html_writer::tag('h5', 'Themes Data', ['class' => 'debug-subtitle']);
            echo html_writer::tag('pre', htmlspecialchars($themes), ['class' => 'debug-code']);

            $decoded = json_decode($themes, true);
            if ($decoded) {
                echo html_writer::tag('p', 'Number of themes: ' . count($decoded));
            }
        }
    } catch (Exception $e) {
        echo html_writer::tag('p', 'Exception: ' . $e->getMessage(), ['class' => 'debug-error']);
    }

    echo html_writer::end_div();
}
