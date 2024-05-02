#!/bin/bash
# This script prints the CPU usage percentage on macOS

# Using the top command to get CPU usage. The command runs in logging mode (similar to batch mode in Linux top with -l 1),
# which means it will update every second and only make one sample. The output is then parsed to find the line containing CPU usage information.
cpu_usage=$(top -l 1 -n 0 | grep "CPU usage" | awk '{print $3}' | sed 's/%//')

echo $cpu_usage
