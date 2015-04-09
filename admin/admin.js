// TODO: Add a notes field to polls


// Globals
var development = false;
var env = "dev";

var username;
var current_poll;
var max_votes = 1;


// Load function, have to do it this way for Google Charts
//google.load('visualization', '1.0', {'packages':['corechart']});
//google.setOnLoadCallback(function() {
//    $(function() {
//        on_load();
//    });
//});

// Old way of loading from before using Google Charts
$(document).ready(function() {
    on_load();
});

//******************************************************************************
//
//
//******************************************************************************
function on_load()
{
    // Set click triggers for options menu
    $("#option-current-poll").click(init_current_poll);
    $("#option-create-poll").click(init_create_poll);
    set_click_trigger();
    if(development) {
        write_debug("Development Environment");

        username = "test";
        env = "dev";
        //request_current_poll();
    }
    else {
        write_debug("Production Environment");
        env = "prod";
        //request_user_info();
    }
    $("#option-current-poll").trigger("click");
}

//******************************************************************************
//
//
//******************************************************************************
function init_current_poll()
{
    // Clear main window
    $("#admin-main").html("");
    
    $("#admin-main").append("<h2>Current Poll</h2>");

    request_current_poll();
}

//******************************************************************************
//
//
//******************************************************************************
function init_create_poll()
{
    // Clear main window
    $("#admin-main").html("");
    
    $("#admin-main").append("<h2>Create Poll</h2>");

    request_options(draw_create_poll);
}

//******************************************************************************
//
//
//******************************************************************************
function draw_create_poll(options_response)
{
    var options = options_response;

    $("#admin-main").append("<div id=\"admin-main-body\"></div>");
    $("#admin-main-body").append("<p>Note: This will auto-close any open polls.</p>");
    $("#admin-main-body").append("<table id=\"admin-input-table\"></table>");

    // Name
    $('#admin-input-table').append($('<tr></tr>').append($('<td></td>').html("<label for=\"admin-input-poll-name\">Name</label")).append(
        "<input type=\"text\" name=\"poll-name\" id=\"admin-input-poll-name\" />"));

    // Description
    $('#admin-input-table').append($('<tr></tr>').append($('<td></td>').html("<label for=\"admin-input-poll-description\">Description</label")).append(
        "<input type=\"text\" name=\"poll-description\" id=\"admin-input-poll-description\" />"));

    // Start
    $('#admin-input-table').append($('<tr></tr>').append($('<td></td>').html("<label for=\"admin-input-poll-start\">Start</label")).append(
        "<input type=\"text\" name=\"poll-start\" id=\"admin-input-poll-start\" />"));
    $('#admin-input-poll-start').datepicker();

    // End
    $('#admin-input-table').append($('<tr></tr>').append($('<td></td>').html("<label for=\"admin-input-poll-end\">End</label")).append(
        "<input type=\"text\" name=\"poll-end\" id=\"admin-input-poll-end\" />"));
    $('#admin-input-poll-end').datepicker();

    // Poll Type
    $('#admin-input-table').append($('<tr></tr>').append($('<td></td>').html("<label for=\"admin-input-poll-type\">Poll Type</label")).append(
        "<select name=\"poll-type\" id=\"admin-input-poll-type\"></select>"));
    $('#admin-input-poll-type').append('<option value=\"multivote\">Multi-vote</option>');
    $('#admin-input-poll-type').append('<option value=\"ranked\">Ranked</option>');

    // Max-Votes
    $('#admin-input-table').append($('<tr></tr>').append($('<td></td>').html("<label for=\"admin-input-poll-max-votes\">Max-Votes</label")).append(
        "<input type=\"text\" name=\"poll-max-votes\" id=\"admin-input-poll-max-votes\" />"));

    // Start
    $('#admin-input-table').append($('<tr></tr>').append($('<td></td>').html("<label for=\"admin-input-poll-options\">Options</label")).append(
        "<select name=\"poll-optinos\" id=\"admin-input-poll-options\" multiple=\"multiple\"></select>"));

    for(var i = 0; i < options.length; ++i) {
        $('#admin-input-poll-options').append('<option value=\"' + options[i].name + '\">' + options[i].name + '</option>');
    }
    $('#admin-main-body').append($('<p></p>').append($('<input type=\"submit\" id=\"admin-create-poll-button\" value=\"Create Poll\" />')));

    $('#admin-create-poll-button').click(function(event) {
        write_debug("Creating Poll");

        $.ajax({
            url: "http://wulph.com/cw-vote/cw-service.php",
            data: {
                action: "create_poll",
                name: $('#admin-input-poll-name').val(),
                description: $('#admin-input-poll-description').val(),
                start: $('#admin-input-poll-start').val(),
                end: $('#admin-input-poll-end').val(),
                type: $('#admin-input-poll-type').val(),
                max_votes: $('#admin-input-poll-max-votes').val(),
                options: JSON.stringify($('#admin-input-poll-options').val()),
                env: env,
            },
            type: "POST",
            //contentType: "application/json",
            success: function(response) {
                $("#admin-debug").append("<br>" + response);
                $("#admin-main-body").html("");
                if(response == "success") {
                    $("#admin-main-body").append("<p>Poll created successfully.</p>");
                }
                else {
                    $("#admin-main-body").append("<p>Error creating poll, tell WolfBro.</p>");
                }
            },
            error: error_func
        });
    });

}

