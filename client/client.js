// TODO: Add a notes field to polls


// Globals
var development = true;
var env = "dev";

var locked = false;
var username;
var current_poll;
var max_votes = 1;


// Load function, have to do it this way for Google Charts
google.load('visualization', '1.0', {'packages':['corechart']});
google.setOnLoadCallback(function() {
    $(function() {
        on_load();
    });
});

// Old way of loading from before using Google Charts
//$(document).ready(function() {
//    on_load();
//});

//******************************************************************************
//
//
//******************************************************************************
function on_load()
{
    if(development) {
        write_debug("Development Environment");

        username = "test";
        env = "dev";
        request_poll_info();
    }
    else {
        write_debug("Production Environment");
        env = "prod";
        request_user_info();
    }
}

//******************************************************************************
//
//
//******************************************************************************
function request_user_info()
{
    var request = {
        "jsonrpc": "2.0",
        "id": Math.round(Math.random() * (999999 - 100000) + 100000),
        "method": "User.get",
        "params": {
        }
    };
    write_debug("Requesting Enjin User Info");
    $.post("/api/v1/api.php", JSON.stringify(request), function(response) {
        if(response.result) {
            //username = user_response[0].result.username;
            username = response.result.username;
            write_debug("User Received: " + username);
        }
        else {
            username = "MISSING";
        }
        request_poll_info();
    });
}

//******************************************************************************
//
//
//******************************************************************************
function request_poll_info()
{
    $.when(
        $.ajax({
            url: "http://wulph.com/cw-vote/cw-service.php",
            data: {
                action: "get_current_poll", 
                env: env
            },
            type: "GET",
            dataType: "json",
            error: error_func
        }),
        $.ajax({
            url: "http://wulph.com/cw-vote/cw-service.php",
            data: {
                action: "get_votes_current",
                username: username,
                env: env
            },
            type: "GET",
            dataType: "json",
            error: error_func
        })
    ).then(draw_vote_view);

    results();
    write_debug("Init");
}

//******************************************************************************
//
//
//******************************************************************************
function draw_vote_view(poll_response, vote_response)
{
    var poll = poll_response[0];
    var votes = vote_response[0];

    current_poll = poll;
    max_votes = poll.max_votes;

    write_debug("username: " + username);
    //$("#debug").append("votes: " + JSON.stringify(votes));

    if(poll == "null") {
        $("#main").append("<h2>There is no current poll</h2>");
    }
    else {
        $("#main").append("<h1>" + poll.name + "</h1>\n");
        $("#main").append("<h2>" + poll.description + "</h2>\n");
        $("#main").append("<h3>Start: " + poll.start_date + "</h3>\n");
        $("#main").append("<h3>End: " + poll.end_date + "</h3>\n");

        // Multivote 
        if(poll.type.name == "multivote") {
            $("#main").append("<p>Select " + max_votes + " options</p>");
            $("#main").append("<ul id=\"vote_list\"></ul>\n");

            for(i = 0; i < poll.options.length; i++) {
                $("#vote_list").append("<li id=\"option-" + poll.options[i].id + "\">" + poll.options[i].name + "</li>\n");
            }

            $("#main").append("</ul>\n");

            // "select" the options already voted for
            if(votes != null) {
                for(i = 0; i < votes.length; ++i) {
                    $("#option-" + votes[i].option.id).addClass("selected");
                }
            }
            set_click_trigger();
        }
        // Ranked
        else if(poll.type.name == "ranked") {
            $("#main").append("<p>Rank the following items via click and drag.</p>");
            $("#main").append("<ul id=\"vote_list\"></ul>\n");

            for(i = 0; i < poll.options.length; i++) {
                $("#vote_list").append("<li id=\"option-" + poll.options[i].id + "\">" + poll.options[i].name + "</li>\n");
            }

            $("#main").append("</ul>\n");

            // TODO: Reorder the items based on previous votes
            
            $("#vote_list").sortable();
        }

        // Disable text selection
        $("#vote_list").disableSelection();

        $("#main").append("<p><button id=\"vote_button\" onclick=\"cast_votes()\">Cast Vote</button></p>\n");

        if(votes != null) {
            $("#main").append("<div id=\"message\"><p>You have already voted in this poll. Click below to recast your vote.</p>"
                + "<button id=\"revote_button\" onclick=\"revote()\">Re-vote</button></div>\n");
                lock_vote();
        }
        else {
            $("#main").append("<div id=\"message\"></div>\n");
        }
    }

}

