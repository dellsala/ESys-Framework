#!/bin/bash

### CONFIG START ###

REMOTEWWW="%user%@%host%:~/path/to/www"
REMOTELIB="%user%@%host%:~/path/to/lib"

DSALA_APPLICATION_LIB="/path/to/project/lib"

### CONFIG END ###


#  -- rsync notes --
#  mac file system option: -eahfs
#  dry run option: -n

if [[ ! -n "$DSALA_APPLICATION_LIB" ]]
then
    echo "error: DSALA_APPLICATION_LIB is not defined"
    echo "exiting..."
    exit
fi

WWWEXCLUDE="${DSALA_APPLICATION_LIB}/../deploy/exclude.www.rsync"
LIBEXCLUDE="${DSALA_APPLICATION_LIB}/../deploy/exclude.lib.rsync"


TESTARG=""
DELETEARG=""

WWWSOURCE="${DSALA_APPLICATION_LIB}/../www"
WWWTARGET="${REMOTEWWW}"

LIBSOURCE="${DSALA_APPLICATION_LIB}"
LIBTARGET="${REMOTELIB}"

REVERSED=0
PRIMARYACTION=0

printHelp () {
            echo;
            echo "-- USAGE --";
            echo "deploy.sh -u|d|s|h [-xt]";
            echo;
            echo "-- OPTIONS --";
            echo "-u    push files UP to remote server";
            echo "-d    pull files DOWN from remote server";
            echo "-t    test mode";
            echo "-x    delete unmatch files on target";
            echo "-s    display current settings and exit";
            echo "-h    display this screen and exit";
            echo;
}

while getopts ":txudsh" FLAG
do
    case "$FLAG" in
        't' ) 
            TESTARG='n' 
            ;;
        'x' ) 
            DELETEARG='--delete' 
            ;;
        'u' ) 
            PRIMARYACTION=1
            ;;
        'd' ) 
            if (( ! $REVERSED ))
            then
                WWWTEMP="$WWWSOURCE"
                WWWSOURCE="$WWWTARGET"
                WWWTARGET="$WWWTEMP"
                LIBTEMP="$LIBSOURCE"
                LIBSOURCE="$LIBTARGET"
                LIBTARGET="$LIBTEMP"
                REVERSED=1
                PRIMARYACTION=1
            fi
            ;;
        's' ) 
            echo
            echo "-- DEPLOY SETTINGS --"
            echo
            echo "remote paths:"
            echo "www: $REMOTEWWW"
            echo "lib: $REMOTELIB"
            echo
            echo "www exclude rules:"
            cat "${WWWEXCLUDE}"
            echo
            echo "lib exclude rules:"
            cat "${LIBEXCLUDE}"
            echo
            exit
            ;;
        'h' ) 
            printHelp
            exit
            ;;
        * )
            echo;
            echo "error: unrecognized option '$FLAG'"
            printHelp
            exit
            ;;
    esac
done

if (( ! $PRIMARYACTION ))
then
    echo "You must choose to either upload (-u) or download (-d)"
    printHelp
    exit
fi

COMMAND=\
"rsync -av$TESTARG -e ssh $DELETEARG \
--exclude-from=${WWWEXCLUDE} \
$WWWSOURCE/ $WWWTARGET"
echo $COMMAND
$COMMAND

echo

COMMAND=\
"rsync -av$TESTARG -e ssh $DELETEARG \
--exclude-from=${LIBEXCLUDE} \
$LIBSOURCE/ $LIBTARGET"
echo $COMMAND
$COMMAND
