@echo off
set db_sid=xe
set db_host=localhost
set db_port=1521

set db_user=DatabaseUser
set db_pass=DatabasePassword

set tb_table_pattern=ICAEN_%%
:: ICAEN_%%

:: WARNING: In bash you must write '%%' instead of '%'.


IF %db_user%==DatabaseUser (
	ECHO Script not configured, please read Readme.txt &echo.
	pause
	exit
)

java -jar MapSchema.jar oracledb %db_sid% %db_user% %db_pass% %db_host% %db_port% %tb_table_pattern%
pause