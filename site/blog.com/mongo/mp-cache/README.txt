THIS DIRECTORY IS THE CACHE STORAGE SPACE.

Must be writeable by the webserver - you can accomplish this on unix with chmod 777 - but we suggest making it owned by the webserver user - www-data or nobody

For super fast UNIX operation - replace with a link to /dev/shm/mp-cache - to use shared memory.

For windows - install RAMDISK from 3rd party.
- modify mp-loader.php - to use X:\mp-cache - or whatever you decide.

TODO - need a reaper on a cron job, or if enabled a periodic flush of this.
