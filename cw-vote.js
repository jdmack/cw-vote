function request_init()
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
                action: "get_vote_current",
                username: $("#username").val()
            },
            type: "GET",
            dataType: "json",
            error: error_func
        })/*,
        $.ajax({
            url: "http://www.enjin.com/api/v1/api.php",
            data: {
                method: "User.get",
            },
            type: "POST",
            dataType: "json",
            error: error_func
        })*/
    ).then(drawVoteView);
}
