.PHONY: build install

.DEFAULT_GOAL := help

help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

# If the first argument is one of the supported commands...
SUPPORTED_COMMANDS := npm
SUPPORTS_MAKE_ARGS := $(findstring $(firstword $(MAKECMDGOALS)), $(SUPPORTED_COMMANDS))
ifneq "$(SUPPORTS_MAKE_ARGS)" ""
    # use the rest as arguments for the command
    COMMAND_ARGS := $(wordlist 2,$(words $(MAKECMDGOALS)),$(MAKECMDGOALS))
    # ...and turn them into do-nothing targets
    $(eval $(COMMAND_ARGS):;@:)
endif

build: ## build the docker trival-navigo images localy
	@docker-compose -f ./docker-compose.yml build

run: ## run trival-navigo in production mode
	@docker-compose -f ./docker-compose.yml up -d

start: ## start trival-navigo production daemon (needs a first run-prod the first time)
	@docker-compose -f ./docker-compose.yml start

stop: ## stop trival-navigo production daemon
	@docker-compose -f ./docker-compose.yml stop

kill: ## kill trival-navigo running containers
	@docker-compose -f ./docker-compose.debug.yml kill

rm: ## remove trival-navigo containers even if they are running
	@docker-compose -f ./docker-compose.debug.yml rm -f