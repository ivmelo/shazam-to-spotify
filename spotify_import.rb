require 'rest-client'
require 'json'
require 'active_support/time'

def read_file(filename)
    data = ''
    f = File.open(filename, 'r')
    f.each_line { |line| data += line }
    json_data = JSON.parse(data)
    return json_data
end

def print_readable(data)
    data.each do |tag|
        puts tag['track']['heading']['title']
        puts tag['track']['heading']['subtitle']

        d = Time.at(tag['tag']['timestamp'] / 1000)
        puts d.in_time_zone(tag['tag']['timezone']).strftime("%Y-%m-%d %H:%M:%S")

        puts tag['tag']['timezone']

        if tag['tag']['geolocation']
            puts 'http://maps.google.com/maps?z=12&q=loc:' + tag['tag']['geolocation']['latitude'].to_s + '+' + tag['tag']['geolocation']['longitude'].to_s
        end

        puts '----------------------------------'
    end
end

tags_file = read_file('tags_tracks_1492748826.json')
print_readable(tags_file)
