package controller;


import java.io.*;
import java.nio.file.Paths;

import static model.schema.*;

public class Main {

    public static void main(String[] args) {

        /*
        if ( args.length < 6 ) {
            System.out.println("Missing arguments. Usage:");
            System.out.println("MySql:");
            System.out.println( "  java -jar MaponSql.jar mysql <database_name> <user_name> <password> <IP> <Port>");
            System.out.println("Postgres:");
            System.out.println( "  java -jar MaponSql.jar postgres <database_name> [<schema>] <user_name> <password> <IP> <Port>");
            return;
        }*/

        if ( !checkArguments( args ) ) return;

        String schema, dbType, outputFileName;

        outputFileName = "schema";
        dbType = args[0].toLowerCase();

        try {

            if ( dbType.equals("mysql") ) {

                // databaseName, userName, password, dbPort, ip
                schema = getMysqlSchema( args[1], args[2], args[3], args[4], args[5] );
                outputFileName = args[1];

            } else if ( dbType.equals("postgres") || dbType.equals("postgresql") ) {

                // databaseName, schema_name, userName, password, dbPort, ip
                if ( args.length == 7 ) {
                    // Nos pasan el schema name como segundo argumento.
                    schema = getPostgresqlSchema( args[1], args[2], args[3], args[4], args[5], args[6] );
                } else {
                    schema = getPostgresqlSchema( args[1], "", args[2], args[3], args[4], args[5] );
                }
                outputFileName = args[1];


            } else if ( dbType.equals("oracle") || dbType.equals("oracledb") ) {

                System.out.println( "Table pattern: (" + args[6] + ")" );

                // databaseName, userName, password, dbPort, ip, tableNamePattern
                schema = getOracleSchema( args[1], args[2], args[3], args[4], args[5], args[6] );
                outputFileName = args[1];

            } else if ( dbType.equals("cvs") ) {

                // cvsFilePath, commaSeparator
                if ( args.length == 3 ) {
                    schema = getCvsSchema( args[1], args[2] );
                } else {
                    schema = getCvsSchema( args[1], "," );
                }
                outputFileName = Paths.get(args[1]).getFileName().toString().split("\\.")[0];

            } else {

                throw new IllegalArgumentException("Invalid '" + dbType + "' argument. Supported databases: [mysql|postgres]" );
            }




            System.out.println("Creating xml file...");

            PrintWriter writer = new PrintWriter(outputFileName + ".xml", "UTF-8");
            writer.println(schema);
            writer.close();

            System.out.println("Done.");

        } catch (Exception e) {

            System.out.println("An error occurred.");
            if ( e.getCause() != null) System.err.println(e.getCause());
            if ( e.getMessage() != null ) System.err.println(e.getMessage());

        }

        //System.out.println(schema);

    }


    public static boolean checkArguments ( String[] args ) {
        String dbName = "";
        int err = 0;

        if ( args.length > 0 ) {
            dbName = args[0];
            err = -1;
        }


        if ( dbName.equals("mysql") && args.length != 6 ) err = 1;
        if ( (dbName.equals("postgres") || dbName.equals("postgresql")) && args.length != 6 && args.length != 7 )err = 2;
        if ( (dbName.equals("oracle") || dbName.equals("oracledb"))  && args.length != 7 ) err = 3;
        if ( dbName.equals("cvs") && args.length != 2 && args.length != 3 ) err = 4;

        if (    !dbName.equals("mysql") && !dbName.equals("postgres") && !dbName.equals("postgresql") &&
                !dbName.equals("oracle") && !dbName.equals("oracledb") && !dbName.equals("cvs")
                ) {
            err = 0;
        }

        if ( err >= 0 ) System.out.println("Missing arguments. Usage:");

        if ( err == 0 || err == 1 ) {
            System.out.println("MySql:");
            System.out.println( "  java -jar MapSchema.jar mysql <database_name> <user_name> <password> <IP> <Port>");
        }

        if ( err == 0 || err == 2 ) {
            System.out.println("Postgres:");
            System.out.println( "  java -jar MapSchema.jar postgres <database_name> [<schema>] <user_name> <password> <IP> <Port>");
        }

        if ( err == 0 || err == 3 ) {
            System.out.println("Oracle:");
            System.out.println( "  java -jar MapSchema.jar oracledb <sid> <user_name> <password> <IP> <Port> <tableNamePattern>");
        }

        if ( err == 0 || err == 4 ) {
            System.out.println("CVS:");
            System.out.println( "  java -jar MapSchema.jar cvs <path_to_cvs_file> [<comma_separator>]");
        }


        return (err == -1);
    }


}
