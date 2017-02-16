import matplotlib.pyplot as plt
import sys

num_attr = 0
with open(sys.argv[1]) as f:
    for line in f:
        line_arr = line.split(',')
        if num_attr is 0:
            num_attr = len(line_arr)
            print num_attr
            exit(0)
            
