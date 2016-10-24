@echo off
set db_name=DatabaseName
set db_schema=
set db_user=UserName
set db_pass=Password
set db_host=localhost
set db_port=5432

IF %db_name%==DatabaseName (
	ECHO Script not configured, please read Readme.txt &echo.
	pause
	exit
)

java -jar MapSchema.jar postgres %db_name% %db_schema% %db_user% %db_pass% %db_host% %db_port%
pause