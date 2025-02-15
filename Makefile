.PHONY: start
start:
	pnpm run build
	./vendor/bin/sail up -d
	./vendor/bin/sail artisan inertia:start-ssr

.PHONY: start-ssr
start-ssr:
	pnpm run build
	./vendor/bin/sail artisan inertia:start-ssr

.PHONY: stop
stop:
	./vendor/bin/sail down

.PHONY: migrate
migrate:
	./vendor/bin/sail artisan migrate
