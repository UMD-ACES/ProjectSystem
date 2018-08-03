# Project System

## Authentication

- Must be in the user table to have access to the application

## Setup
1. Make sure to change the default password for the OS that this application is running on
2. Become root
3. Go to /var/www/html/ProjectSystem
4. Run "php composer.phar install"
5. Setup the DB connection in .env (may need to create a DB)
6. Run "php artisan migrate"
7. Run "php artisan addAdminUser {directoryID}"
8. Go to <site>/setup.php

## TODO:
- Criteria setup in setup page


## License: MIT

Copyright 2018 UMD-ACES

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
