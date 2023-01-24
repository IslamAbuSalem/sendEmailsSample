To apply the background process I implemented two approaches
-The first one using Bernard Queue ==> please check indexUsingQueue.php
created two classes the first one (SendEmailJob) that uses PHP mail function, the second one (SendEmailSESJob) for SES
-The second using exec() function ==> indexUsingExecCommand.php

To send emails we can use
1-mail() PHP function which has some limitations and potential drawbacks
2-Amazon SES which is paid service

Notes:
I ignored some best practices like saving SES keys in the config file
also, I didn't implement the DB part to fetch emails and save logs

Bernard Queue requires some steps for installation on the server
Installation instructions
sudo apt install supervisor
sudo systemctl status supervisor
sudo nano /etc/supervisor/conf.d/queue.conf

Then add the following to the file, Save and exit.

[program:queue]
command=/usr/local/bin/php <<consumer.php Path>>
autostart=true
autorestart=true
stderr_logfile=/var/log/queue.err.log
stdout_logfile=/var/log/queue.out.log

supervisorctl update
supervisorctl restart all
