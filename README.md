# NHL Data Files Processing

Downloads and parses game data files from NHL.com.

## Installing

From git:

    $ git clone https://github.com/gnumast/nhl
    $ cd nhl
    $ composer install

## Basic Usage
    
    $ php bin/nhl.php --help
    
Download all play by play files for the 2015-2016 season

    $ nhl --download-only --files /home/alex/nhl/files --season 20152016
    
Parse the all play by play data files and print out a complete game log for each game:

    $ nhl --parse-only --files /home/alex/nhl/files --season 20152016 --verbose
    
Sample output:

    Processing /home/alex/nhl/files/20152016/PL020001.HTM
    001 [P1: 0:00] Start of Period at 7:20 EDT local time
    002 [P1: 0:00] MTL won faceoff in Neu. Zone - #14 PLEKANEC (MTL) vs #42 BOZAK (TOR)
    003 [P1: 0:14] Stop: HAND PASS
    004 [P1: 0:14] MTL won faceoff in Neu. Zone - #14 PLEKANEC (MTL) vs #42 BOZAK (TOR)
    005 [P1: 0:40] #11 GALLAGHER (MTL) hit #2 HUNWICK (TOR) in Off. Zone
    006 [P1: 0:51] Wrist shot ONGOAL by #28 BOYES (TOR) from Off. Zone (35 ft.)
    007 [P1: 0:58] Giveaway: #74 EMELIN (MTL) in Def. Zone
    008 [P1: 1:05] Snap shot ONGOAL by #43 KADRI (TOR) from Off. Zone (11 ft.)
    ...
    022 [P1: 2:45] Giveaway: #21 SMITH-PELLY (MTL) in Def. Zone
    023 [P1: 3:09] Goal (Wrist) by #67 PACIORETTY (MTL) from 38 ft. in Off. Zone. Assists: #76 SUBBAN (MTL)
    ...
    038 [P1: 5:11] #47 KOMAROV (TOR) 2 minutes for Boarding in Off. Zone drawn by #76 SUBBAN (MTL)
    039 [P1: 5:11] #21 SMITH-PELLY (MTL) 2 minutes for Roughing in Def. Zone drawn by #47 KOMAROV (TOR)
    ...
    113 [P1: 19:25] Slap shot ONGOAL by #36 HARRINGTON (TOR) from Off. Zone (62 ft.)
    114 [P1: 20:00] End of Period at 8:00 EDT local time
    115 [P2: 0:00] Start of Period at 8:19 EDT local time
    116 [P2: 0:00] TOR won faceoff in Neu. Zone - #14 PLEKANEC (MTL) vs #42 BOZAK (TOR)
    ...
    325 [P3: 19:29] TOR won faceoff in Neu. Zone - #14 PLEKANEC (MTL) vs #24 HOLLAND (TOR)
    326 [P3: 20:00] End of Period at 9:56 EDT local time
    327 [P3: 20:00] End of Game at 9:56 EDT local time

## Configuration

Most of the configuration can be done in ``config.ini``.

## Command line arguments

Below is a list of available command line arguments. Use ``--help`` to display a complete listing.

* ``--download-only``: Download the play by play files without parsing them
* ``--parse-only``: Parses the files in the path set with ``--files``) if any exist
* ``--files <folder>``: Location of the play by play files. Subdirectories are seasons (ie. /my/folder/20152016/*.HTM)
* ``--force``: Download the files even if they are found in the download path (set with ``--files``)
* ``--quick``: Disable pauses during download (normally the downloader takes a short break every 10 files)
* ``--verbose``: Will output different notices/messages during the process.
* ``--list <type>``: Lists all the available implementations for a given type (see ``--list exporters``)

All command line arguments will overwrite the configuration file.

## Todo

## Parser

* Store the last parsed file somewhere so we have a way to only parse new files. This is useful if the downloader runs daily to grab new game data
and we want to parse only that day's files.

### Writers
The next step is to implement the output interface that will allow to write the data to different format (SQL, CSV mostly). I'm looking for a database of every team and roster
from the past couple of years that I could use to go with this (something similar to Lahman's baseball database), if I can't find anything I might build it myself.

* CSV is partially working. To use, set exporter->export setting in ``config.ini`` to "csv" and run the tool.

### Command line
The command line tool needs work.

Pull requests welcomed! If you have anything question, I'm [@Hamburghost](https://twitter.com/hamburghost) on Twitter.