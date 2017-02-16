#!/usr/bin/python
#
# This file takes as input a filename, pointing to a file
# containing tweets in JSON format.
#
# It then outputs the text component of these tweets in a JSON
# format that is acceptable by the sentiment analysis API that 
# we're using (http://sentiment140.com/api)
#
# Example workflow:
#
#    ./textify.py tweets.FULL.json
#
#    get_sentiments.sh textify_output.txt > get_sentiments.sh
#

import sys
import json

if( len(sys.argv) != 2 ):
    print "usage: " + sys.argv[0] + " <filename>"
    exit(1)

try:
    with open(sys.argv[1]) as f:
        content = f.readlines()
except IOError:
    print sys.argv[0] + ": cannot open " + sys.argv[1]
    exit(1)


tweets = []
with open(sys.argv[1]) as f:
    for line in f:
        tweets.append(json.loads(line))   

data = {}
tweets_text = []
for tweet in tweets:
    if 'retweeted_status' in tweet:
		tweet_text_obj = {}
		tweet_text_obj['text'] = tweet['retweeted_status']['text']
		tweets_text.append(tweet_text_obj)
data['data'] = tweets_text

# Dump this to a file that is readable by the
# Sentiment API
with open('textify_output.txt', 'w') as outfile:
	json.dump(data,outfile)