//******************************************************************************
//
//
//******************************************************************************
function results()
{
    // Request results data
    $.ajax({
        url: "http://wulph.com/cw-vote/cw-service.php",
        data: {
            action: "get_current_results",
            env: env
        },
        type: "GET",
        dataType: "json",
        success: draw_results,
        error: error_func
    });

}
//******************************************************************************
//
//
//******************************************************************************
function draw_results(response)
{
    if(response == null) {
        $('#results').append("<p>No results to show.</p>");
    }
    else {
        // TODO: Determine how to display results for ranked poll
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Faction');
        data.addColumn('number', 'Votes');

        for(var key in response) {
            data.addRow([key, response[key]]);
        }

        var options = {
            'title':'Poll Results',
            'width' : 500,
            'height': 500,
            'backgroundColor' : '#EEEEEE'
        };
        var chart = new google.visualization.ColumnChart(document.getElementById('results'));
        chart.draw(data, options);
    }
}

//******************************************************************************
//
//
//******************************************************************************
function revote()
{
    $("#message").html("");
    unlock_vote();
}

//******************************************************************************
//
//
//******************************************************************************
function lock_vote()
{
    //$("#debug").html("lock");
    $("#vote_button").prop("disabled", true);
    if(current_poll.type.name == "multivote") {
        $("li.selected").toggleClass("locked");
    }
    else if(current_poll.type.name == "ranked") {
        $("#vote_list").sortable("disable");
    }
    locked = true;
}

//******************************************************************************
//
//
//******************************************************************************
function unlock_vote()
{
    $("#debug").html("unlock");
    $("#vote_button").prop("disabled", false);

    if(current_poll.type.name == "multivote") {
        $("li.selected").toggleClass("locked");
    }
    else if(current_poll.type.name == "ranked") {
        $("#vote_list").sortable("enable");
    }

    locked = false;
}

//******************************************************************************
//
//
//******************************************************************************
function cast_votes()
{
    // TODO: Cast votes for ranked
    $("#message").html("");
    var options = Array();

    if(current_poll.type.name == "multivote") {

        $("li.selected").each(function() {
            options.push($(this).html());  
        });

        if(options.length != max_votes) {
            $("#message").append("<p>You must select " + max_votes + " options.</p>");
            return;
        }

        $("#debug").html("cast vote: " + options.join());
    }
    else if(current_poll.type.name == "ranked") {
        var count = $('#vote_list > li').length;
        
        $("#vote_list > li").each(function() {
            var this_option = {
                name: $(this).html(),
                value: count
            };
            options.push(this_option);
            --count;
        });
        write_debug(JSON.stringify(options));
    }

    $.ajax({
        url: "http://wulph.com/cw-vote/cw-service.php",
        data: {
            action: "cast_votes",
            username: username,
            options: JSON.stringify(options),
            env: env,
            poll_type: current_poll.type.name
        },
        type: "POST",
        //contentType: "application/json",
        success: function(response) {
            $("#debug").append("<br>" + response);
            $("#message").html("");
            if(current_poll.type.name == "multivote") {
                $("#message").append("<p>Your vote for " + options.join(', ') + " was submitted.</p>");
            }
            else {
                $("#message").append("<p>Your vote was submitted.</p>");
            }
            $("#message").append("<p>Click below to recast your vote.</p>"
                + "<button id=\"revote_button\" onclick=\"revote()\">Re-vote</button></div>\n");
            results();

        },
        error: error_func
    });

    lock_vote();
}

//******************************************************************************
//
//
//******************************************************************************
function set_click_trigger() 
{
    $("li").click(function() {
        //write_debug("max_votes: " + max_votes);
        if(locked) { return; }
        if(max_votes == 1) {
            if($(this).hasClass("selected")) {
                $("li").removeClass("selected");
                 
            }
            else {
                $("li").removeClass("selected");
                $(this).addClass("selected");
            }
        }
        else {
            if($(this).hasClass("selected")) {
                $(this).toggleClass("selected");
            }
            else if($("li.selected").size() < max_votes) {
                $(this).toggleClass("selected");
            }
        }
    });
}
//******************************************************************************
//
//
//******************************************************************************
function write_debug(message) 
{
    $("#debug").append("<p>" + message + "</p>");
}
//******************************************************************************
//
//
//******************************************************************************
function error_func(xhr, status, errorThrown)
{
    write_debug("ERROR: " + status + " - " + errorThrown);
    console.log("Error: " + errorThrown);
    console.log("Status: " + status);
    console.dir(xhr);
}
