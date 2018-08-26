# Project System

## Authentication

- Must be in the user table to have access to the application

## Preliminary Setup:
- MySQL, PHP7.1 and Apache2
- Go to /var/www/html/
- git clone https://github.com/UMD-ACES/ProjectSystem.git
- Edit your SSL VirtualHost file. Mine is located here: "/etc/apache2/sites-enabled/default-ssl.conf"
- Change "DocumentRoot /var/www/html/" to "DocumentRoot /var/www/html/ProjectSystem/public"
- Add "ServerName (domain)" where (domain) is your domain
- Restart Apache

## Setup
1. Become root
2. Make sure to change the default OS and DB password that this application is running on. 
3. Go to /var/www/html/ProjectSystem
4. Run "php composer.phar install"
5. Run "cp .env.example .env"
5. Run "php artisan key:generate"
6. Setup the DB connection in .env (may need to create a DB)
7. Run "php artisan migrate"
8. Run "php artisan app:addAdminUser {name} {directoryID}"
9. Run "php artisan app:addCriterion {criterion}" for each peer evaluation criterion
10. Run "php artisan app:addTechnicalCategory {category}" for each technical log category.
11. SECURITY (mandatory): In .env, setup the CAS_VALIDATION variable to be "ca" and the CAS_CERT variable to the path of the CA certificate (instructions in config/cas.php)
12. SECURITY (mandatory): Open ".env", set APP_ENV to "production" and set APP_DEBUG to "false"
13. SECURITY (recommended): Disable phpmyadmin with "sudo a2disconf phpmyadmin.conf && sudo /etc/init.d/apache2 restart"
14. Go to <site>/setup.php

## Recommended
Turn off password authentication and solely allow publickey authentication

Peer Evaluation Criteria:  
php artisan app:addCriterion "Attended group meetings"  
php artisan app:addCriterion "Available for communication"  
php artisan app:addCriterion "Contributed to ideas/planning"  
php artisan app:addCriterion "Contributed to testing/researching if those ideas would work"  
php artisan app:addCriterion "Conducted research/background information"  
php artisan app:addCriterion "Configured honeypot"  
php artisan app:addCriterion "Wrote/tested automation scripts"  
php artisan app:addCriterion "Created honey"  
php artisan app:addCriterion "Did fair share of work"  

Technical Log Criteria:  
php artisan app:addTechnicalCategory "Host Configuration"  
php artisan app:addTechnicalCategory "Honeypot VMs"  
php artisan app:addTechnicalCategory "Networking"  
php artisan app:addTechnicalCategory "Scripts"  

## TODO:
- Criteria setup in setup page


## License: MIT

Copyright 2018 UMD-ACES

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
