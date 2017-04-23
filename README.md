Shazam to Spotify.
=======

Shazam Exporter / Spotify Importer.

Some time ago, Shazam went through some major redesign and for some reason, they removed the feature that allowed users to export their tags to a HTML file. My Shazam's panel also doesn't show the place and date of your Shazams anymore (why, Shazam?), so I decided to create a script to export your Shazam data, so you can do whatever you want with it.

In this repo, there are two php files. `shazam_export.php` uses a Shazam auth token and exports a json file with all of the data from your shazam tags, including location, timezone, date and track metadata such as title, artist, links to spotify/apple music, etc...

The second script `spotify_upload.php` takes a spotify playlist, and a tags_tracks file to add the songs in your file to that playlist. Be aware that some songs might not have a spotify uri. So these songs won't be added yo the playlist.