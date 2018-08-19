cd "$(dirname "$0")"

mkdir web

cp -rf src web/src
cp -rf conf web/conf
cp -rf https_pems web/https_pems

find web/ -name ".DS_Store" -depth -exec rm {} \;
find web/ -name ".gitignore" -depth -exec rm {} \;

rm -rf web/src/console/runtime/*
rm -rf web/src/frontend/runtime/*
rm -rf web/src/backend/runtime/*

mkdir hoohoolab

tar -zcvf hoohoolab/web.tar.gz web

md5 hoohoolab/web.tar.gz > hoohoolab/md5.txt

rm -rf web
