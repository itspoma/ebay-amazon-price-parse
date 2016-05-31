#!/bin/bash -e

cd /shared/site

rm -rf web/assets/*.{js,css}

# build js
node node_modules/.bin/r.js \
  -o web/scripts/main-build.js

# build css
node node_modules/.bin/grunt build
