.PHONY: run-all
run-all:
	./vendor/bin/sail up -d
	./vendor/bin/sail artisan inertia:start-ssr
