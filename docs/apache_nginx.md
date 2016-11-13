# Apache/Nginx configuration

This project **runs under default apache installation**.


Here are some examples of optional configurations:


Advanced configurations
------------------------

### Apache VirtualHost
We asume the project is located in directory /var/www/html/Map-On

	<VirtualHost *:80>
        DocumentRoot /var/www/html

        <Directory /var/www/html/Map-On/>
                AllowOverride All
        </Directory>
        
	</VirtualHost>


### Nginx configuration
We asume the project is located in directory /usr/share/nginx/html/Map-On

	server {
		root /usr/share/nginx/html;
		
		location /Map-On/ {
			try_files $uri $uri/ /Map-On/index.php;
		}

		location ~ \.php$ {
			include snippets/fastcgi-php.conf;
			fastcgi_pass unix:/var/run/php/php7.0-fpm.sock;
		}
	}





Copyright (C) 2016 ARC Engineering and Architecture La Salle, Ramon Llull University.
