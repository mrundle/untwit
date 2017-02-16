#!/usr/bin/python
import json
import sys
import pprint

with open(sys.argv[1]) as json_data:
    for line in json_data:
        d = json.loads(unicode(line,"ISO-8859-1"))
tweets = d['data']
count = 0
for tweet in tweets:
    count += 1
print count
