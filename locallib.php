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
 * @package mod_loop
 * @author  Marc Vorreiter <marc.vorreiter@oncampus.de>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


function get_allowed_loops() {
    global $DB;

    mtrace("Getting list of allowed LOOPs from Moodalis");

    $token = get_config('mod_loop', 'token');

    $cha = curl_init();
    curl_setopt($cha, CURLOPT_URL, ('https://moodalis.oncampus.de/files/lms_loops.php?token=' . $token));
    curl_setopt($cha, CURLOPT_ENCODING, "UTF-8");
    curl_setopt($cha, CURLOPT_RETURNTRANSFER, true);
    $jsonresult = curl_exec($cha);

    if (!$jsonresult) {
        throw new Exception("Error getting data from server: " . curl_error($cha));
    }

    $loops = json_decode($jsonresult, true);
    // mtrace (print_r($loops,true));

    curl_close($cha);

    $processedloops = [];

    if ($loops) {
        foreach ($loops as $loop => $loopdata) {
            mtrace("Checking LOOP " . $loop);
            unset($loopsystem);

            if ($DB->record_exists('loop_systems', ['externalid' => $loop])) {
                // update
                mtrace("LOOP already exisits");


                $loopsystem = $DB->get_record('loop_systems', ['externalid' => $loop]);
                $processedloops[] = $loopsystem->id;

                if (
                    ($loopsystem->name != $loopdata['name']) ||
                    ($loopsystem->url != $loopdata['url']) ||
                    ($loopsystem->personalized_access != $loopdata['personalized_access']) ||
                    ($loopsystem->student_role_allocation != $loopdata['student_role_allocation']) ||
                    ($loopsystem->teacher_role_allocation != $loopdata['teacher_role_allocation']) ||
                    ($loopsystem->allowed_themes != json_encode($loopdata['allowed_themes']))
                ) {
                    $updateloopsystem = new stdClass();
                    $updateloopsystem->id = $loopsystem->id;
                    $updateloopsystem->externalid = $loop;
                    $updateloopsystem->name = $loopdata['name'];
                    $updateloopsystem->url = $loopdata['url'];
                    $updateloopsystem->personalized_access = $loopdata['personalized_access'];
                    $updateloopsystem->student_role_allocation = $loopdata['student_role_allocation'];
                    $updateloopsystem->teacher_role_allocation = $loopdata['teacher_role_allocation'];
                    $updateloopsystem->allowed_themes = json_encode($loopdata['allowed_themes']);
                    $updateloopsystem->timemodified = time();
                    $DB->update_record('loop_systems', $updateloopsystem);

                    mtrace("LOOP updated");
                } else {
                    mtrace("no update neccessary");
                }
            } else {
                mtrace("New LOOP");
                // insert
                $newloopsystem = new stdClass();
                $newloopsystem->externalid = $loop;
                $newloopsystem->name = $loopdata['name'];
                $newloopsystem->url = $loopdata['url'];
                $newloopsystem->personalized_access = $loopdata['personalized_access'];
                $newloopsystem->student_role_allocation = $loopdata['student_role_allocation'];
                $newloopsystem->teacher_role_allocation = $loopdata['teacher_role_allocation'];
                $newloopsystem->allowed_themes = json_encode($loopdata['allowed_themes']);
                $newloopsystem->timemodified = time();
                $newloopsystem->timecreated = time();
                $newid = $DB->insert_record('loop_systems', $newloopsystem, true);
                $processedloops[] = $newid;
                mtrace("inserted into Database: " . $newid);
            }
        }


        // delete all not processed loops
        if (!empty($processedloops)) {
            $placeholders = str_repeat('?,', count($processedloops) - 1) . '?';
            $sql = "SELECT * FROM {loop_systems} WHERE id NOT IN ($placeholders)";
            $todelete = $DB->get_records_sql($sql, $processedloops);
        } else {
            $todelete = [];
        }

        if (is_array($todelete)) {
            foreach ($todelete as $delloop) {
                mtrace("delete LOOP with id: " . $delloop->id);
                $DB->delete_records('loop_systems', ['id' => $delloop->id]);
            }
        }
    }


    return true;
}



function get_loop_structure($url) {
    global $CFG, $DB;



    if (!$loop = $DB->get_record_sql("select * from {loop_systems} where " . $DB->sql_compare_text('url') . " = '" . $url . "'")) {
        return false;
    }



    $wgemoodlelooptoken = get_config('mod_loop', 'token');

    $moodleurl = str_replace('https://', '', $CFG->wwwroot);

    $loopstructureurl = 'https://' . $url . '/mediawiki/api.php?action=loopauth-structure&auth=api&m=' . $moodleurl . '&t=' . $wgemoodlelooptoken . '&format=json';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $loopstructureurl);
    curl_setopt($ch, CURLOPT_FAILONERROR, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    // curl_setopt($ch, CURLOPT_TIMEOUT, intval($timeout));
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    $result = curl_exec($ch);


    if (curl_errno($ch)) {
        return false;
    }

    curl_close($ch);

    $structureresult = json_decode($result, true);
    $structure = $structureresult['loopauth-structure']['structure'];


    $return = [];
    $return['---'] = '---';
    if (isset($structure) && is_array($structure)) {
        foreach ($structure as $structureitem) {
            $key = urlencode($structureitem['toctext']);
            $level = $structureitem['toclevel'];
            $number = $structureitem['tocnumber'];
            if ($number != 0) {
                $value = str_pad('', $level - 1, " ") . $number . ' ' . $structureitem['toctext'];
                $return[$key] = $value;
            }
        }
    }

    $returnarray = [];
    $n = 1;
    foreach ($return as $key => $value) {
        $returnarray[] = ['key' => $key, 'value' => $value];
        $n++;
    }



    return json_encode($returnarray);
}

function get_loop_themes($url) {
    global $CFG, $DB;

    if (!$loop = $DB->get_record_sql("select * from {loop_systems} where " . $DB->sql_compare_text('url') . " = '" . $url . "'")) {
        return false;
    }
    $allowedthemes = json_decode($loop->allowed_themes);

    $returnarray = [];

    if (isset($allowedthemes) && is_array($allowedthemes)) {
        foreach ($allowedthemes as $allowedtheme) {
            $returnarray[] = ['key' => $allowedtheme, 'value' => $allowedtheme];
        }
    }

    return json_encode($returnarray);
}
