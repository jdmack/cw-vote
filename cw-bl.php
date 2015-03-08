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
    function bl_getCurrentPoll()
    {
        $poll = dal_selectPollCurrent();
        $json = json_encode($poll);
        return $json;
    }

    //-------------------------------------------------------------------------
    // bl_getVoteCurrent
    //
    // Parameters:
    //  - BLAH: BLAH - BLAH
    //
    // Return: BLAH: BLAH - BLAH
    //
    //-------------------------------------------------------------------------
    function bl_getVoteCurrent($username)
    {
        $poll = dal_selectPollCurrent();
        $user = dal_selectUserByName($username);

        // If user isn't in DB, insert it and update variable
        if($user == null) {
            $user = new User();
            $user->name = $username;

            $user_id = dal_insertUser($user);
            $user->id = $user_id;
        }

        // If there is current poll, lookup the vote
        if($poll != null) {
            $vote = dal_selectVoteByPollAndUser($poll->id, $user->id);
            $json = json_encode($vote);
        }
        else {
            $json = json_encode(null);
        }

        return $json;
    }

    //-------------------------------------------------------------------------
    // bl_castVote
    //
    // Parameters:
    //  - BLAH: BLAH - BLAH
    //
    // Return: BLAH: BLAH - BLAH
    //
    //-------------------------------------------------------------------------
    function bl_castVote($username, $option_name)
    {
        $poll = dal_selectPollCurrent();
        if($poll == null) {
            return "fail:no_poll";
        }
        $return = "";

        // If user isn't in DB, insert it and update variable
        $user = dal_selectUserByName($username);
        if($user == null) {
            $user = new User();
            $user->name = $username;

            $user_id = dal_insertUser($user);
            $user->id = $user_id;
        }

        $option = dal_selectOptionByName($option_name);
        if($option == null) {
            return "fail:bad_option";
        }

        $date = date('c');

        // Check for current vote
        $vote = dal_selectVoteByPollAndUser($poll->id, $user->id);
        if($vote != null) {
            $vote->poll = $poll;
            $vote->date = $date;
            $vote->option = $option;
            dal_updateVote($vote);
            return "success:updated";
        }
        else {
            $vote->poll = $poll;
            $vote->user = $user;
            $vote->date = $date;
            $vote->option = $option;
            dal_insertVote($vote);
            return "success";
        }
    }

?>
