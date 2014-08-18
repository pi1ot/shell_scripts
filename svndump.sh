#!/bin/sh

PROGNAME=`basename $0`
if [ $# -lt 1 ]; then
    echo "Usage: $PROGNAME <file> [revstep]"
    exit;
fi

file=$1
step=1
if [ $# -eq 2 ]; then
    step=$2
fi

revision=`svn log -l $step $file | grep \| | awk "NR==$step{print \\$1}"`
number=${revision:1}

svn diff --diff-cmd diff2html.sh -c $number $file

