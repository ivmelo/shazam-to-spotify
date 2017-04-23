Shazam to Spotify.
=======

Shazam Exporter / Spotify Importer.

Some time ago Shazam went through a major redesign. During that, they removed the feature that allowed users to export their tags to a HTML file. My Shazam's web panel also doesn't show the location and date of your tags anymore (why, Shazam?). With that in mind, I decided to create a script to export your Shazam data, so you can do whatever you want with it.

In this repo, there are two php files. `shazam_export.php` uses a Shazam auth token and user ID and exports a JSON file with all of the data from your Shazam tags such as track ID, geolocation (lat, lng), timezone and date. It also gets track metadata for all of your tags including links to streaming platforms such as Spotify, Google Play, Apple Music, etc...

The second script `spotify_upload.php` takes the JSON file that was previously generated and adds all  tracks to a Spotify Playlist. Be aware that some songs might not have a Spotify URI returned by Shazam. So these songs won't be added to your playlist.

Running the script:
- Clone the repo and cd into the folder;
- Run `composer install`;
- Rename the `.env.example` file to `.env`;
- Edit the `.env` file to add your credentials;
- Run `shazam_export.php`.

To get a Spotify token (to add tracks to your playlist), you can use the Spotify [WEB Api Console](https://developer.spotify.com/web-api/console/post-playlist-tracks/). Just click the `GET OAUTH TOKEN` button, check all of the relevant scopes and copy your token.

It is a little bit trickier get a Shazam token and your user ID. The way I did it was by inspecting the HTTP requests that the Shazam web app does. I used [mitmproxy](https://mitmproxy.org) to do that.
