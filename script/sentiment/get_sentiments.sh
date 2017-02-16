#!/bin/bash

# Make sure we're using the right number of args
if [ $# -ne 1 ]
	then
		echo "Usage: $0 <file_name>"
		exit 1;

fi

# Kick out if it's not a valid file
if [ ! -f $1 ]
	then
		echo "not a file"
		exit 1;
fi

# Make the api call
curl -d @$1 http://www.sentiment140.com/api/bulkClassifyJson?appid=mrundle@nd.edu
