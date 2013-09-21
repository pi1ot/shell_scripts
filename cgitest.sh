#!/bin/sh

PROGNAME=`basename $0`
if [ $# -lt 1 ]; then
    echo "Usage: $PROGNAME <url>"
    exit;
fi

url=$1 # http://domian/query?param
temp=${url:7} # domian/query?param
domain=${temp%%/*} # domian
query=${temp#*/} # query?param
iplist=`nslookup $domain|grep Address|grep -v "#"|awk '{print $2}'`

for ip in $iplist
    do
    open -a safari.app "http://"$ip"/"$query
done

