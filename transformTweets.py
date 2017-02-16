#!/usr/bin/python

import sys
import json

tweets = []

# check number of arguments
if(len(sys.argv) != 2):
    print "usage: " + sys.argv[0] + " <filename>"
    exit(1)

with open(sys.argv[1]) as f:
    for line in f:
        tweets.append(json.loads(line))

with open('tweet_transform_output.txt', 'w') as outfile:
    print len(tweets)
    for tweet in tweets:
        if 'retweeted_status' in tweet:
            attributes = []
            # WORD_COUNT 
            attributes.append(str(len(tweet['retweeted_status']['text'].split(' '))))
            # FOLLOWERS 
            attributes.append(str(tweet['retweeted_status']['user']['followers_count']))
            # (grab timestamp data)
            datetime = str(tweet['retweeted_status']['created_at'])
            datetime_arr = datetime.split(' ')
            day = datetime_arr[0]
            time_arr = datetime_arr[3].split(':')
            hour = time_arr[0]
            # DAY
            attributes.append(day)
            # HOUR
            attributes.append(hour) # TODO: discretize hour
            # retweet 
            attributes.append(str(tweet['retweeted_status']['retweet_count']))

            # aggregate attributes into feature, write to file
            feature = ""            
            for attribute in attributes:
                feature += attribute + ","
            feature = feature[:-1]
            outfile.write(feature + "\n") 







