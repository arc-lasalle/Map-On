MySql
===================================================================================
1. Modify MySql.bat file with your database configuration.
2. Execute MySql.bat, This will generate an .xml file.
3. Upload the generated .xml file to Mapon application.

	Configuration variables:
	-------------------------------------------------------------------------------
	set db_name=MyDatabase
		Specify the MySql database name.

	set db_user=MyUserName
	set db_pass=MyPassword
		Specify a MySql User and password.
	   
	set db_host=127.0.0.1
	set db_port=3306
		Specify the IP (or Hostname) and port of MySql.

Postgres
===================================================================================
1. Modify Postgres.bat file with your database configuration.
2. Execute Postgres.bat, This will generate an .xml file.
3. Upload the generated .xml file to Mapon application.

	Configuration variables:
	-------------------------------------------------------------------------------
	set db_name=MyDatabase
		Specify the Postgres database name.
			
	set db_schema=
		Specify the schema name. If empty all schemas will be parsed.

	set db_user=MyUserName
	set db_pass=MyPassword
		Specify a Postgres User and password.
	   
	set db_host=127.0.0.1
	set db_port=3306
		Specify the IP (or Hostname) and port of Postgres.


			
			
			
Examples
===================================================================================

	MySql (MySql.bat)
	-------------------------------------------------------------------------------
	@echo off
	set db_name=Mapon
	set db_user=root
	set db_pass=root
	set db_host=127.0.0.1
	set db_port=3306

	java -jar MaponSql.jar mysql %db_name% %db_user% %db_pass% %db_host% %db_port%
	pause


	Postgres (Postgres.bat)
	-------------------------------------------------------------------------------
	@echo off
	set db_name=dvdrental
	set db_schema=public
	set db_user=postgres
	set db_pass=root
	set db_host=localhost
	set db_port=5432

	java -jar MaponSql.jar postgres %db_name% %db_schema% %db_user% %db_pass% %db_host% %db_port%
	pause