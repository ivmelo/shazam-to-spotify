require 'rest-client'
require 'json'

url = 'https://amp.shazam.com/shazam/v1/en-US/BR/android/-/installation/__USER__/tagevents'
key = ''
track_url = 'https://cdn.shazam.com/discovery/v4/en-US/BR/android/-/track/'

shazam_tags = []

puts 'Getting TAG data...'

loop do
    result = RestClient.get(url, {
        'x-shazam-ampkey': key
    })

    parsed_result = JSON.parse(result)

    tags = parsed_result['events']
    url = parsed_result['next']['url']

    tags.each do |tag|
        shazam_tags.push(tag)
        puts tag['tagid']
    end

    break if tags.length == 0
end

# puts shazam_tags

puts 'Getting tracks data...'

for i in 0..shazam_tags.length - 1 do
    result = RestClient.get(track_url + shazam_tags[i]['tag']['key'])
    parsed_result = JSON.parse(result)

    shazam_tags[i]['track'] = parsed_result

    puts i.to_s + '/' + shazam_tags.length.to_s + ' tracks added'
end

# puts shazam_tags.to_json

puts 'Saving to file...'

filename = 'tags_tracks_' + Time.now.getutc.to_i.to_s + '.json'
File.open(filename, 'w') { |file| file.write(shazam_tags.to_json) }

puts 'Done!'
