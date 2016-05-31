include Environment.config

CONTAINER_NAME := $(notdir $(shell pwd))
IMAGE_NAME = ${CONTAINER_NAME}/${VERSION}

CONTAINER_HOME = /shared
MYSQL_PORT = 33068
HTTP_PORT = 80
ENVIRONMENT = production

default: help
.PHONY: logs

up: precheck git-up clean-container build run
up-dev: precheck set-dev up
sync: precheck git-up assets precheck
down: precheck clean-image clean-container
stop: precheck clean-container
start: precheck run
reset: precheck clean-all

NC = \033[0m
BG_BLACK = \033[0;40m
WHITE = ${BG_BLACK}\033[1;37m
GREEN = \033[0;32m
DARKGRAY = \033[1;30m
ICON_RIGHT = \xE2\x96\xB6

help:
	@echo -e " ${WHITE}up${NC}       \xE2\x80\xA3 to run all on ${WHITE}production${NC} ${DARKGRAY}(port 80)${NC}"
	@echo -e " ${WHITE}up-dev${NC}   \xE2\x80\xA3 to run all on ${WHITE}development${NC} ${DARKGRAY}(port 8080)${NC}"
	@echo -e " ${WHITE}restart${NC}  \xE2\x80\xA3 to ${WHITE}restart services${NC} ${DARKGRAY} related to app${NC}"
	@echo -e " ${WHITE}sync${NC}     \xE2\x80\xA3 to ${WHITE}synchonize & rebuild${NC} ${DARKGRAY}app from repo${NC}"
	@echo ""
	@echo -e " ${WHITE}down${NC}     \xE2\x80\xA3 to ${WHITE}clean${NC} project-related containers & images"
	@echo -e " ${WHITE}stop${NC}     \xE2\x80\xA3 to ${WHITE}clean${NC} project-related container"
	@echo -e " ${WHITE}start${NC}    \xE2\x80\xA3 to ${WHITE}run${NC} project-related container"
	@echo -e " ${WHITE}reset${NC}    \xE2\x80\xA3 to ${WHITE}clean all${NC} Docker containers & images"
	@echo -e " ${WHITE}ssh${NC}      \xE2\x80\xA3 to ${WHITE}ssh${NC} into Docker containers"
	@echo ""
	@echo -e " ${WHITE}precheck${NC}      \xE2\x80\xA3 to check & set right environment ${WHITE}permissions${NC}"
	@echo -e " ${WHITE}env${NC}           \xE2\x80\xA3 to view the ${WHITE}build parameters${NC}"
	@echo -e " ${WHITE}deps${NC}          \xE2\x80\xA3 to download & setup all app ${WHITE}dependencies${NC}"
	@echo -e " ${WHITE}deps-refresh${NC}  \xE2\x80\xA3 to download & setup all app ${WHITE}dependencies${NC}"
	@echo -e " ${WHITE}dump-db${NC}       \xE2\x80\xA3 to dump current state of ${WHITE}database${NC}"
	@echo -e " ${WHITE}logs${NC}          \xE2\x80\xA3 to see all ${WHITE}logs${NC} related to app"
	@echo -e ""
	@echo -e " ${DARKGRAY}(info) version:          ${VERSION}${NC}"
	@echo -e " ${DARKGRAY}(info) docker container: ${CONTAINER_NAME}${NC}"
	@echo -e " ${DARKGRAY}(info) docker image:     ${IMAGE_NAME}${NC}"

precheck:
	@echo -e "${GREEN}${ICON_RIGHT} running precheck operations..${NC}"
	@chmod -R 0777 environment/mysql/data
	@chmod -R 0777 logs/
	@chmod -R 0777 site/vendor/
	@chmod -R 0777 site/data
	@echo -e 'succeed'

env:
	@echo -e "${GREEN}${ICON_RIGHT} verifying environment params..${NC}"
	@echo -e " ${WHITE}version:         ${VERSION}${NC}"
	@echo -e " ${WHITE}nodejs version:  ${NODEJS_VERSION}${NC}"
	@echo -e " ${WHITE}php version:     ${PHP_VERSION}${NC}"

git-up:
	@echo -e "${GREEN}${ICON_RIGHT} updating project codebase..${NC}"
	#@git pull --force

set-dev:
	@echo -e "${GREEN}${ICON_RIGHT} setting development environment variables..${NC}"
	$(eval HTTP_PORT = 8080)
	$(eval ENVIRONMENT = development)

clean-image:
	@echo -e "${GREEN}${ICON_RIGHT} cleaning app related images..${NC}"
	@docker rmi -f ${IMAGE_NAME} 2>/dev/null || true
	docker images | grep ${IMAGE_NAME} || true

clean-container:
	@echo -e "${GREEN}${ICON_RIGHT} cleaning app related containers..${NC}"
	@docker rm -f ${CONTAINER_NAME} 2>/dev/null || true
	docker ps -a

clean-all: clean-image clean-container
	@echo -e "${GREEN}${ICON_RIGHT} cleaning all images..${NC}"
	@docker rm -f $$(docker ps -aq) || true
	docker ps -a
	@echo -e "${GREEN}${ICON_RIGHT} cleaning all containers..${NC}"
	@docker rmi -f $$(docker images -aq) || true
	docker images

build: stop clean-container
	@echo -e "${GREEN}${ICON_RIGHT} building app container..${NC}"
	@echo -e "${DARKGRAY}(info) environment: ${ENVIRONMENT}${NC}"
	@cp Dockerfile.tpl Dockerfile
	@chmod 0777 Dockerfile
	@sed "s/ARG ENVIRONMENT/ENV ENVIRONMENT \"${ENVIRONMENT}\"/" Dockerfile > x && mv x Dockerfile
	@sed "s/ARG NODEJS_VERSION/ENV NODEJS_VERSION \"${NODEJS_VERSION}\"/" Dockerfile > x && mv x Dockerfile
	@sed "s/ARG PHP_VERSION/ENV PHP_VERSION \"${PHP_VERSION}\"/" Dockerfile > x && mv x Dockerfile
	@sed "s/ARG MYSQL_USER/ENV MYSQL_USER \"${MYSQL_USER}\"/" Dockerfile > x && mv x Dockerfile
	@sed "s/ARG MYSQL_PASSWORD/ENV MYSQL_PASSWORD \"${MYSQL_PASSWORD}\"/" Dockerfile > x && mv x Dockerfile
	@docker build -t ${IMAGE_NAME} .

run: stop
	@echo -e "${GREEN}${ICON_RIGHT} running app container..${NC}"
	@docker run --name=${CONTAINER_NAME} \
		-p ${HTTP_PORT}:8080 \
		-p ${MYSQL_PORT}:3306 \
		-v $$PWD:${CONTAINER_HOME} \
		-e ENVIRONMENT="${ENVIRONMENT}" \
		-ti -d ${IMAGE_NAME}

ssh:
	@echo -e "${GREEN}${ICON_RIGHT} going over app ssh..${NC}"
	@docker exec -ti ${CONTAINER_NAME} bash

logs:
	@echo -e "${GREEN}${ICON_RIGHT} reading app logs..${NC}"
	@docker exec -ti ${CONTAINER_NAME} /bin/sh -c 'tail -f $$(find /shared/logs/ -type f)'

deps:
	@echo -e "${GREEN}${ICON_RIGHT} installing app dependencies..${NC}"
	@docker exec -ti ${CONTAINER_NAME} /bin/sh -c 'cd /shared/site && composer install'

deps-remove:
	@echo -e "${GREEN}${ICON_RIGHT} removing app dependencies..${NC}"
	@docker exec -ti ${CONTAINER_NAME} /bin/sh -c 'cd /shared/site && rm -rf composer.lock vendor/'

deps-refresh: deps-remove deps

dump-db:
	@echo -e "${GREEN}${ICON_RIGHT} creating dump of database..${NC}"
	@docker exec -ti ${CONTAINER_NAME} /bin/sh -c 'bash /shared/environment/dump.sh'

restart:
	@echo -e "${GREEN}${ICON_RIGHT} restarting the app services..${NC}"
	@docker exec -ti ${CONTAINER_NAME} /bin/sh -c 'service httpd restart'
	@docker exec -ti ${CONTAINER_NAME} /bin/sh -c 'service mysqld restart'
