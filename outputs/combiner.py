#!/usr/bin/python
# combines output from get_sentiments.sh with output from transformTweets.py
import sys
import json

if len(sys.argv) != 3:
    print "usage: " + sys.argv[0] + ": <output from 'get_sentiments.sh'> <output from 'transformTweets.py'>"
    exit(1)

# GET THE SENTIMENTS
sentiments = []
with open(sys.argv[1]) as json_data:
    for line in json_data:
        d = json.loads(unicode(line,"ISO-8859-1"))
tweets = d['data']
for tweet in tweets:
    sentiments.append(tweet['polarity'])

with open(sys.argv[2]) as f:
    features = f.readlines()

i = 0
with open('combiner_output.txt', 'w') as outfile:
    for feature in features:
        attributes = feature.rstrip().split(' ')
        attributes.insert(len(attributes) - 2, sentiments[i])
        i += 1
        # aggregate attributes into feature, write to file
        feature_str = ""            
        for attribute in attributes:
            feature_str += str(attribute) + ","
        feature_str = feature_str[:-1]
        outfile.write(feature_str + "\n")

if len(sentiments) != i:
    print "FAILURE: DIFFERENT NUMBER OF SENTIMENT SCORES AND FEATURE VECTORS"
else:
    print "SUCCESS: Combined " + str(len(sentiments)) + " sentiment scores with " + str(i) + " feature vectors!" 






    
