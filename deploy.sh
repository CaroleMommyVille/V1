#!/bin/bash

usage() {
	echo "Usage: $0 --env <prod|dev> [--with-purge] [--with-sourcing]" 
}

env="prod"
purge=false
sourcing=false
append=false
args=( "$@" )

if [ "$#" -ne 0 ]; then
	for (( i=0; i<$#; i++ )); do
		case "${args[$i]}" in
			"--env")
				((i++))
				env="${args[$i]}"
				if [ $env == 'prod' ]; then
					param='--env=prod --no-debug'
				elif [ $env == 'beta' ]; then
					param='--env=beta'
				else 
					param=''
				fi
				;;
			"--with-append")
				append=true
				;;
			"--with-purge")
				purge=true
				;;
			"--with-sourcing")
				sourcing=true
				;;
		esac
	done
else
	usage $0
	exit $#
fi

git stash
rm -fR web/bundles/mommy*
chown -R www-data:www-data app/cache/
chmod -R go+w app/logs/ app/cache/
rm -fR app/cache/* app/logs/*
git pull
git submodule init
git submodule sync
git submodule foreach 'git fetch origin --tags; git checkout master; git pull' && git pull && git submodule update --init --recursive
git submodule update --init --recursive
chown -R www-data:www-data .
chmod -R g+w .
rm -fR app/cache/* app/logs/* web/bundles/mommy*

if [ $env == 'prod' ]; then
	composer install --no-dev --optimize-autoloader
elif [ $env == 'beta' ]; then
	composer install --no-dev --optimize-autoloader
fi 

composer dumpautoload --optimize
php app/console cache:clear $param
php app/console doctrine:cache:clear-metadata $param

if [ $sourcing == true ]; then
	curl --globoff -o src/Mommy/MapBundle/Resources/config/transportation.json "http://oapi-fr.openstreetmap.fr/oapi/interpreter?data=[out:json];node[%22type:RATP%22~%22metro|rer|tram|bus%22];out;node[%22type:SNCF%22~%22rer|transilien%22];out;%3E;out%20skel;" 2> /dev/null
fi

if [ $purge == true ]; then
	/etc/init.d/memcached restart
	php app/console doctrine:schema:drop --force --full-database $param
	php app/console doctrine:schema:update --force $param
	php app/console doctrine:fixtures:load --no-interaction $param
elif [ $append == true ]; then
	/etc/init.d/memcached restart
	php app/console doctrine:schema:update --force $param
	php app/console doctrine:fixtures:load --no-interaction --append $param
else
	php app/console doctrine:schema:update --force $param
fi


if [ $env == 'prod' ]; then
	php app/console doctrine:ensure-production-settings $param
elif [ $env == 'beta' ]; then
	php app/console doctrine:ensure-production-settings $param --no-debug
fi

cp web/.htaccess.$env web/.htaccess

php app/console assets:install $param
php app/console assetic:dump $param
php app/console cache:warmup $param
chown -R www-data:www-data app/cache/
chmod -R go+w app/logs/ app/cache/
rm -fR app/cache/* app/logs/*
setfacl -dR -m u::rwX app/cache app/logs

exit 0