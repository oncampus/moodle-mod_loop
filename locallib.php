<?php
/**
 * @package mod_loop
 * @author  Marc Vorreiter <marc.vorreiter@th-luebeck.de>  
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
    $json_result = curl_exec($cha);
    
    if (!$json_result) {
        throw new Exception("Error getting data from server: " . curl_error($cha));
    }
    
    $loops = json_decode($json_result,true);
    #mtrace (print_r($loops,true));

    curl_close($cha);
    
    if ($loops) {
        foreach($loops as $loop=>$loop_data) {
            mtrace ("Checking LOOP ".$loop);
            unset($loop_system);

            if ($DB->record_exists('loop_systems', array ('externalid'=>$loop))) {
                // update
                mtrace ("LOOP already exisits");

                $loop_system = $DB->get_record('loop_systems', array ('externalid'=>$loop));

                if (
                    ($loop_system->name != $loop_data['name']) ||
                    ($loop_system->url != $loop_data['url']) ||
                    ($loop_system->personalized_access != $loop_data['personalized_access']) ||
                    ($loop_system->student_role_allocation != $loop_data['student_role_allocation']) ||
                    ($loop_system->teacher_role_allocation != $loop_data['teacher_role_allocation']) ||
                    ($loop_system->allowed_themes != json_encode($loop_data['allowed_themes'])) 
                ) {

                    $update_loop_system = new stdClass();
                    $update_loop_system->id = $loop_system->id;
                    $update_loop_system->externalid = $loop;
                    $update_loop_system->name = $loop_data['name'];
                    $update_loop_system->url = $loop_data['url'];
                    $update_loop_system->personalized_access = $loop_data['personalized_access'];
                    $update_loop_system->student_role_allocation = $loop_data['student_role_allocation'];
                    $update_loop_system->teacher_role_allocation = $loop_data['teacher_role_allocation'];
                    $update_loop_system->allowed_themes = json_encode($loop_data['allowed_themes']);
                    $update_loop_system->timemodified = time();
                    $DB->update_record('loop_systems', $update_loop_system);

                    mtrace ("LOOP updated");
                } else {
                    mtrace ("no update neccessary");
                }




            } else {
                mtrace ("New LOOP");
                // insert
                $new_loop_system = new stdClass();
                $new_loop_system->externalid = $loop;
                $new_loop_system->name = $loop_data['name'];
                $new_loop_system->url = $loop_data['url'];
                $new_loop_system->personalized_access = $loop_data['personalized_access'];
                $new_loop_system->student_role_allocation = $loop_data['student_role_allocation'];
                $new_loop_system->teacher_role_allocation = $loop_data['teacher_role_allocation'];
                $new_loop_system->allowed_themes = json_encode($loop_data['allowed_themes']);
                $new_loop_system->timecreated = time();
                $DB->insert_record('loop_systems', $new_loop_system, true);
                mtrace ("inserted into Database");
            }

            
            


        }
    }


    return true;
 }