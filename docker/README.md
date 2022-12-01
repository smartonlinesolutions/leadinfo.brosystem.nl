# Docker Development Environment

## WP-CLI

Run any wp command in the following manner: 

    docker exec %container-name% wp

## Toggle XDebug

If you are not debugging you can disable XDebug by running:

    docker exec %container-name% xdebug-disable
    
If you want to enable it again:

    docker exec %container-name% xdebug-enable

