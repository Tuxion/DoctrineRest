test:
	cd tests && ./phpunit.sh -dc html=coverage.html && x-www-browser ./coverage.html/dashboard.html

watch:
	cd tests && ./phpunit.sh -dp && watch -n 3 find unit/src container/src ../src ../config \
		-mmin -1 -iname '"*.php"' -exec "./phpunit.sh -s \;" -quit