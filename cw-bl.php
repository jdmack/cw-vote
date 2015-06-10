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
        $polls = dal_selectPollCurrent();
        if(count($polls) == 0) {
            $poll = null;
        }
        else {
            $poll = array_pop($polls);
        }

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
        $polls = dal_selectPollCurrent();
        if(count($polls) == 0) {
            return null;
        }
        else {
            $poll = array_pop($polls);
        }
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
    // bl_castVotes
    //
    // Parameters:
    //  - BLAH: BLAH - BLAH
    //
    // Return: BLAH: BLAH - BLAH
    //
    //-------------------------------------------------------------------------
    function bl_castVotes($username, $vote_options)
    {
        $polls = dal_selectPollCurrent();
        if(count($polls) == 0) {
            return "fail:no_poll";
        }
        else {
            $poll = array_pop($polls);
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
        
        if($poll->type->name == "multivote") {
            return bl_castVotesMultivote($poll, $user, $vote_options);
        }
        else if($poll->type->name == "ranked") {
            return bl_castVotesRanked($poll, $user, $vote_options);
        }
    }

    //-------------------------------------------------------------------------
    // bl_castVotesMultivote
    //
    // Parameters:
    //  - BLAH: BLAH - BLAH
    //
    // Return: BLAH: BLAH - BLAH
    //
    //-------------------------------------------------------------------------
    function bl_castVotesMultivote($poll, $user, $vote_options)
    {
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
        // TODO: get the "success:update" response working again
        // TODO: Do something if there are extra $existing_votes that weren't popped

        return "success";
    }

    //-------------------------------------------------------------------------
    // bl_castVotesRanked
    //
    // Parameters:
    //  - BLAH: BLAH - BLAH
    //
    // Return: BLAH: BLAH - BLAH
    //
    //-------------------------------------------------------------------------
    function bl_castVotesRanked($poll, $user, $vote_options)
    {
        $date = date('c');

        $existing_votes = dal_selectVotesByPollAndUser($poll->id, $user->id);
        error_log("[CW-VOTE] [DEBUG] existing_votes: " . count($existing_votes));
        error_log("[CW-VOTE] [DEBUG] vote_options: " . count($vote_options));

        // If you decide to allow for voting of nothing to clear votes, right here do an if $vote_options is 0 and then delete the votes

        foreach($vote_options as $this_option) {
            $option = dal_selectOptionByName($this_option->name);

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
                $vote->value = $this_option->value;
                dal_updateVote($vote);
            }
            else {
                $vote = new Vote();
                $vote->poll = $poll;
                $vote->user = $user;
                $vote->date = $date;
                $vote->option = $option;
                $vote->value = $this_option->value;
                dal_insertVote($vote);
            }
        }
        // TODO: get the "success:update" response working again
        // TODO: Do something if there are extra $existing_votes that weren't popped

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
        $polls = dal_selectPollCurrent();
        if(count($polls) == 0) {
            return null;
        }
        else {
            $poll = array_pop($polls);
        }

        $options = dal_selectOptionsByPoll($poll->id);

        if($poll->type->name == "multivote") {
            $counts = dal_selectVoteCounts($poll->id);
        }
        else if($poll->type->name == "ranked") {
            $counts = dal_selectVoteScores($poll->id);
        }

        if($counts == null) {
            //error_log("[CW-VOTE] [DEBUG] here");
            return "null";
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

    //-------------------------------------------------------------------------
    // bl_getOptions
    //
    // Parameters:
    //  - BLAH: BLAH - BLAH
    //
    // Return: BLAH: BLAH - BLAH
    //
    //-------------------------------------------------------------------------
    function bl_getOptions()
    {
        $options = dal_selectOptions();
        $json = json_encode($options);
        return $json;
    }

    //-------------------------------------------------------------------------
    // bl_createPoll
    //
    // Parameters:
    //  - BLAH: BLAH - BLAH
    //
    // Return: BLAH: BLAH - BLAH
    //
    //-------------------------------------------------------------------------
    function bl_createPoll($name, $description, $start, $end, $type, $max_votes, $options)
    {
        bl_closeCurrentPolls();

        // Create poll
        $poll = new Poll();
        $poll->name = $name;
        $poll->description = $description;

        $start_date = date_create($start);
        $start_date->setTime(0, 0, 0);
        $poll->start_date = $start_date->format(DateTime::W3C);
        $end_date = date_create($end);
        $end_date->setTime(23, 59, 59);
        $poll->end_date = $end_date->format(DateTime::W3C);

        $poll->type = dal_selectPollTypeByName($type);
        if($type == "multivote") {
            $poll->max_votes = $max_votes;
        }
            
        // Insert poll
        $poll_id = dal_insertPoll($poll);

        // Insert options
        $poll_options = array();
        foreach($options as $option) {
            $poll_option = dal_selectOptionByName($option);
            array_push($poll_options, $poll_option);
            dal_insertPollOption($poll_id, $poll_option->id);
        }

        return "success";
    }

    //-------------------------------------------------------------------------
    // bl_closeCurrentPolls
    //
    // Parameters:
    //  - BLAH: BLAH - BLAH
    //
    // Return: BLAH: BLAH - BLAH
    //
    //-------------------------------------------------------------------------
    function bl_closeCurrentPolls()
    {
        $polls = dal_selectPollCurrent();
        foreach($polls as $poll) {
            bl_closePoll($poll);
        }
    }

    //-------------------------------------------------------------------------
    // bl_closePoll
    //
    // Parameters:
    //  - BLAH: BLAH - BLAH
    //
    // Return: BLAH: BLAH - BLAH
    //
    //-------------------------------------------------------------------------
    function bl_closePoll($poll)
    {
        $poll->end_date = date('c');
        dal_updatePoll($poll);
    }

    //-------------------------------------------------------------------------
    // bl_addOption
    //
    // Parameters:
    //  - BLAH: BLAH - BLAH
    //
    // Return: BLAH: BLAH - BLAH
    //
    //-------------------------------------------------------------------------
    function bl_addOption($option_name)
    {
        $option = new Option();
        $option->name = $option_name;
        dal_insertOption($option);
        return "success: " . $option_name;
    }

    //-------------------------------------------------------------------------
    // bl_deleteOption
    //
    // Parameters:
    //  - BLAH: BLAH - BLAH
    //
    // Return: BLAH: BLAH - BLAH
    //
    //-------------------------------------------------------------------------
    function bl_deleteOption($option_name)
    {
        $num_deleted = dal_deleteOptionByName($option_name);
        if($num_deleted > 0) {
        return "success: " . $option_name;
        }
        else {
            return "failed";
        }
    }
?>
