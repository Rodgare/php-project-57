start:
	php artisan serve & npm run dev

test:
	php artisan test

lint:
	./vendor/bin/pint

stan:
	./vendor/bin/phpstan analyse app