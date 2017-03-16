## Simple Blood Bowl League Manager

This is a very basic, just-for-small-private-leagues manager for Blood Bowl. It features:

  - Seasons
  - Game programming and result recording
  - Team and player tracking
  - Coach tracking
  - Localization (English and Spanish are included, others are easily added)
  - Automatic round robin game programming
  - Basic stats
  - Invites

It does not try to replace OBBLM, NAFLM, FEBB, nor any of these big suites with many stats, different leagues, etc.

You can find an example in my own league: http://grimcup.blepstudio.es/.

### Requirements

SBBLM only needs a web server with PHP (>= 5.4) & SQLite support (it should not be difficult to find such a server these days).
Please note I have not tested it extensively with every PHP version out there, just PHP7.1 on Lighttpd and PHP5.5 on Apache,
but there is no reason why it wouldn't work on any other platform. If it doesn't, please file a bug.

### Install

  1. Upload the files to your server. Please note that the public directory is **www**, not the project root. I am not good
  explaining things, but if you have any problems with this file me a bug and I'll do my best to help you.
  2. Make sure folders *www/tmp* and *www/img* are writable by the server.
  3. Point your browser towards your URL and follow the directions (this takes a while, so be patient)

### Notes

SBBLM comes with the LRB6 skills in Spanish. It is not very complicated to add new rulesets, but there might be legal 
complications for their distribution. However, since the changes are small, you can have a look at *rules/<files>.csv*
and change them wherever you want **prior to install**. I might include an English version soon, depending on requests.

If you wish to translate the app, you can have a look into the *dict/* folder. It is quite self-explanatory.

Re-install is as easy as deleting the database *db/db.sql* and going to the *install url* again.

This is a work in progress. I wrote it to manage my first Blood Bowl league, and answer to my requirements. I might add 
new features as I see them useful or necessary. I also accept requests, although their inclusion might depend on factors.


### Still missing
  * Enforce game number per round limitations
  * Implement some kind of team transition between seasons (I need to see how the off-season rules work in 
  Death Zone Season 1 first, to design something generic enough).
