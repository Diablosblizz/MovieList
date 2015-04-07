# MovieList

MovieList started out as a simple PHP, MySQL and Javascript based project that allows you to keep track of both your physical and digital movies. As time progressed, more and more features became required for this project. Please see below!

## Features
<ul>
<li>Full Plex Integration</li>
<ul>
<li>Play, Pause and Stop your favourite Plex movies on your favourite Plex devices</li>
<li>Fully supports Roku playback</li>
<li>Movies will automatically import to MovieList once they are added to Plex - always be in sync</li>
</ul>
<li>Fully jQuery Based - meaning it will work on any device with Javascript support (tested on iOS and Android 4.2.2)</li>
<li>Freedom on your Metadata, edit it to the way you like it</li>
<li>Duplicate Detection</li>
</ul>

## Usage
Extract the archive to your web server, preferably one that is located on the same server as Plex (although not required). Execute movielist.sql on your database. Edit configuration.php for the following - <b>Please ensure all strings are encapsulated in double quotes</b>:
* ``` $db_username = Your Database Username ```
* ``` $db_password = Your Database Pass for the User specified above ```
* ``` $dsn = "mysql:host=Your MySQL Server's IP Address;dbname=Your Database Name" ```
* ``` $plexIP = Your Plex Server's IP Address (can be localhost) ```
* ``` $plexPort = Your Plex Server's Port ```
* ``` $plexServID = Your Plex's Machine Identifier - this can be found by going to http://plexip:plexport/ then grabbing the "Machine Identifier" ```
