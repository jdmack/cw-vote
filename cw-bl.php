<?php

    include_once('cw-dal.php');

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
