<?php
    include_once('cw-dal.php');

    include_once('objects/option.php');
    include_once('objects/poll.php');
    include_once('objects/poll_type.php');
    include_once('objects/user.php');
    include_once('objects/vote.php');

    date_default_timezone_set('America/Los_Angeles');

    /*
    $poll = new Poll();
    $poll->description = "Test Poll - Clan";
    $poll->type = dal_selectPollType(1);
    $poll->start_date = date('c');
    $poll->end_date = date('c', time() + 60 * 60 * 24 * 2);
    $poll_id = dal_insertPoll($poll);
    */
    //$poll_id = 3;

    //$options = dal_selectOptions();

    //foreach($options as $option) {
    //    dal_insertPollOption($poll_id, $option->id);
   // }

    $current_poll = dal_selectPollCurrent();
    //$current_poll = dal_selectPoll($poll_id);

    //print(json_encode($poll->type, JSON_PRETTY_PRINT));
    print(json_encode($current_poll, JSON_PRETTY_PRINT));
    //print(json_encode($poll_types, JSON_PRETTY_PRINT));
    //print(json_encode(dal_selectPollOption(1), JSON_PRETTY_PRINT));

?>
