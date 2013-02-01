#!/bin/bash

if [ -f coverage.dat ]
then
	unlink coverage.dat
fi

../tools/Tester/Tester/tester -p php -j 20 -c "xdebug.ini"

if [ $? -eq 0 ]
then
	if [ -f coverage.dat ]
	then
		./coverage.sh --noenter
	fi
fi
