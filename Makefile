test:
	cd tests/unit && ./phpunit.sh

watch:
	cd tests/unit && composer self-update && cp ../../composer.json . && \
	if [ -d vendor ] ; then composer update ; else composer install ; fi && \
	watch -n 5 find ../../src -mmin -1 -iname '"*.php"' -exec './phpunit.sh watch \;'