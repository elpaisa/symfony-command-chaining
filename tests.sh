#!/usr/bin/env bash

MASTER_STARTTIME=$(date +%s)
STARTTIME=$(date +%s)

BASEDIR=$(pwd)
SOURCE_DIR=$BASEDIR/src

PHPMD_BIN=./vendor/phpmd/phpmd/src/bin/phpmd
FIX=${1-false}

echo "Starting verification..."

STARTTIME=$(date +%s)
echo "running php linting"

echo "running the linting over just the changed files to save time"

git diff --cached --name-only | while read FILE; do
if [[ "$FILE" =~ ^.+(php)$ ]]; then
    if [[ -f $FILE ]]; then
      php -l "$FILE" 1> /dev/null
      if [ $? -ne 0 ]; then
        echo -e "Aborting commit due to files with syntax errors" >&2
        exit 1
      fi
    fi
fi
done
LINT_RESULT=$?

if [[ $LINT_RESULT -ne 0 ]]; then
  echo "$PHP_LINTING"|grep -v "No syntax errors detected" >&2
  echo "There were syntax error detected.">&2
  exit 1
fi

ENDTIME=$(date +%s)
echo "linting took $((ENDTIME - STARTTIME)) seconds to complete."

#PHPMd not yet ready for php7
echo "Running phpmd on everything"
PHPMD_RESULTS=$($PHPMD_BIN "$SOURCE_DIR"/ text phpmd.xml --suffixes php )
if [ $? -ne 0 ]; then
  echo "$PHPMD_RESULTS" >&2
  echo "Warning, phpmd does not like something. Please see the phpmd error message." >&2
  wait
  exit 1
fi

echo "running php unit tests"
STARTTIME=$(date +%s)
EXTRAARGS=''


PHPUNIT_RESULTS=$(phpunit 2>&1)
PHPUNIT_EXIT_CODE=$?
ENDTIME=$(date +%s)

echo "phpunit took $((ENDTIME - STARTTIME)) seconds to complete."

if [ $PHPUNIT_EXIT_CODE -ne 0 ]; then
  echo "$PHPUNIT_RESULTS" >&2
  echo "Unit tests failed!" >&2
  wait
  exit 1;
fi
MASTER_ENDTIME=$(date +%s)
echo "everything took $((MASTER_ENDTIME - MASTER_STARTTIME)) seconds to complete."
echo "Tests Passed"
wait
exit 0
