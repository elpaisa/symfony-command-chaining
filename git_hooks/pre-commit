#!/usr/bin/env bash


# Check if the individual developer has his own hook
CMD_NAME=`basename $0`
if [ -f .git/hooks/personal/$CMD_NAME ]
then
  # If so, run it. $@ passes all the command line arguments passed to this function
  # If the personal hook fails, fail this one as well
  if ! .git/hooks/personal/$CMD_NAME $@
  then
    echo "User hook '$CMD_NAME' failed"
    exit 1
  fi
fi


TEST_SCRIPT=./tests.sh

if [ ! -f "$TEST_SCRIPT" ]; then
  echo "$TEST_SCRIPT does not exist. Aborting Commit.">&2
  exit 1
fi

if [ ! -x "$TEST_SCRIPT" ]; then
  echo "$TEST_SCRIPT is not executable. Aborting Commit.">&2
  exit 1
fi

bash -n "$TEST_SCRIPT"
if [ $? -ne 0 ]; then
  echo "There was a syntax error in your unit testing script $TEST_SCRIPT \n Commit was aborted.">&2
  exit 1
fi

"$TEST_SCRIPT" true
if [ $? -ne 0 ]; then
  echo "Commit was aborted.">&2
  exit 1
fi
