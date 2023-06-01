#!/bin/bash

PHP=`which php`
SCRIPT_DIR=$( cd -- "$( dirname -- "${BASH_SOURCE[0]}" )" &> /dev/null && pwd )
cd $SCRIPT_DIR/src/

if [ -z "$1" ]; then
	$PHP ./RunUnitTests.php
	exit
fi


# Starting PHP Build-in server in background
$PHP -q -S 0.0.0.0:7080 UnitTestEndpoint.php &
PID=$! # $(ps | grep $PHP | grep 7080 | cut -f 1 -d" ")

if ! kill -0 $PID 2> /dev/null; then
	exit
fi

$PHP ./RunUnitTests.php $@

kill -HUP $PID
wait $PID 2> /dev/null
