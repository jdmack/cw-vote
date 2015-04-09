<?php
    include_once('cw-dal.php');
    include_once('cw-bl.php');
    include_once('cw-config.php');

    include_once('objects/option.php');
    include_once('objects/poll.php');
    include_once('objects/poll_type.php');
    include_once('objects/user.php');
    include_once('objects/vote.php');

    date_default_timezone_set('America/Los_Angeles');

    // INSERT POLL
    /*
    $poll = new Poll();
    $poll->description = "Test Poll 2";
    $poll->type = dal_selectPollType(1);
    $poll->start_date = date('c');
    $poll->end_date = date('c', time() + 60 * 60 * 24 * 7);
    $poll->max_votes = 2;
    $poll_id = dal_insertPoll($poll);
    print "poll_id: $poll_id\n";
    */

    //$poll_id = 9;
    //$options = dal_selectOptions();
    //foreach($options as $option) {
    //    dal_insertPollOption($poll_id, $option->id);
    //}

    //$current_poll = dal_selectPollCurrent();
    bl_closeCurrentPolls();
    // INSERT VOTE
    /*
    $vote = new Vote();
    $vote->poll = $current_poll;
    $vote->user = dal_selectUserByName("test");
    $vote->date = date('c');
    $vote->option = dal_selectOption(1); 
    dal_insertVote($vote);
    */

    //$current_poll = dal_selectPoll($poll_id);

    //print(json_encode($poll->type, JSON_PRETTY_PRINT));
    print(json_encode($current_poll, JSON_PRETTY_PRINT));
    //print(json_encode($poll_types, JSON_PRETTY_PRINT));
    //print(json_encode(dal_selectPollOption(1), JSON_PRETTY_PRINT));

?>
