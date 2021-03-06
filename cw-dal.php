<?php

    //$connection = null;

    //-------------------------------------------------------------------------
    // dal_createConnection
    //-------------------------------------------------------------------------
    function dal_createConnection()
    {
        //print "Creating new connection\n";
        global $host;
        global $dbname;
        global $db_username;
        global $db_password;

        $this_dbname = $dbname;

        /*
        if(isset($_SESSION['db-env'])) {
            if($_SESSION['db-env'] == 'dev') {
                $this_dbname += '_dev';
            }
        }
        */
        //error_log("[CW-VOTE] [DB] database: $this_dbname");


        $mysqli = new mysqli($host, $db_username, $db_password, $this_dbname);

        if($mysqli->connect_error) {
            printf("Connect failed: %s\n", $mysqli->connect_error);
            exit();
        }

        $connection = $mysqli;
        return $mysqli;
    }

    //-------------------------------------------------------------------------
    // dal_createConnection
    //-------------------------------------------------------------------------
    function dal_closeConnection()
    {
        /*
        global $connection;
        if($connection == null) {
            return;
        }
        $connection->close();
        $connection = null;
        */
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

        $query = "INSERT INTO poll (name, description, type, start_date, end_date, max_votes) VALUES (?, ?, ?, ?, ?, ?)";

        // Prepare statement
        $statement = $connection->prepare($query);
        if($statement == false) {
            trigger_error('Wrong SQL: ' . $query . ' Error: ' . $connection->error, E_USER_ERROR);
        }
        
        // bind parameters
        $statement->bind_param('ssissi', $poll->name, $poll->description, $poll->type->id, $poll->start_date, $poll->end_date, $poll->max_votes);

        // execute
        $statement->execute();

        $return_value;
        if($statement->affected_rows > 0) {
            $return_value = $statement->insert_id;
        }
        else {
            echo "No Results Found";
            $return_value = null;
        }
        $statement->close();
        $connection->close();

        return $return_value;
    }

    //-------------------------------------------------------------------------
    // dal_selectPoll
    // 
    // Parameters:
    //  - BLAH: BLAH - BLAH
    //
    // Return: BLAH: BLAH - BLAH
    // 
    //-------------------------------------------------------------------------
    function dal_selectPoll($id)
    {
        $connection = dal_createConnection();

        $query = "SELECT name, description, type, start_date, end_date, max_votes FROM poll WHERE id = ?";
         
        // prepare statement
        $statement = $connection->prepare($query);

        if($statement === false) {
              trigger_error('Wrong SQL: ' . $query . ' Error: ' . $connection->error, E_USER_ERROR);
        }
          
        // bind parameters
        $statement->bind_param('i', $id);
           
        // execute
        $statement->execute();
        
        // get results
        $statement->bind_result($name, $description, $type_id, $start_date, $end_date, $max_votes);
        $statement->fetch();

        // create return object
        $poll = new Poll();
        $poll->id = $id;
        $poll->name = $name;
        $poll->description = $description;
        $poll->type = dal_selectPollType($type_id);
        $poll->start_date = $start_date;
        $poll->end_date = $end_date;
        $poll->max_votes = $max_votes;

        $poll->options = dal_selectOptionsByPoll($id);

        $statement->close();
        $connection->close();
    
        return $poll;

    }

    //-------------------------------------------------------------------------
    // dal_updatePoll
    // 
    // Parameters:
    //  - BLAH: BLAH - BLAH
    //
    // Return: BLAH: BLAH - BLAH
    // 
    //-------------------------------------------------------------------------
    function dal_updatePoll($poll)
    {
        $connection = dal_createConnection();

        $query = "UPDATE poll SET name = ?, description = ?, type = ?, start_date = ?, end_date = ?, max_votes = ? WHERE id = ?";

        // Prepare statement
        $statement = $connection->prepare($query);
        if($statement == false) {
            trigger_error('Wrong SQL: ' . $query . ' Error: ' . $connection->error, E_USER_ERROR);
        }
        
        // bind parameters
        $statement->bind_param('ssissii', 
            $poll->name,
            $poll->description,
            $poll->type->id, 
            $poll->start_date,
            $poll->end_date,
            $poll->max_votes,
            $poll->id);

        // execute
        $statement->execute();

        $return_value;
        if($statement->affected_rows > 0) {
            $return_value = $statement->insert_id;
        }
        else {
            echo "No Results Found";
            $return_value = null;
        }
        $statement->close();
        $connection->close();

        return $return_value;
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
        $connection = dal_createConnection();

        $query = "SELECT id, name, description, type, start_date, end_date, max_votes FROM poll WHERE start_date < ? AND end_date > ?";
         
        // prepare statement
        $statement = $connection->prepare($query);

        if($statement === false) {
              trigger_error('Wrong SQL: ' . $query . ' Error: ' . $connection->error, E_USER_ERROR);
        }

        $current_date = date("c"); 
        // bind parameters
        $statement->bind_param('ss', $current_date, $current_date);
           
        // execute
        $statement->execute();

        // get results
        $statement->bind_result($id, $name, $description, $type_id, $start_date, $end_date, $max_votes);

        $polls = array();

        while($statement->fetch()) {
            // create return object
            $poll = new Poll();
            $poll->id = $id;
            $poll->name = $name;
            $poll->description = $description;
            $poll->type = dal_selectPollType($type_id);
            $poll->start_date = $start_date;
            $poll->end_date = $end_date;
            $poll->options = dal_selectOptionsByPoll($id);
            $poll->max_votes = $max_votes;

            array_push($polls, $poll);
        }

        $statement->close();
        $connection->close();
    
        return $polls;
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

        $return_value;
        if($statement->affected_rows > 0) {
            $return_value = $statement->insert_id;
        }
        else {
            echo "No Results Found";
            $return_value = null;
        }
        $statement->close();
        $connection->close();

        return $return_value;
    }

    //-------------------------------------------------------------------------
    // dal_selectUser
    // 
    // Parameters:
    //  - BLAH: BLAH - BLAH
    //
    // Return: BLAH: BLAH - BLAH
    // 
    //-------------------------------------------------------------------------
    function dal_selectUser($id)
    {
        $connection = dal_createConnection();

        $query = "SELECT name FROM user WHERE id = ?";
         
        // prepare statement
        $statement = $connection->prepare($query);

        if($statement === false) {
              trigger_error('Wrong SQL: ' . $query . ' Error: ' . $connection->error, E_USER_ERROR);
        }
          
        // bind parameters
        $statement->bind_param('i', $id);
           
        // execute
        $statement->execute();
        
        // get results
        $statement->bind_result($name);
        $statement->fetch();

        // create return object
        $user = new User();
        $user->id = $id;
        $user->name = $name;
    
        $statement->close();
        $connection->close();

        return $user;
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
    function dal_selectUserByName($name)
    {
        $connection = dal_createConnection();

        $query = "SELECT id, name FROM user WHERE name = ?";
         
        // prepare statement
        $statement = $connection->prepare($query);

        if($statement === false) {
              trigger_error('Wrong SQL: ' . $query . ' Error: ' . $connection->error, E_USER_ERROR);
        }
          
        // bind parameters
        $statement->bind_param('s', $name);
           
        // execute
        $statement->execute();

        $statement->store_result();

        if($statement->num_rows <= 0) {
            return null;
        }
        
        // get results
        $statement->bind_result($id, $name);
        $statement->fetch();

        // create return object
        $user = new User();
        $user->id = $id;
        $user->name = $name;
    
        $statement->close();
        $connection->close();

        return $user;
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
    // TODO - Test
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

        $return_value;
        if($statement->affected_rows > 0) {
            $return_value = $statement->insert_id;
        }
        else {
            echo "No Results Found";
            $return_value = null;
        }
        $statement->close();
        $connection->close();

        return $return_value;
    }

    //-------------------------------------------------------------------------
    // dal_updateVote
    // 
    // Parameters:
    //  - BLAH: BLAH - BLAH
    //
    // Return: BLAH: BLAH - BLAH
    // 
    //-------------------------------------------------------------------------
    function dal_updateVote($vote)
    {
        $connection = dal_createConnection();

        $query = "UPDATE vote SET poll = ?, user = ?, date = ?, option = ?, value = ? WHERE id = ?";

        // Prepare statement
        $statement = $connection->prepare($query);
        if($statement == false) {
            trigger_error('Wrong SQL: ' . $query . ' Error: ' . $connection->error, E_USER_ERROR);
        }
        
        // bind parameters
        $statement->bind_param('iisisi', 
            $vote->poll->id, 
            $vote->user->id, 
            $vote->date, 
            $vote->option->id, 
            $vote->value,
            $vote->id);

        // execute
        $statement->execute();

        $return_value;
        if($statement->affected_rows > 0) {
            $return_value = $statement->insert_id;
        }
        else {
            echo "No Results Found";
            $return_value = null;
        }
        $statement->close();
        $connection->close();

        return $return_value;
    }

    //-------------------------------------------------------------------------
    // dal_selectVotesByPoll
    // 
    // Parameters:
    //  - BLAH: BLAH - BLAH
    //
    // Return: BLAH: BLAH - BLAH
    // 
    //-------------------------------------------------------------------------
    // TODO - Test
    function dal_selectVotesByPoll($id)
    {
        $connection = dal_createConnection();

        $query = "SELECT id, poll, user, date, option, value FROM vote WHERE id = ?";
         
        // prepare statement
        $statement = $connection->prepare($query);

        if($statement === false) {
              trigger_error('Wrong SQL: ' . $query . ' Error: ' . $connection->error, E_USER_ERROR);
        }

        // bind parameters
        $statement->bind_param('i', $id);
          
        // execute
        $statement->execute();
        
        // get results
        $statement->bind_result($id, $poll_id, $user_id, $date, $option_id, $value);

        $votes = array();
        
        $statement->fetch();
        $poll = dal_selectPoll($poll_id);

        do {
            // create return object
            $vote = new Vote();
            $vote->id = $id;
            $vote->poll = $poll;
            $vote->user = dal_selectUser($user_id);
            $vote->date = $date;
            $vote->option = dal_selectOption($option_id);
            $vote->value = $value;

            array_push($votes, $vote);
        } while($statement->fetch());

        $statement->close();
        $connection->close();

        return $votes;
    }

    //-------------------------------------------------------------------------
    // dal_selectVotesByPollAndUser
    // 
    // Parameters:
    //  - BLAH: BLAH - BLAH
    //
    // Return: BLAH: BLAH - BLAH
    // 
    //-------------------------------------------------------------------------
    function dal_selectVotesByPollAndUser($poll_id, $user_id)
    {
        $connection = dal_createConnection();

        $query = "SELECT id, poll, user, date, option, value FROM vote WHERE poll = ? AND user = ?";
         
        // prepare statement
        $statement = $connection->prepare($query);

        if($statement === false) {
              trigger_error('Wrong SQL: ' . $query . ' Error: ' . $connection->error, E_USER_ERROR);
        }

        // bind parameters
        $statement->bind_param('ii', $poll_id, $user_id);
          
        // execute
        $statement->execute();

        $statement->store_result();

        if($statement->num_rows <= 0) {
            return null;
        }

        // get results
        $statement->bind_result($id, $poll_id, $user_id, $date, $option_id, $value);

        $votes = array();
        
        $statement->fetch();
        $poll = dal_selectPoll($poll_id);
        $user = dal_selectUser($user_id);
        do {
            // create return object
            $vote = new Vote();
            $vote->id = $id;
            //$vote->poll = $poll;
            $vote->user = $user;
            $vote->date = $date;
            $vote->option = dal_selectOption($option_id);
            $vote->value = $value;

            array_push($votes, $vote);
        } while($statement->fetch());

        $statement->close();
        $connection->close();

        return $votes;
    }

    //-------------------------------------------------------------------------
    // dal_selectVoteCounts
    // 
    // Parameters:
    //  - BLAH: BLAH - BLAH
    //
    // Return: BLAH: BLAH - BLAH
    // 
    //-------------------------------------------------------------------------
    function dal_selectVoteCounts($poll)
    {
        $connection = dal_createConnection();

        $query = "SELECT option.name, COUNT(*) FROM vote, option WHERE vote.option = option.id AND vote.poll = ? group by option.name";
         
        // prepare statement
        $statement = $connection->prepare($query);

        if($statement === false) {
              trigger_error('Wrong SQL: ' . $query . ' Error: ' . $connection->error, E_USER_ERROR);
        }

        // bind parameters
        $statement->bind_param('i', $poll);
          
        // execute
        $statement->execute();
        
        // get results
        $statement->store_result();
        $statement->bind_result($name, $count);

        if($statement->num_rows <= 0) {
            return null;
        }

        $options = array();

        while($statement->fetch()) {
            $options[$name] = $count;
        }

        $statement->close();
        $connection->close();

        return $options;
    }

    //-------------------------------------------------------------------------
    // dal_selectVoteScores
    // 
    // Parameters:
    //  - BLAH: BLAH - BLAH
    //
    // Return: BLAH: BLAH - BLAH
    // 
    //-------------------------------------------------------------------------
    function dal_selectVoteScores($poll)
    {
        $connection = dal_createConnection();

        $query = "SELECT option.name, SUM(IFNULL(value, 0)) FROM vote, option WHERE vote.option = option.id AND vote.poll = ? group by option.name";
         
        // prepare statement
        $statement = $connection->prepare($query);

        if($statement === false) {
              trigger_error('Wrong SQL: ' . $query . ' Error: ' . $connection->error, E_USER_ERROR);
        }

        // bind parameters
        $statement->bind_param('i', $poll);
          
        // execute
        $statement->execute();
        
        // get results
        $statement->store_result();
        $statement->bind_result($name, $count);

        if($statement->num_rows <= 0) {
            return null;
        }

        $options = array();

        while($statement->fetch()) {
            $options[$name] = $count;
        }

        $statement->close();
        $connection->close();

        return $options;
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

        $return_value;
        if($statement->affected_rows > 0) {
            $return_value = $statement->insert_id;
        }
        else {
            echo "No Results Found";
            $return_value = null;
        }
        $statement->close();
        $connection->close();

        return $return_value;
    }

    //-------------------------------------------------------------------------
    // dal_selectOption
    // 
    // Parameters:
    //  - BLAH: BLAH - BLAH
    //
    // Return: BLAH: BLAH - BLAH
    // 
    //-------------------------------------------------------------------------
    function dal_selectOption($id)
    {
        $connection = dal_createConnection();

        $query = "SELECT name, standard FROM option WHERE id = ?";
         
        // prepare statement
        $statement = $connection->prepare($query);

        if($statement === false) {
              trigger_error('Wrong SQL: ' . $query . ' Error: ' . $connection->error, E_USER_ERROR);
        }
          
        // bind parameters
        $statement->bind_param('i', $id);
           
        // execute
        $statement->execute();
        
        // get results
        $statement->bind_result($name, $standard);
        $statement->fetch();

        // create return object
        $option = new Option();
        $option->id = $id;
        $option->name = $name;
        $option->standard = $standard;
    
        $statement->close();
        $connection->close();

        return $option;
    }


    //-------------------------------------------------------------------------
    // dal_selectOptions
    // 
    // Parameters:
    //  - BLAH: BLAH - BLAH
    //
    // Return: BLAH: BLAH - BLAH
    // 
    //-------------------------------------------------------------------------
    function dal_selectOptions()
    {
        $connection = dal_createConnection();

        $query = "SELECT id, name, standard FROM option";
         
        // prepare statement
        $statement = $connection->prepare($query);

        if($statement === false) {
              trigger_error('Wrong SQL: ' . $query . ' Error: ' . $connection->error, E_USER_ERROR);
        }
          
        // execute
        $statement->execute();
        
        // get results
        $statement->bind_result($id, $name, $standard);

        $options = array();

        while($statement->fetch()) {
            // create return object
            $option = new Option();
            $option->id = $id;
            $option->name = $name;
            $option->standard = $standard;

            array_push($options, $option);
        }

        $statement->close();
        $connection->close();

        return $options;
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
    function dal_selectOptionByName($name)
    {
        $connection = dal_createConnection();

        $query = "SELECT id, name, standard FROM option WHERE name = ?";
         
        // prepare statement
        $statement = $connection->prepare($query);

        if($statement === false) {
              trigger_error('Wrong SQL: ' . $query . ' Error: ' . $connection->error, E_USER_ERROR);
        }
          
        // bind parameters
        $statement->bind_param('s', $name);
           
        // execute
        $statement->execute();
        
        // get results
        $statement->store_result();

        if($statement->num_rows <= 0) {
            return null;
        }
        $statement->bind_result($id, $name, $standard);
        $statement->fetch();

        // create return object
        $option = new Option();
        $option->id = $id;
        $option->name = $name;
        $option->standard = $standard;
    
        $statement->close();
        $connection->close();

        return $option;
    }

    //-------------------------------------------------------------------------
    // dal_deleteOptionByName
    // 
    // Parameters:
    //  - BLAH: BLAH - BLAH
    //
    // Return: BLAH: BLAH - BLAH
    // 
    //-------------------------------------------------------------------------
    function dal_deleteOptionByName($option_name)
    {
        $connection = dal_createConnection();

        $query = "DELETE FROM option WHERE name= ?";

        // Prepare statement
        $statement = $connection->prepare($query);
        if($statement == false) {
            trigger_error('Wrong SQL: ' . $query . ' Error: ' . $connection->error, E_USER_ERROR);
        }
        
        // bind parameters
        $statement->bind_param('s', $option_name);

        // execute
        $statement->execute();

        $return_value = $statement->affected_rows;
        $statement->close();
        $connection->close();

        return $return_value;
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
    function dal_insertPollOption($poll_id, $option_id)
    {
        $connection = dal_createConnection();

        $query = "INSERT INTO poll_option (poll, option) VALUES (?, ?)";

        // Prepare statement
        $statement = $connection->prepare($query);
        if($statement == false) {
            trigger_error('Wrong SQL: ' . $query . ' Error: ' . $connection->error, E_USER_ERROR);
        }
        
        // bind parameters
        $statement->bind_param('ii', $poll_id, $option_id);

        // execute
        $statement->execute();

        $return_value;
        if($statement->affected_rows > 0) {
            $return_value = $statement->insert_id;
        }
        else {
            echo "No Results Found";
            $return_value = null;
        }
        $statement->close();
        $connection->close();

        return $return_value;
    }

    //-------------------------------------------------------------------------
    // dal_selectPollOption
    // 
    // Parameters:
    //  - BLAH: BLAH - BLAH
    //
    // Return: BLAH: BLAH - BLAH
    // 
    //-------------------------------------------------------------------------
    function dal_selectPollOption($id)
    {
        $connection = dal_createConnection();

        $query = "SELECT poll, option, option.name FROM poll_option, option WHERE poll_option.id = ? AND option.id = option";
         
        // prepare statement
        $statement = $connection->prepare($query);

        if($statement === false) {
              trigger_error('Wrong SQL: ' . $query . ' Error: ' . $connection->error, E_USER_ERROR);
        }
          
        // bind parameters
        $statement->bind_param('i', $id);
           
        // execute
        $statement->execute();
        
        // get results
        $statement->bind_result($poll_id, $option_id, $name);
        $statement->fetch();

        // create return object
        $option = new Option();
        $option->id = $id;
        $option->name = $name;
    
        $statement->close();
        $connection->close();

        return $option;
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
    function dal_selectOptionsByPoll($id)
    {
        $connection = dal_createConnection();

        $query = "SELECT poll_option.option, option.name FROM "
            . "poll_option, option WHERE poll_option.poll = ? AND poll_option.option = option.id";
         
        // prepare statement
        $statement = $connection->prepare($query);

        if($statement === false) {
              trigger_error('Wrong SQL: ' . $query . ' Error: ' . $connection->error, E_USER_ERROR);
        }

        // bind parameters
        $statement->bind_param('i', $id);
          
        // execute
        $statement->execute();
        
        // get results
        $statement->bind_result($option_id, $name);

        $options = array();

        while($statement->fetch()) {
            // create return object
            $option = new Option();
            $option->id = $option_id;
            $option->name = $name;

            array_push($options, $option);
        }

        $statement->close();
        $connection->close();

        return $options;
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

        $return_value;
        if($statement->affected_rows > 0) {
            $return_value = $statement->insert_id;
        }
        else {
            echo "No Results Found";
            $return_value = null;
        }
        $statement->close();
        $connection->close();

        return $return_value;
    }

    //-------------------------------------------------------------------------
    // dal_selectPollType
    // 
    // Parameters:
    //  - BLAH: BLAH - BLAH
    //
    // Return: BLAH: BLAH - BLAH
    // 
    //-------------------------------------------------------------------------
    function dal_selectPollType($id)
    {
        $connection = dal_createConnection();

        $query = "SELECT id, name FROM poll_type WHERE id = ?";
         
        // prepare statement
        $statement = $connection->prepare($query);

        if($statement === false) {
              trigger_error('Wrong SQL: ' . $query . ' Error: ' . $connection->error, E_USER_ERROR);
        }

        // bind parameters
        $statement->bind_param('i', $id);
          
        // execute
        $statement->execute();
        
        // get results
        $statement->bind_result($id, $name);

        $poll_types = array();

        $statement->fetch();

        // create return object
        $poll_type = new PollType();
        $poll_type->id = $id;
        $poll_type->name = $name;

        $statement->close();
        $connection->close();

        return $poll_type;
    }

    //-------------------------------------------------------------------------
    // dal_selectPollTypeByName
    // 
    // Parameters:
    //  - BLAH: BLAH - BLAH
    //
    // Return: BLAH: BLAH - BLAH
    // 
    //-------------------------------------------------------------------------
    function dal_selectPollTypeByName($name)
    {
        $connection = dal_createConnection();

        $query = "SELECT id, name FROM poll_type WHERE name = ?";
         
        // prepare statement
        $statement = $connection->prepare($query);

        if($statement === false) {
              trigger_error('Wrong SQL: ' . $query . ' Error: ' . $connection->error, E_USER_ERROR);
        }

        // bind parameters
        $statement->bind_param('s', $name);
          
        // execute
        $statement->execute();
        
        // get results
        $statement->bind_result($id, $name);

        $poll_types = array();

        $statement->fetch();

        // create return object
        $poll_type = new PollType();
        $poll_type->id = $id;
        $poll_type->name = $name;

        $statement->close();
        $connection->close();

        return $poll_type;
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
        $connection = dal_createConnection();

        $query = "SELECT id, name FROM poll_type";
         
        // prepare statement
        $statement = $connection->prepare($query);

        if($statement === false) {
              trigger_error('Wrong SQL: ' . $query . ' Error: ' . $connection->error, E_USER_ERROR);
        }
          
        // execute
        $statement->execute();
        
        // get results
        $statement->bind_result($id, $name);

        $poll_types = array();

        while($statement->fetch()) {
            // create return object
            $poll_type = new PollType();
            $poll_type->id = $id;
            $poll_type->name = $name;

            array_push($poll_types, $poll_type);
        }

        $statement->close();
        $connection->close();

        return $poll_types;
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
