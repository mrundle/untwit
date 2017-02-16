import matplotlib.pyplot as plt
import sys

counts = []
with open(sys.argv[1]) as f:
    for line in f:
        count = int(line.split(',')[0])
        if count < 30:
            counts.append(count)

plt.hist(counts, bins=6)
plt.show()
