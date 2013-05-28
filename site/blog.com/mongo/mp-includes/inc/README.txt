THIS DIRECTORY WILL CONTAIN THE CORE LIBRARIES AND CLASSES FOR MONGOPRESS

PHP files placed here will have no output - no automatically running stuff

==========================
Only classes or functions.
==========================

Library model:

nonce.php - should contain only function nonce_*function_name*() {} styled functions.

This is for two reasons;

    - locatability - if a function is called we know exactly where it should be living
    - name spaces - so we don't write the same named function with out difficulty


Classes - one large class per file - small classes can be bundled but not encouraged



