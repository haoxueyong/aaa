
# src
rsync -av --exclude-from=./push_exclude_ali ../src/ www@47.93.112.115:/web/iCatch/

if [ $1 == "a" ]
	then  

	# app config

	rsync -av ../src/common/config/ www@47.93.112.115:/web/iCatch/common/config/

	# conf
	rsync -av ../conf/ www@47.93.112.115:/web/conf/

	rsync -av ../https_pems/ www@47.93.112.115:/web/https_pems/


	# run

	rsync -av run.sh www@47.93.112.115:/web/run.sh

	rsync -av socket_run.sh www@47.93.112.115:/web/socket_run.sh
fi

if [ $1 == "e" ]
	then  

	# app environments

	rsync -av ../src/environments/ www@47.93.112.115:/web/iCatch/environments/

	
fi

if [ $1 == "c" ]
	then  
	# conf
	rsync -av ../conf/ www@47.93.112.115:/web/conf/
	
fi