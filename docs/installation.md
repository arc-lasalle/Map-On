# Map-On
**A web-based editor for visual ontology mapping** <br>
Map-On is a graphical environment for ontology mapping which helps different kinds of users to carry out ontology mapping processes by establishing relations between the elements of a database and a domain ontology.

----------
<br>
Installation
-------------
### System configuration
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

Copyright (C) 2016 ARC Engineering and Architecture La Salle, Ramon Llull University.
