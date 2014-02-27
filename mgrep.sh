#!/bin/sh
# usage: mgrep.sh "aaa|bbb|ccc|..." <file>

PROGNAME=`basename $0`
if [ $# -lt 2 ]; then
    echo "Usage: $PROGNAME \"aaa|bbb|ccc|...\" <file>"
    exit;
fi

words=$1
match=(${words//|/ })

shift
cmd="grep \"${match[0]}\" $@"

len=${#match[@]}
for((i=1;i<len;++i))
do
	cmd="$cmd | grep \"${match[$i]}\""
done

echo $cmd

