#!/bin/bash
clear;

#plugin path from ilias-root:
PLUGINPATH='Customizing/global/plugins/Services/EventHandling/EventHook/CourseSubscriptionMails';
SCRIPT_PATH=$(dirname "$0");
cd $SCRIPT_PATH;

# note: no more parameters
# phpunit tests;

#first param is path to ilias installation
if [ $1 ] ; then
	cd $1;
	echo;
	echo 'now running ILIAS tests in ' $1;
	phpunit $PLUGINPATH/tests
fi
