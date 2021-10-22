default: help

UNAME_S := $(shell uname -s)
ifeq ($(UNAME_S),Linux)
	export REMOTE_HOST := $(shell ip -4 addr show docker0 | grep -Po 'inet \K[\d.]+')
endif
ifeq ($(UNAME_S),Darwin)
	export REMOTE_HOST := $(shell ifconfig | sed -En 's/127.0.0.1//;s/.*inet (addr:)?(([0-9]*\.){3}[0-9]*).*/\2/p' | sed -n '1p')
endif


help::
	@echo Available options: build, up, down, logs, vars

build::
	docker-compose build

up::
	docker-compose up --detach --force-recreate

exec::
	docker-compose exec -u root php bash

down::
	docker-compose down

logs::
	docker-compose logs --follow

vars::
	@echo "export REMOTE_HOST=${REMOTE_HOST}"
