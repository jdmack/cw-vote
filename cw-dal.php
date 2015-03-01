<?php

    //-------------------------------------------------------------------------
    // dal_createConnection
    //-------------------------------------------------------------------------
    function dal_createConnection()
    {
        $host     = "localhost";
        $dbname   = "cw-vote";
        $username = "cwvote";
        $password = "";

        $mysqli = new mysqli($host, $username, $password, $dbname);

        if($mysqli->connect_error) {
            printf("Connect failed: %s\n", $mysqli->connect_error);
            exit();
        }

        return $mysqli;
    }


    //-------------------------------------------------------------------------
    // dal_insertPoll
    // 
    // Parameters:
    //  - poll: Poll - poll to insert
    //
    // Return: id: int - id of the created poll
    // 
    //-------------------------------------------------------------------------
    function dal_insertPoll($poll)
    {
        $connection = dal_createConnection();

        $query = "INSERT INTO poll (description, type, start_date, end_date) VALUES (?, ?, ?, ?)";

        // Prepare statement
        $statement = $connection->prepare($query);
        if($statement == false) {
            trigger_error('Wrong SQL: ' . $query . ' Error: ' . $connection->error, E_USER_ERROR);
        }
        
        // bind parameters
        $statement->bind_param('siss', $poll->description, $poll->type->id, $poll->start_date, $poll->end_date);

        // execute
        $statement->execute();

        if($statement->affected_rows > 0) {
            return $statement->insert_id;
        }
        else {
            echo "No Results Found";
            return null;
        }
    }

    //-------------------------------------------------------------------------
    // dal_selectPollCurrent
    // 
    // Parameters:
    //  - BLAH: BLAH - BLAH
    //
    // Return: BLAH: BLAH - BLAH
    // 
    //-------------------------------------------------------------------------
    function dal_selectPollCurrent()
    {

    }

    //-------------------------------------------------------------------------
    // dal_insertUser
    // 
    // Parameters:
    //  - BLAH: BLAH - BLAH
    //
    // Return: BLAH: BLAH - BLAH
    // 
    //-------------------------------------------------------------------------
    function dal_insertUser($user)
    {
        $connection = dal_createConnection();

        $query = "INSERT INTO user (name) VALUES (?)";

        // Prepare statement
        $statement = $connection->prepare($query);
        if($statement == false) {
            trigger_error('Wrong SQL: ' . $query . ' Error: ' . $connection->error, E_USER_ERROR);
        }
        
        // bind parameters
        $statement->bind_param('s', $user->name);

        // execute
        $statement->execute();

        if($statement->affected_rows > 0) {
            return $statement->insert_id;
        }
        else {
            echo "No Results Found";
            return null;
        }

    }

    //-------------------------------------------------------------------------
    // dal_selectUserByName
    // 
    // Parameters:
    //  - BLAH: BLAH - BLAH
    //
    // Return: BLAH: BLAH - BLAH
    // 
    //-------------------------------------------------------------------------
    function dal_selectUserByName()
    {

    }

    //-------------------------------------------------------------------------
    // dal_insertVote
    // 
    // Parameters:
    //  - BLAH: BLAH - BLAH
    //
    // Return: BLAH: BLAH - BLAH
    // 
    //-------------------------------------------------------------------------
    function dal_insertVote($vote)
    {
        $connection = dal_createConnection();

        $query = "INSERT INTO vote (poll, user, date, option, value) VALUES (?, ?, ?, ?, ?)";

        // Prepare statement
        $statement = $connection->prepare($query);
        if($statement == false) {
            trigger_error('Wrong SQL: ' . $query . ' Error: ' . $connection->error, E_USER_ERROR);
        }
        
        // bind parameters
        $statement->bind_param('iisis', $vote->poll->id, $vote->user->id, $vote->date, 
            $vote->option->id, $vote->value);

        // execute
        $statement->execute();

        if($statement->affected_rows > 0) {
            return $statement->insert_id;
        }
        else {
            echo "No Results Found";
            return null;
        }
    }

    //-------------------------------------------------------------------------
    // dal_selectVoteByPollAndUser
    // 
    // Parameters:
    //  - BLAH: BLAH - BLAH
    //
    // Return: BLAH: BLAH - BLAH
    // 
    //-------------------------------------------------------------------------
    function dal_selectVoteByPollAndUser()
    {

    }

    //-------------------------------------------------------------------------
    // dal_insertOption
    // 
    // Parameters:
    //  - BLAH: BLAH - BLAH
    //
    // Return: BLAH: BLAH - BLAH
    // 
    //-------------------------------------------------------------------------
    function dal_insertOption($option)
    {
        $connection = dal_createConnection();

        $query = "INSERT INTO option (name) VALUES (?)";

        // Prepare statement
        $statement = $connection->prepare($query);
        if($statement == false) {
            trigger_error('Wrong SQL: ' . $query . ' Error: ' . $connection->error, E_USER_ERROR);
        }
        
        // bind parameters
        $statement->bind_param('s', $option->name);

        // execute
        $statement->execute();

        if($statement->affected_rows > 0) {
            return $statement->insert_id;
        }
        else {
            echo "No Results Found";
            return null;
        }

    }

    //-------------------------------------------------------------------------
    // dal_selectOptionByName
    // 
    // Parameters:
    //  - BLAH: BLAH - BLAH
    //
    // Return: BLAH: BLAH - BLAH
    // 
    //-------------------------------------------------------------------------
    function dal_selectOptionByName()
    {

    }

    //-------------------------------------------------------------------------
    // dal_insertPollOption
    // 
    // Parameters:
    //  - BLAH: BLAH - BLAH
    //
    // Return: BLAH: BLAH - BLAH
    // 
    //-------------------------------------------------------------------------
    function dal_insertPollOption($poll_option)
    {
        $connection = dal_createConnection();

        $query = "INSERT INTO poll_option (poll, option) VALUES (?, ?)";

        // Prepare statement
        $statement = $connection->prepare($query);
        if($statement == false) {
            trigger_error('Wrong SQL: ' . $query . ' Error: ' . $connection->error, E_USER_ERROR);
        }
        
        // bind parameters
        $statement->bind_param('ii', $poll_option->poll->id, $poll_option->option->id);

        // execute
        $statement->execute();

        if($statement->affected_rows > 0) {
            return $statement->insert_id;
        }
        else {
            echo "No Results Found";
            return null;
        }

    }

    //-------------------------------------------------------------------------
    // dal_selectPollOptionsByPoll
    // 
    // Parameters:
    //  - BLAH: BLAH - BLAH
    //
    // Return: BLAH: BLAH - BLAH
    // 
    //-------------------------------------------------------------------------
    function dal_selectPollOptionsByPoll()
    {

    }

    //-------------------------------------------------------------------------
    // dal_insertPollType
    // 
    // Parameters:
    //  - BLAH: BLAH - BLAH
    //
    // Return: BLAH: BLAH - BLAH
    // 
    //-------------------------------------------------------------------------
    function dal_insertPollType($poll_type)
    {
        $connection = dal_createConnection();

        $query = "INSERT INTO poll_type (name) VALUES (?)";

        // Prepare statement
        $statement = $connection->prepare($query);
        if($statement == false) {
            trigger_error('Wrong SQL: ' . $query . ' Error: ' . $connection->error, E_USER_ERROR);
        }
        
        // bind parameters
        $statement->bind_param('s', $poll_type->name);

        // execute
        $statement->execute();

        if($statement->affected_rows > 0) {
            return $statement->insert_id;
        }
        else {
            echo "No Results Found";
            return null;
        }

    }

    //-------------------------------------------------------------------------
    // dal_selectPollTypes
    // 
    // Parameters:
    //  - BLAH: BLAH - BLAH
    //
    // Return: BLAH: BLAH - BLAH
    // 
    //-------------------------------------------------------------------------
    function dal_selectPollTypes()
    {

    }

    //-------------------------------------------------------------------------
    // dal_BLAH
    // 
    // Parameters:
    //  - BLAH: BLAH - BLAH
    //
    // Return: BLAH: BLAH - BLAH
    // 
    //-------------------------------------------------------------------------
    function dal_BLAH()
    {

    }

?>
