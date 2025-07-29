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

    $processed_loops = array();

    if ($loops) {
        foreach($loops as $loop=>$loop_data) {
            mtrace ("Checking LOOP ".$loop);
            unset($loop_system);

            if ($DB->record_exists('loop_systems', array ('externalid'=>$loop))) {
                // update
                mtrace ("LOOP already exisits");


                $loop_system = $DB->get_record('loop_systems', array ('externalid'=>$loop));
                $processed_loops[] = $loop_system->id;

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
                $new_loop_system->timemodified = time();
                $new_loop_system->timecreated = time();
                $new_id = $DB->insert_record('loop_systems', $new_loop_system, true);
                $processed_loops[] = $new_id;
                mtrace ("inserted into Database: ".$new_id);
            }





        }


        // delete all not processed loops
        if (!empty($processed_loops)) {
            $placeholders = str_repeat('?,', count($processed_loops) - 1) . '?';
            $sql = "SELECT * FROM {loop_systems} WHERE id NOT IN ($placeholders)";
            $to_delete = $DB->get_records_sql($sql, $processed_loops);
        } else {
            $to_delete = array();
        }

        if (is_array($to_delete)) {
        	foreach ($to_delete as $del_loop) {
        		mtrace ("delete LOOP with id: ".$del_loop->id);
        		$DB->delete_records('loop_systems', array('id'=>$del_loop->id));
        	}
        }





    }


    return true;
 }



function get_loop_structure($url) {
	global $CFG, $DB;



	if (!$loop = $DB->get_record_sql("select * from {loop_systems} where " . $DB->sql_compare_text('url') . " = '".$url."'" )) {
		return false;
	}



	$wgeMoodleLoopToken = get_config('mod_loop', 'token');

	$moodle_url = str_replace('https://','',$CFG->wwwroot);

	$loop_structure_url = 'https://'.$url.'/mediawiki/api.php?action=loopauth-structure&auth=api&m='.$moodle_url.'&t='.$wgeMoodleLoopToken.'&format=json';

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$loop_structure_url);
	curl_setopt($ch, CURLOPT_FAILONERROR, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);

	#curl_setopt($ch, CURLOPT_TIMEOUT, intval($timeout));
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);

	$result = curl_exec($ch);


	if(curl_errno($ch))	{
		return false;
	}

	curl_close($ch);

	$structure_result = json_decode ($result, true);
	$structure = $structure_result['loopauth-structure']['structure'];


	$return = array();
	$return['---'] = '---';
	if (isset($structure) && is_array($structure)) {
		foreach ($structure as $structure_item) {
			$key = urlencode($structure_item['toctext']);
			$level = $structure_item['toclevel'];
			$number= $structure_item['tocnumber'];
			if ($number != 0) {
				$value = str_pad('',  $level-1, " ").$number.' '.$structure_item['toctext'];
				$return[$key] = $value;
			}
		}
	}

	$returnarray = array();
	$n = 1;
	foreach ($return as $key => $value) {
		$returnarray[] = array('key'=>$key, 'value'=> $value);
		$n++;
	}



	return json_encode($returnarray);
}

function get_loop_themes($url) {
	global $CFG, $DB;

	if (!$loop = $DB->get_record_sql("select * from {loop_systems} where " . $DB->sql_compare_text('url') . " = '".$url."'" )) {
		return false;
	}
	$allowed_themes = json_decode($loop->allowed_themes);

	$returnarray = array();

	if (isset($allowed_themes) && is_array($allowed_themes)) {
		foreach ($allowed_themes as $allowed_theme) {
			$returnarray[] = array('key'=>$allowed_theme, 'value'=> $allowed_theme);
		}
	}

	return json_encode($returnarray);

}
