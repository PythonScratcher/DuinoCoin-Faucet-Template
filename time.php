<?php
// Check if the user has claimed within the past 24 hours
function check_last_claim($duco_address, $ip_address) {
    $time_file = 'time.csv';
    $time_data = array_map('str_getcsv', file($time_file));
    foreach($time_data as $row) {
        if($row[0] == $duco_address && $row[1] == $ip_address) {
            $last_claim_time = strtotime($row[2]);
            $now = strtotime('now');
            $time_diff = $now - $last_claim_time;
            $time_left = 86400 - $time_diff; // One day in seconds
            $result = array('claimed' => true, 'time_left' => $time_left);
            break;
        }
    }
    if(!isset($result)) {
        $result = array('claimed' => false);
    }
    return $result;
}

// Log the claim time to prevent users from making multiple claims per day
function log_claim_time($duco_address, $ip_address) {
    $time_file = 'time.csv';
    $claim_time = date('Y-m-d H:i:s');
    $time_file_handle = fopen($time_file, 'a');
    fputcsv($time_file_handle, array($duco_address, $ip_address, $claim_time));
    fclose($time_file_handle);
}
