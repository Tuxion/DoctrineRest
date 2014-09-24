if [ $1 != 'watch' ]
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
phpunit
status=$?
exit $status
