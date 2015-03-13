// Globals
var development = true;

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
        request_poll_info();
    }
    else {
        write_debug("Production Environment");
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
            },
            type: "GET",
            dataType: "json",
            error: error_func
        }),
        $.ajax({
            url: "http://wulph.com/cw-vote/cw-service.php",
            data: {
                action: "get_votes_current",
                username: username
            },
            type: "GET",
            dataType: "json",
            error: error_func
        })
    ).then(drawVoteView);

    results();
    write_debug("Init");
}

//******************************************************************************
//
//
//******************************************************************************
function drawVoteView(poll_response, vote_response)
{
    var poll = poll_response[0];
    var votes = vote_response[0];
    current_poll = poll;
    max_votes = poll.max_votes;
    write_debug("username: " + username);
    $("#debug").append("votes: " + JSON.stringify(votes));

    $("#main").append("<h2>" + poll.description + "</h2>\n");
    $("#main").append("<h3>Start: " + poll.start_date + "</h3>\n");
    $("#main").append("<h3>End: " + poll.end_date + "</h3>\n");
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

    $("#main").append("<p><button id=\"vote_button\" onclick=\"cast_votes()\">Cast Vote</button></p>\n");

    if(votes != null) {
        $("#main").append("<div id=\"message\"><p>You have already voted in this poll. Click below to recast your vote.</p>"
            + "<button id=\"revote_button\" onclick=\"revote()\">Re-vote</button></div>\n");
            lock_vote();
    }
    else {
        $("#main").append("<div id=\"message\"></div>\n");
    }

    set_click_trigger();
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
        },
        type: "GET",
        dataType: "json",
        success: drawResults,
        error: error_func
    });

}
//******************************************************************************
//
//
//******************************************************************************
function drawResults(response)
{
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
    $("li.selected").toggleClass("locked");
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
    $("li.selected").toggleClass("locked");
    locked = false;
}

//******************************************************************************
//
//
//******************************************************************************
function cast_votes()
{
    $("#message").html("");
    var data = Object;
    var options = Array();
    data.username = username;

    $("li.selected").each(function() {
        options.push($(this).html());  
    });

    if(options.length != max_votes) {
        $("#message").append("<p>You must select " + max_votes + " options.</p>");
        return;
    }

    data.options = options;
    data.action = "cast_votes";

    $("#debug").html("cast vote: " + options.join());

    $.ajax({
        url: "http://wulph.com/cw-vote/cw-service.php",
        data: {
            action: "cast_votes",
            username: username,
            options: JSON.stringify(options)
        },
        type: "POST",
        //contentType: "application/json",
        success: function(response) {
            $("#debug").append("<br>" + response);
            $("#message").html("");
            $("#message").append("<p>Your vote for " + options.join(', ') + " was submitted.</p>");
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
    alert("Request Problem");
    console.log("Error: " + errorThrown);
    console.log("Status: " + status);
    console.dir(xhr);
}

