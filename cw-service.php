<?php
    require_once('cw-bl.php');
    date_default_timezone_set('America/Los_Angeles');


    // Read request
    /* Actions:
        - create_poll
        - 
        - select(name)
        - selectAll()
        - update(name, value)
        - updateAll(array)  
    */
    if(isset($_GET['action'])) 
    {
        $action = $_GET['action'];
        //echo "action: $action\n";

        // Perform request
        if($action == "get_current_poll")
        {
            $response = bl_getCurrentPoll();
            echo $response;
        }
    }

    else {
        echo "no action provided";
    }
    

    // Return data



?>
