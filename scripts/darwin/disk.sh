#!/bin/bash
# This script outputs the disk usage percentage of the root filesystem on macOS without the '%' sign.
echo `df -lh | awk '{if ($NF == "/") { print $(NF-4) }}'` | cut -d'%' -f1