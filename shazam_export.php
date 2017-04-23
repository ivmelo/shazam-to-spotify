<?php

require __DIR__ . '/vendor/autoload.php';

use GuzzleHttp\Client;

$client = new Client();
$dotenv = new Dotenv\Dotenv(__DIR__);

// Load from .env file
$dotenv->load();
$endpoint = 'https://amp.shazam.com/shazam/v1/en-US/BR/android/-/installation/' . getenv('SHAZAM_ID') . '/tagevents';
$apiKey = getenv('SHAZAM_KEY');

$allTags = [];

// Get tags...
echo 'Getting tags...';

do {
    $tags = getTags($endpoint, $apiKey, $client);

    foreach ($tags['events'] as $tag) {
        array_push($allTags, $tag);
    }

    echo '.';

    $endpoint = isset($tags['next']['url']) ? $tags['next']['url'] : null;
} while ($endpoint != null);

echo "\n";

// Encode and save to file...
saveJsonToFile('tags', $allTags);

echo count($allTags) . " tags found!\n";

// Get info for each track...
for ($i = 0; $i < count($allTags); $i++) {
    $track = getTrack($allTags[$i]['tag']['key'], $client);
    $allTags[$i]['track'] = $track;
    echo 'Getting tracks info: ' . $i . '/' . count($allTags) . "\n";
}

saveJsonToFile('tags_tracks', $allTags);

// Get tag list from the user...
function getTags($endpoint, $apiKey, $client) {
    $res = $client->request('GET', $endpoint, [
        'headers' => [
            'x-shazam-ampkey' => $apiKey,
        ]
    ]);
    return json_decode($res->getBody(), true);
}

// Get track metadata.
function getTrack($trackId, $client) {
    $res =  $client->request('GET', 'https://cdn.shazam.com/discovery/v4/en-US/BR/android/-/track/' . $trackId);
    return json_decode($res->getBody(), true);
}

// Save tags to json file.
function saveJsonToFile($fileName, $tags) {
    $serializedTags = json_encode($tags);
    $fileName = $fileName . '_' . time() . '.json';
    $file = fopen($fileName, 'w');
    fwrite($file, $serializedTags);
    fclose($file);
}
