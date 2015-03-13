select poll.description, user.name, vote.date, option.name from poll, user, vote, option where vote.poll = poll.id and vote.user = user.id and vote.option = option.id;