//******************************************************************************
//
//
//******************************************************************************
function request_options(callback)
{
    write_debug("Requesting Options");
    $.ajax({
        url: "http://wulph.com/cw-vote/cw-service.php",
        data: {
            action: "get_options", 
            env: env
        },
        type: "GET",
        dataType: "json",
        success: callback,
        error: error_func
    });
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
        //request_current_poll();
    });
}

//******************************************************************************
//
//
//******************************************************************************
function request_current_poll()
{
    write_debug("Requesting current poll");
    //$.when(
        $.ajax({
            url: "http://wulph.com/cw-vote/cw-service.php",
            data: {
                action: "get_current_poll", 
                env: env
            },
            type: "GET",
            dataType: "json",
            success: draw_current_poll,
            error: error_func
        });//,
        //$.ajax({
        //    url: "http://wulph.com/cw-vote/cw-service.php",
        //    data: {
        //        action: "get_votes_current",
        //        username: username,
        //        env: env
        //    },
        //    type: "GET",
        //    //dataType: "json",
        //    error: error_func
        //})
    //).then(draw_vote_view);

    //results();
    write_debug("Init");
}

//******************************************************************************
//
//
//******************************************************************************
function draw_current_poll(poll_response)
{
    //var poll = poll_response[0];
    var poll = poll_response;

    current_poll = poll;

    if(poll == null) {
        $("#admin-main").append("<h2>There is no current poll</h2>");
    }
    else {
        //$("#admin-main").append(JSON.stringify(poll, null, 4));

        $("#admin-main").append("<h2>" + poll.name + "</h2>\n");
        $("#admin-main").append("<h3>" + poll.description + "</h3>\n");
        $("#admin-main").append("<h3>Start: " + poll.start_date + "</h3>\n");
        $("#admin-main").append("<h3>End: " + poll.end_date + "</h3>\n");
        $("#admin-main").append("<h3>Poll Type: " + poll.type.name + "</h3>\n");
        if(poll.type.name == "multivote") {
            $("#admin-main").append("<h3>Max Votes: " + poll.max_votes + "</h3>\n");
        }
        $("#admin-main").append("<ul id=\"admin-option-view-list\"></ul>\n");

        for(i = 0; i < poll.options.length; i++) {
            $("#admin-option-view-list").append("<li id=\"option-" + poll.options[i].id + "\">" + poll.options[i].name + "</li>\n");
        } $("#admin-main").append("</ul>\n"); 
        // Disable text selection
        //$("#admin-option-view-list").disableSelection();
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
        //dataType: "json",
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
        var count = 1;
        
        $("#vote_list > li").each(function() {
            var this_option = Object;
            this_option.name = $(this).html();
            this_option.value = count;
            options.push(this_option);
            ++count;
        });
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
        if($(this).hasClass("selected")) {
            $("li").removeClass("selected");
             
        }
        else {
            $("li").removeClass("selected");
            $(this).addClass("selected");
        }
    });
}
//******************************************************************************
//
//
//******************************************************************************
function write_debug(message) 
{
    $("#admin-debug").append("<p>" + message + "</p>");
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

