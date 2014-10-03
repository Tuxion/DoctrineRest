#!/bin/sh

# Default values
suppress_status=0
deploy=0
coverage=0
coverage_type="text"
suite="."
prepare_only=0

# Help info
show_help() {
cat << EOF
Usage: ${0##*/} [-hcdsp] [SUITE]...
Run the PHPUnit tests defined by the given suite.
By default the suite is '.', which runs all suites.

    -h              Display this help and exit.
    -d              Deploy the vendor package using composer.
    -s              Supress PHPUnit status code, always returning 0.
    -p              Preparations only, this skips the actual testing.
                    Useful for doing a one-time deployment with -d for example.
    -c type[=path]  Generate code coverage reports.
                    Type indicated the type of reports, based on phpunit functionality.
                    The optional path indicates where to store the report.

Examples scenarios:
  
  ${0##*/} -dc html=coverage.html
    Deploy the vendor package and generate a code coverage report in HTML format.
  
  ${0##*/} -c text unit
    Run the unit test suite only and report code coverage in TEXT format.
  
  ${0##*/} -dp
    Deploy vendor package only and skip testing.
  
EOF
}

# Get options
OPTIND=1 # Reset getopts variable
while getopts "hc:dsp" opt; do
  case "$opt" in
    h)
      show_help
      exit 0
      ;;
    d)
      deploy=1
      ;;
    s)
      suppress_status=1
      ;;
    c)
      coverage=1
      coverage_type=$OPTARG
      ;;
    p)
      prepare_only=1
      ;;
  esac
done
shift "$((OPTIND-1))" # Shift off the options and optional --.      

# This is the suite parameter. Default: '.' which tests all suites.
if [ -n "$1" ]
then
  suite=$1
fi

# Do we need to deploy the vendor packages?
if [ $deploy -eq 1 ]
then
  composer self-update
  cp ../composer.json .
  if [ -d vendor ]
  then
      composer update
  else
      composer install
  fi
fi

# Preparations only?
if [ $prepare_only -eq 1 ]
then
  exit 0
fi

# Run the tests.
if [ $coverage -eq 1 ]
then
  phpunit -c $suite --coverage-$coverage_type
else
  phpunit -c $suite
fi
status=$?

if [ $suppress_status -eq 1 ]
then
  exit 0
else
  exit $status
fi
