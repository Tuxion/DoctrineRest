test:
	cd tests && ./phpunit.sh --coverage && x-www-browser ./coverage.html/dashboard.html

watch:
	cd tests && composer self-update && cp ../composer.json . && \
	if [ -d vendor ] ; then composer update ; else composer install ; fi && \
	watch -n 3 find unit/src container/src ../src ../config -mmin -1 -iname '"*.php"' -exec "./phpunit.sh --watched-mode \;" -quit