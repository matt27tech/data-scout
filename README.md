Search posts using search phrases using the console command.
<p>Deploys as a regular Laravel project</p>
<p>In the root folder of the project, run the command:</p>
<p>composer install</p>
<p>After deployment you can use the console command</p>
<p>'app:tg-get-updates{phrases* : The array of phrases}'</p>
<p>Phrases are the words you need to search for</p> 
<p>The first time you run it, it will ask for your account phone number and then you need to enter your Telegram login code into the console</p>
<p>Example command:</p>
<p>app:tg-get-updates test test2 test3</p>
<p>After entering the command it gets a list of all chats and goes through the list taking a maximum of 100 messages from each chat and checks for any messages.
maximum 100 messages from each and checks only those that have appeared in the last 5 minutes. 
last 5 minutes. The task is configured to run the command every 5 minutes.
<p>Task execution should be added to the cron. </p>
<p>So every 5 minutes new messages will be queried and a keyword search will be performed.
keyword search. Task keywords are specified in app/Console/Kernel.php</p>
