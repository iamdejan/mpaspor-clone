.PHONY: build
build:
	pnpm run build

.PHONY: start
start: build
	./vendor/bin/sail up -d

.PHONY: stop
stop:
	./vendor/bin/sail down

.PHONY: migrate
migrate:
	./vendor/bin/sail artisan migrate
