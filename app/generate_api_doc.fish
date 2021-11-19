set REDOC_PATH (PWD)/../redoc

if test ! -d $REDOC_PATH
  git clone https://github.com/Redocly/redoc.git $REDOC_PATH
end

if test -z (docker images -q redoc-cli)
  docker build --rm -t redoc-cli "$REDOC_PATH/cli"
end

php artisan openapi:generate > openapi.json

set DOC_PATH (PWD)/resources/views/doc.html
docker run --rm -it -v (PWD)/openapi.json:/data/openapi.json redoc-cli -w /data bundle openapi.json --output /dev/stdout > $DOC_PATH

sed -i -e '1,2d' $DOC_PATH
sed -i -e '$d' $DOC_PATH
rm $DOC_PATH-e

