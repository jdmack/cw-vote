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
    // bl_getVotesCurrent
    //
    // Parameters:
    //  - BLAH: BLAH - BLAH
    //
    // Return: BLAH: BLAH - BLAH
    //
    //-------------------------------------------------------------------------
    function bl_getVotesCurrent($username)
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
            $votes = dal_selectVotesByPollAndUser($poll->id, $user->id);
            $json = json_encode($votes);
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
    function bl_castVotes($username, $vote_options)
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

        $date = date('c');

        $existing_votes = dal_selectVotesByPollAndUser($poll->id, $user->id);

        // If you decide to allow for voting of nothing to clear votes, right here do an if $vote_options is 0 and then delete the votes

        foreach($vote_options as $this_option) {
            $option = dal_selectOptionByName($this_option);

            if($option == null) {
                return "fail:bad_option";
            }
            
            // pop an existing vote off the array of existing votes and update its value to current option
            if($existing_votes == null) {
                $vote = null;
            }
            else {
                $vote = array_pop($existing_votes);
            }
            
            // Check for current vote
            if($vote != null) {
                $vote->poll = $poll;
                $vote->date = $date;
                $vote->option = $option;
                dal_updateVote($vote);
            }
            else {
                $vote = new Vote();
                $vote->poll = $poll;
                $vote->user = $user;
                $vote->date = $date;
                $vote->option = $option;
                dal_insertVote($vote);
            }
        }

        return "success";
    }

    //-------------------------------------------------------------------------
    // bl_getCurrentPollResults
    //
    // Parameters:
    //  - BLAH: BLAH - BLAH
    //
    // Return: BLAH: BLAH - BLAH
    //
    //-------------------------------------------------------------------------
    function bl_getCurrentPollResults()
    {
        $poll = dal_selectPollCurrent();
        if($poll == null) {
            return null;
        }
        $options = dal_selectOptionsByPoll($poll->id);
        $counts = dal_selectVoteCounts($poll->id);

        if($counts == null) {
            return null;
        }

        $results = array();

        foreach($options as $option) {
            $count = 0;
            if(array_key_exists($option->name, $counts)) {
                $count = $counts[$option->name];
            }
            $results[$option->name] = $count;
        }
        
        $json = json_encode($results);
        return $json;
    }

?>
