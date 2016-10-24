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


CSV
===================================================================================
1. Modify CSV.bat file with your configuration.
2. Execute CSV.bat, This will generate an .xml file.
3. Upload the generated .xml file to Mapon application.

	Configuration variables:
	-------------------------------------------------------------------------------
	set cvs_file=theFile.cvs
		Specify the cvs file path.

	set cvs_comma_separator=,
		Specify the comma separator, usually "," or ";"


Oracle
===================================================================================
1. Modify Oracle.bat file with your database configuration.
2. Execute Oracle.bat, This will generate an .xml file.
3. Upload the generated .xml file to Mapon application.

	Configuration variables:
	-------------------------------------------------------------------------------
	set db_sid=xe
	set db_host=127.0.0.1
	set db_port=3306
		Specify the Oracle connection params.

	set db_user=MyUserName
	set db_pass=MyPassword
		Specify the User and Password.

    set tb_table_pattern=%%
        Specify the table pattern. (In bash wou must write '%%' instead of '%' in order to scape the symbol.)
        '%%' -> Will show all tables.
        'FOO_%%' -> Will show all tables starting with 'FOO_'



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

	java -jar MapSchema.jar mysql %db_name% %db_user% %db_pass% %db_host% %db_port%
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

	java -jar MapSchema.jar postgres %db_name% %db_schema% %db_user% %db_pass% %db_host% %db_port%
	pause


	CSV (CVS.bat)
    -------------------------------------------------------------------------------
    @echo off
    set cvs_file=theFile.csv
    set cvs_comma_separator=;

    java -jar MapSchema.jar cvs %cvs_file% %cvs_comma_separator%
    pause


	Oracle (Oracle.bat)
    -------------------------------------------------------------------------------
    @echo off
    set db_sid=xe
    set db_host=localhost
    set db_port=1521

    set db_user=ICAEN_WEB
    set db_pass=root
    set tb_table_pattern=ICAEN_%

    java -jar MapSchema.jar oracledb %db_sid% %db_user% %db_pass% %db_host% %db_port% %tb_table_pattern%
    pause