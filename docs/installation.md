# Map-On
**A web-based editor for visual ontology mapping** <br>
Map-On is a graphical environment for ontology mapping which helps different kinds of users to carry out ontology mapping processes by establishing relations between the elements of a database and a domain ontology.

----------
<br>
Installation
-------------
### System configuration
Map-On was developed under PHP7, older versions may give some compatibility issues.

Make sure 'mysql' command are available on terminal.
> $ mysql <br>
If not available, add mysql path to your environment PATH variable.

### Create the database
Create a database called 'mapon' and import tables from 'mapon_base_db.sql'

> $ CREATE DATABASE mapon; <br>
> $ mysql --user=<database_user> --password=<database_password> mapon < mapon_base_db.sql

**Note:** Sql file are located in 'application/config/mapon_base_db.sql'
<br><br>
### Framework configuration
Rename the files 'config.dist.php' and 'database.dist.php' to 'config.php' and 'database.php'
> $ sudo cp application/config/config.dist.php application/config/config.php <br>
> $ sudo cp application/config/database.dist.php application/config/database.php 

Modify base_url variable from 'application/config/config.php'
> $config['base_url'] = 'http://localhost/<project_path>';

Modify database credentials from 'application/config/database.php'
> $db['default']['username'] = 'database_username'; <br>
> $db['default']['password'] = 'database_password';

### Permissions
Apache user must have write permissions on 'upload' and 'download' directories.

### Optional configurations
This project **runs under default apache installation**, for optional configurations (apache2 VirtualHosts or Nginx) see [apache/nginx configuration](./apache_nginx.md)


----------
<br>
Usage
-------------

### Default user
> Login: admin@admin.com <br>
> Password: adminadmin

### Basic steps
1. Login to platform.
2. Go to 'Admin > Create new team' amd create a new team.
3. Go to 'Admin > List users > Admin > Edit user > Teams' and select the previously created team.
4. Go to the top right corner of screen and select the team.
5. Upload the ontology, upload the datasource and create the mappings.
	1. Go to 'Ontologies' tab and upload the ontology.
	2. Go to 'Data sources' tab and upload the datasource.
	3. Go to 'Data sources' tab, select your datasource and create the mappings.
	4. Go to 'Data sources' tab, select your datasource and click on 'Export to R2RML' to obtain the created mappings.

Copyright (C) 2016 ARC Engineering and Architecture La Salle, Ramon Llull University.
