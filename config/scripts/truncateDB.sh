#!/bin/bash

RED='\033[0;31m'
GREY='\033[0;35m'
NC='\033[0m'

# Check if bin/console exists
if [[ ! -x bin/console ]]; then
  echo -e "You should be in your web app root dir ${GREY}No bin/console found${NC}"
  echo -e "Current path: ${RED}$(pwd)${NC}"
  echo ""
  exit 1
fi

bin/console doctrine:database:drop --if-exists --force
bin/console doctrine:database:create
bin/console doctrine:mi:mi
bin/console doctrine:fixtures:load --append

echo ""
echo "Truncate finished!"
echo ""