#!/usr/bin/env bash
projectCommon=$(cd `dirname $0`; pwd)
# projectCommon='/Users/gaolei/Documents/Work/php'

excludeCommon="app/cache/* app/logs/*"

projectArr[0]=$projectCommon'/src'
rsyncArr[0]='root@192.168.1.20:/web/src'
exclude[0]=""

# projectArr[1]=$projectCommon'/project2'
# rsyncArr[1]='-e "ssh -p222" root@xxx.xxx.xxx.xxx:/srv/www/project1'
# rsyncArr[1]='root@xxx.xxx.xxx.xxx:/srv/www/project2'
#wild card,seperated by space

function funSync(){
    for k1 in ${!projectArr[*]};do
        dirPrefix=${projectArr[$k1]}
        path=${1}
        if [ "$1" = 'all' ];then
            path=${dirPrefix}
        fi
        if [ "${path:0:${#dirPrefix}}" = "$dirPrefix" ];then
            path=${path#$dirPrefix}
            excludeK1=($excludeCommon' '${exclude[$k1]})
            if [ -n "$excludeK1" ];then
                for v2 in ${excludeK1[*]};do
                    if [[ "${path:1}" == $v2 ]];then
                        break 2
                    fi
                done
            fi
            rsync='/usr/local/bin/rsync -avz --delete --exclude "rsync.sh"'
            rsync="cd ${projectArr[$k1]} && "$rsync' .'$path' '${rsyncArr[$k1]}$path' --no-p --no-t'
            dst=${rsyncArr[$k1]#*@}
            #dst=${dst%:*}
            echo -n '['`date +%H:%M:%S`'] '${projectArr[$k1]##*/}$path' => '$dst$path
            time=$( (time eval $rsync > /dev/null) 2>&1|head -n2|tail -n1|cut -f2)
            # if [ 0 -eq $? ];then
            #     echo -ne " 33[32mok33[0m"
            # else
            #     echo -ne " 33[31mfailed33[0m"
            # fi
            echo '  ('${time:2}')'
        fi
    done
}

for i in "$*"; do
    if [ "$i" = '-r' ];then
        funSync all
        echo '全量更新完毕！'
    fi
done

fswatch --exclude=.git $projectCommon|while read v;do
    if [ ! -e $v ];then
        #此处跳过将不会删除文件，删除文件消耗比较大，需要递归检测
        continue
        v=$(dirname $v)
    fi
    funSync $v
done