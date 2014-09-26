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
  phpunit --coverage-clover=coverage.clover
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
