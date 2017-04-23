<?php

require __DIR__ . '/vendor/autoload.php';

use GuzzleHttp\Client;
use Dotenv\Dotenv;

$client = new Client();
$dotenv = new Dotenv(__DIR__);

$spotifyToken = getenv('SPOTIFY_TOKEN');
$fileName = getenv('TAGS_TRACKS_FILE');

echo "Reading from file...\n";

$tagData = readJsonFromFile($fileName);

$spotifyData = [];

echo "Parsing data...\n";

// Parse the data to get the most important pieces.
foreach ($tagData as $data) {
    $track = [];

    $track['tag'] = $data['tag'];

    $track['track']['track'] = $data['track']['heading']['title'];
    $track['track']['artist'] = $data['track']['heading']['subtitle'];

    if(isset($data['track']['streams']['spotify']['actions'][0]['uri'])) {
        $track['track']['uri'] = $data['track']['streams']['spotify']['actions'][0]['uri'];
        array_push($spotifyData, $track);
    }
}

echo "Sorting data...\n";

// Sort track data by tag date.
usort($spotifyData, function($a, $b) {
    return $a['tag']['timestamp'] <=> $b['tag']['timestamp'];
});

print_r($spotifyData);

// Prints track data from each track...
foreach ($spotifyData as $data) {
    echo $data['track']['track'] . "\n";
    echo $data['track']['artist'] . "\n";

    if (strpos($data['track']['uri'], 'spotify:track:') !== 0) {
        $data['track']['uri'] . "\n";
    }

    echo gmdate('D. M j, Y', $data['tag']['timestamp'] / 1000) . "\n";
    echo $data['tag']['timestamp'] . "\n";
    echo "----------------------------------\n";
}

$spotifyUris = [];
$notFound = [];

echo "Finding spotify URIs...";

// Get the tracks that were found in spotify...
foreach ($spotifyData as $data) {
    if (strpos($data['track']['uri'], 'spotify:track:') === 0) {
        array_push($spotifyUris, $data['track']['uri']);
    } else {
        array_push($notFound, $data['track']['uri']);
    }
}

echo "Removing duplicate URIs...";
// Remove duplicates...
$spotifyUris = array_unique($spotifyUris);

print_r($spotifyUris);
print_r($notFound);

// addTracksToPlaylist($spotifyUris, $spotifyToken, $client);

// Reads JSON data from file, returns an array.
function readJsonFromFile($fileName) {
    $file = fopen($fileName, 'r') or die ('Unable to open file.');
    $data = fread($file, filesize('tags_tracks_1492407864.json'));
    fclose($file);
    return json_decode($data, true);
}

// Add all tracks to spotify.
function addTracksToPlaylist($spotifyUris, $spotifyToken, $client) {
    $maxTracksPerRequest = 100; // The max amount of tracks that the spotify API receives per request (currently 100.)
    $endpoint = 'https://api.spotify.com/v1/users/' . getenv('SPOTIFY_USERNAME') . '/playlists/' . getenv('SPOTIFY_PLAYLIST_ID') . '/tracks';

    echo "Saving your playlist...";

    for ($i=0; $i < count($spotifyUris); $i += $maxTracksPerRequest) {
        $slicedTracks = array_slice($spotifyUris, $i, $maxTracksPerRequest);

        echo ".";

        $client->request('POST', $endpoint, [
            'headers' => [
                'Authorization: ' => 'Bearer ' . $spotifyToken,
            ],
            'json' => [
                'uris' => $slicedTracks,
                'position' => $i
            ]
        ]);
    }

    echo ".\n";
}
