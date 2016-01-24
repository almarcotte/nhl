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

    $ nhl --download-only --files /home/usr/alex/nhl/files --season 20152016
    
## Command line arguments

``--download-only``: Download the play by play files without parsing them
``--files <folder>``: Location of the play by play files. Subdirectories are seasons (ie. /my/folder/20152016/*.HTM)
``--force``: Download the files even if they are found in the download path (set with ``--files``)
``--quick``: Disable pauses during download (normally the downloader takes a short break every 10 files)
``--verbose``: Will output different notices/messages during the process.
