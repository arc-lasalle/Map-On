package controller;


import java.io.*;

public class Main {

    public static void main(String[] args) {

        if ( args.length < 6 ) {
            System.out.println("Missing arguments. Usage:");
            System.out.println("MySql:");
            System.out.println( "  java -jar MaponSql.jar mysql <database_name> <user_name> <password> <IP> <Port>");
            System.out.println("Postgres:");
            System.out.println( "  java -jar MaponSql.jar postgres <database_name> [<schema>] <user_name> <password> <IP> <Port>");
            return;
        }

        String dbType, schema_name, databaseName, userName, password, ip, dbPort, schema;
        schema = "";
        schema_name = null;

        int of = 0;
        dbType = args[0+of].toLowerCase();
        databaseName = args[1+of];
        if ( args.length == 7 ) {
            schema_name = args[2+of]; of++;
        }
        userName = args[2+of];
        password = args[3+of];
        ip = args[4+of];
        dbPort = args[5+of];




        try {

            if ( dbType.equals("mysql") ) {

                schema = model.schema.getMysqlSchema(databaseName, userName, password, dbPort, ip );

            } else if ( dbType.equals("postgres") || dbType.equals("postgresql") ) {

                schema = model.schema.getPostgresqlSchema(databaseName, schema_name, userName, password, dbPort, ip );

            } else if ( dbType.equals("oracle") || dbType.equals("oracledb") ) {

                schema = model.schema.getOracleSchema(databaseName, userName, password, dbPort, ip );

            } else {

                throw new IllegalArgumentException("Invalid '" + dbType + "' argument. Supported databases: [mysql|postgres]" );
            }




            System.out.println("Creating xml file...");

            PrintWriter writer = new PrintWriter(databaseName + ".xml", "UTF-8");
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
}
