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

        $return_value;
        if($statement->affected_rows > 0) {
            $return_value = $statement->insert_id;
        }
        else {
            echo "No Results Found";
            $return_value = null;
        }
        $statement->close();

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

        $query = "SELECT description, type.id, start_date, end_date FROM poll WHERE id = ?";
         
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
        $statement->bind_result($description, $type_id, $start_date, $end_date);
        $statement->fetch();

        // create return object
        $poll = new Poll();
        $poll->id = $id;
        $poll->description = $description;
        $poll->type = dal_selectPollType($type_id);
        $poll->start_date = $start_date;
        $poll->end_date = $end_date;
        $poll->options = dal_selectPollOptionsByPoll($id);

        $statement->close();
    
        return $poll;

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

        $query = "SELECT id, description, type.id, start_date, end_date FROM poll WHERE start_date > ? AND end_date < ?";
         
        // prepare statement
        $statement = $connection->prepare($query);

        if($statement === false) {
              trigger_error('Wrong SQL: ' . $query . ' Error: ' . $connection->error, E_USER_ERROR);
        }

        $current_date = date("c"); 
        // bind parameters
        $statement->bind_param('ss', $current_date, $currnet_date);
           
        // execute
        $statement->execute();
        
        // get results
        $statement->bind_result($description, $type_id, $start_date, $end_date);
        $statement->fetch();

        // create return object
        $poll = new Poll();
        $poll->id = $id;
        $poll->description = $description;
        $poll->type = dal_selectPollType($type_id);
        $poll->start_date = $start_date;
        $poll->end_date = $end_date;
        $poll->options = dal_selectPollOptionsByPoll($id);

        $statement->close();
    
        return $poll;
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

        $query = "SELECT id FROM user WHERE name = ?";
         
        // prepare statement
        $statement = $connection->prepare($query);

        if($statement === false) {
              trigger_error('Wrong SQL: ' . $query . ' Error: ' . $connection->error, E_USER_ERROR);
        }
          
        // bind parameters
        $statement->bind_param('i', $name);
           
        // execute
        $statement->execute();
        
        // get results
        $statement->bind_result($id);
        $statement->fetch();

        // create return object
        $user = new User();
        $user->id = $id;
        $user->name = $name;
    
        $statement->close();

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

        return $votes;
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
    function dal_selectVoteByPollAndUser($poll_id, $user_id)
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
        
        // get results
        $statement->bind_result($id, $poll_id, $user_id, $date, $option_id, $value);

        $statement->fetch();

        // create return object
        $vote = new Vote();
        $vote->id = $id;
        $poll = dal_selectPoll($poll_id);
        $vote->user = dal_selectUser($user_id);
        $vote->date = $date;
        $vote->option = dal_selectOption($option_id);
        $vote->value = $value;

        $statement->close();

        return $vote;
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

        $query = "SELECT name FROM option WHERE id = ?";
         
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
        $option = new Option();
        $option->id = $id;
        $option->name = $name;
    
        $statement->close();

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

        $query = "SELECT id, name FROM option";
         
        // prepare statement
        $statement = $connection->prepare($query);

        if($statement === false) {
              trigger_error('Wrong SQL: ' . $query . ' Error: ' . $connection->error, E_USER_ERROR);
        }
          
        // execute
        $statement->execute();
        
        // get results
        $statement->bind_result($id, $name);

        $options = array();

        while($statement->fetch()) {
            // create return object
            $option = new Option();
            $option->id = $id;
            $option->name = $name;

            array_push($options, $option);
        }

        $statement->close();

        return $options;
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

        $return_value;
        if($statement->affected_rows > 0) {
            $return_value = $statement->insert_id;
        }
        else {
            echo "No Results Found";
            $return_value = null;
        }
        $statement->close();

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

        $query = "SELECT poll, option FROM poll_option WHERE id = ?";
         
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
        $statement->bind_result($poll_id, $option_id);
        $statement->fetch();

        // create return object
        $poll_option = new Option();
        $poll_option->id = $id;
        $poll_option->poll = dal_selectPoll($poll_id);
        $poll_option->option = dal_selectOption($option_id);
    
        $statement->close();

        return $poll_option;
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
    function dal_selectPollOptionsByPoll($id)
    {
        $connection = dal_createConnection();

        $query = "SELECT id, option FROM poll_option WHERE poll = $id";
         
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
        $statement->bind_result($poll_option_id, $option_id);

        $poll_options = array();
        $poll = dal_selectPoll($id);

        while($statement->fetch()) {
            // create return object
            $poll_option = new Option();
            $poll_option->id = $poll_option_id;
            $poll_option->poll = $poll;
            $poll_option->option = dal_selectOption($option_id);

            array_push($poll_options, $poll_option);
        }

        $statement->close();

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

        return $return_value;
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
