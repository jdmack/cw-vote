<?php
    require_once('cw-bl.php'); date_default_timezone_set('America/Los_Angeles'); // Read request
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
        //echo "action: $action\n";

        // Perform request
        if($action == "get_current_poll")
        {
            //print "hello";
            $response = bl_getCurrentPoll();
            print $response;
        }
        else if($action == "get_vote_current") {
            $username = $_GET['username'];
            $response = bl_getVoteCurrent($username);
            print $response;
        }
        else if($action == "get_current_results") {
            $response = bl_getCurrentPollResults();
            print $response;
        }
    }

    else if(isset($_POST['action'])) {
        $action = $_POST['action'];

        if($action == "cast_vote") {
            $username = $_POST['username'];
            $option = $_POST['option'];
            $response = bl_castVote($username, $option);
            print $response;
        }
    }

    else {
        echo "no action provided";
    }
    

    // Return data



?>
