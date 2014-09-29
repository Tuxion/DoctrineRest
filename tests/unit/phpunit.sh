mode=$1
if [ "$mode" != "--watched-mode" ]
then
  composer self-update
  cp ../../composer.json .
  if [ -d vendor ]
  then
      composer update
  else
      composer install
  fi
fi

if [ "$mode" != "--coverage" ]
then
  phpunit --coverage-html coverage.html
else
  phpunit
fi
status=$?

if [ "$mode" != "--watched-mode" ]
then
  exit $status
else
  exit 0
fi
