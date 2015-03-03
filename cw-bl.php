<?php

    include_once('cw-config.php');
    include_once('cw-dal.php');
    include_once('objects/option.php');
    include_once('objects/poll.php');
    include_once('objects/poll_type.php');
    include_once('objects/user.php');
    include_once('objects/vote.php');


    //-------------------------------------------------------------------------
    // bl_getCurrentPoll
    //
    // Parameters:
    //  - BLAH: BLAH - BLAH
    //
    // Return: BLAH: BLAH - BLAH
    //
    //-------------------------------------------------------------------------
    function bl_getCurrentPoll($cell)
    {
        $poll = dal_selectPollCurrent();
        $json = json_encode($poll);
        return $json;
    }

?>
