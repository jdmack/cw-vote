<?php
    require_once('cw-bl.php'); date_default_timezone_set('America/Los_Angeles'); // Read request
    header("Access-Control-Allow-Origin: *");
    /* Actions:
        - create_poll
        - 
        - select(name)
        - selectAll()
        - update(name, value)
        - updateAll(array)  
    */
    if(isset($_GET['action'])) {
        $action = $_GET['action'];
        $_SESSION['db-env'] = $_POST['env'];

        //echo "action: $action\n";
        error_log("[CW-VOTE] [GET] $action");

        // Perform request
        if($action == "get_current_poll")
        {
            //print "hello";
            $response = bl_getCurrentPoll();
            print $response;
        }
        else if($action == "get_votes_current") {
            $username = $_GET['username'];
            $response = bl_getVotesCurrent($username);
            print $response;
        }
        else if($action == "get_current_results") {
            $response = bl_getCurrentPollResults();
            print $response;
        }
    }

    else if(isset($_POST['action'])) {
        $action = $_POST['action'];
        $_SESSION['db-env'] = $_POST['env'];
        error_log("[CW-VOTE] [POST] $action");

        if($action == "cast_votes") {
            //$json = file_get_contents('php:://input');
            //$object = json_decode($json);
            $username = $_POST['username'];
            $options = json_decode($_POST['options']);
            $response = bl_castVotes($username, $options);
            print $response;
        }
    }

    else {
        echo "no action provided";
    }
    

    // Return data



?>
