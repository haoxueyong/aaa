
# src
rsync -av --exclude-from=./push_exclude_prod ../src/ www@192.168.1.20:/home/www/iCatch/

if [ $1 == "a" ]
	then  

	# app config

	rsync -av ../src/common/config/ www@192.168.1.20:/home/www/iCatch/common/config/

	# conf
	rsync -av ../srv/prod/ www@192.168.1.20:/home/www/conf/

	rsync -av ../https_pems/ www@192.168.1.20:/home/www/https_pems/

fi