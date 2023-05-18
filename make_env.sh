#!/bin/bash
set -eu

cat << EOT > .env
#PHPの設定
IP=127.0.0.1
WEB=28001
DB=28002

EOT
