#!/bin/bash
ln -s /var/www/html/grant-pattishall-award.com/site.conf /etc/apache2/sites-available/grant-pattishall-award.com.conf
a2ensite grant-pattishall-award.com
service apache2 reload
