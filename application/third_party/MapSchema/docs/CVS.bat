@echo off
set cvs_file=theFile.csv
set cvs_comma_separator=;

IF %cvs_file%==theFile.csv (
	ECHO Script not configured, please read Readme.txt &echo.
	pause
	exit
)

java -jar MapSchema.jar cvs %cvs_file% %cvs_comma_separator%
pause