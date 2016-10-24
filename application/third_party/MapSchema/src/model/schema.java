package model;



import com.thoughtworks.xstream.XStream;

import java.io.BufferedReader;
import java.io.FileInputStream;
import java.io.InputStreamReader;
import java.nio.file.Paths;
import java.sql.*;

/*
String databaseName = "mapon";
String userName = "mapon";
String password = "mapon";
String mySQLPort = "3306";
String  = "127.0.0.1";
*/



public class schema {

    public static String getMysqlSchema ( String databaseName, String userName, String password, String hostUrl, String dbPort ) throws SQLException, ClassNotFoundException {
        System.out.println("Connecting to MySql...");

        Class.forName("com.mysql.jdbc.Driver");
        Connection conn = DriverManager.getConnection("jdbc:mysql://" + hostUrl + ":" + dbPort + "/" + databaseName, userName, password);

        return generateSqlSchema( conn, "mysql", null, "%" );
    }


    public static String getPostgresqlSchema ( String databaseName, String schema_name, String userName, String password, String hostUrl, String dbPort ) throws SQLException, ClassNotFoundException {
        System.out.println("Connecting to Postgresql...");

        Class.forName("org.postgresql.Driver");
        Connection conn;

         if ( schema_name == null || schema_name.equals("")  ) {
             conn = DriverManager.getConnection("jdbc:postgresql://" + hostUrl + ":" + dbPort + "/" + databaseName, userName, password);
             return generateSqlSchema( conn, "postgres", null, "%" );
         }

        conn = DriverManager.getConnection("jdbc:postgresql://" + hostUrl + ":" + dbPort + "/" + databaseName + "?currentSchema=" + schema_name, userName, password);
        conn.setSchema(schema_name);

        return generateSqlSchema( conn, "postgres", schema_name, "%" );

    }

    public static String getOracleSchema ( String sid, String userName, String password, String hostUrl, String dbPort, String tableNamePattern ) throws SQLException, ClassNotFoundException {
        System.out.println("Connecting to OracleDB...");

        Class.forName("oracle.jdbc.OracleDriver");
        Connection conn = DriverManager.getConnection("jdbc:oracle:thin:@//" + hostUrl + ":" + dbPort + "/" + sid, userName, password);

        return generateSqlSchema( conn, "oracle", null, tableNamePattern );
    }

    public static String getCvsSchema ( String cvsFilePath, String commaSeparator ) {

        String fileName = Paths.get(cvsFilePath).getFileName().toString().split("\\.")[0];

        return generateCvsSchema( fileName, cvsFilePath, commaSeparator );

    }


    public static String generateSqlSchema ( Connection conn, String type, String schema, String tableNamePattern ) throws SQLException {

        XStream xs = new XStream();

        Database xml_db = new Database();

        xml_db.setType( type );

        DatabaseMetaData meta = conn.getMetaData();

        System.out.println("Parsing schema...");

        ResultSet resultSet = meta.getTables(null, schema, tableNamePattern, new String[] {"TABLE"});

        String tableName = "";

        while ( resultSet.next() ) {

            tableName = resultSet.getString("TABLE_NAME"); //3

            Table xml_tb = new Table(tableName);

            try {
                //System.out.println("Table: " + tableName);

                ResultSet colsResultSet = meta.getColumns(null, null, tableName, "%");
                while (colsResultSet.next()) {
                    String nombreColumna = colsResultSet.getString("COLUMN_NAME"); // 4
                    String tipoColumna = colsResultSet.getString("TYPE_NAME").toLowerCase(); // 6
                    //tipoColumna = getNameOfType( tipoColumna );

                    //System.out.println("Column Name of table " + tableName + " = " + nombreColumna + " -> " + tipoColumna );
                    xml_tb.addColumn(nombreColumna, tipoColumna);

                }
                colsResultSet.close();

                ResultSet pkResultSet = meta.getPrimaryKeys(null, null, tableName);
                while (pkResultSet.next()) {
                    String pk = pkResultSet.getString("COLUMN_NAME");
                    xml_tb.addPk(pk);
                }
                pkResultSet.close();

                ResultSet fkResultSet = meta.getImportedKeys(null, null, tableName);
                while (fkResultSet.next()) {
                    String fkTableName = fkResultSet.getString("FKTABLE_NAME");
                    String fkColumnName = fkResultSet.getString("FKCOLUMN_NAME");
                    String pkTableName = fkResultSet.getString("PKTABLE_NAME");
                    String pkColumnName = fkResultSet.getString("PKCOLUMN_NAME");
                    xml_tb.addFk(fkTableName, fkColumnName, pkTableName, pkColumnName);
                }
                fkResultSet.close();
            } catch( Exception e ) {
                System.err.println( "Error al añadir la tabla " + tableName );
                System.err.println( e.getMessage() );
            }

            xml_db.addTable(xml_tb);

        }

        resultSet.close();

        return xs.toXML(xml_db);
        //System.out.println("Test: " + xml);

    }

    public static String generateCvsSchema ( String tableName, String cvsFilePath, String commaSeparator ) {
        String[] columns, types, desiciones, line;
        String aux, finalXml;
        int i, k;

        finalXml = "";

        try {
            FileInputStream fstream = new FileInputStream(cvsFilePath);
            BufferedReader br = new BufferedReader(new InputStreamReader(fstream, "UTF8"));

            columns = br.readLine().split(commaSeparator);
            desiciones = new String[columns.length];
            types = new String[columns.length];

            line = br.readLine().split(commaSeparator);

            // Set column types
            for ( i = 0; i < line.length; i++ ) {
                types[i] = getDataType(line[i]);
                desiciones[i] = line[i];
            }

            // Check type.
            // Si anteriormente hemos detectado un numero "123" y en otra linea hay "123Z" lo cambiamos a string.
            k = 0;
            while ( (aux = br.readLine()) != null && k < 1000 )   {
                line = aux.split(commaSeparator);

                for ( i = 0; i < line.length; i++ ) {

                    if ( !line[i].trim().isEmpty() && getDataType( line[i] ).equals("string") ) {
                        types[i] = "string";
                        desiciones[i] = line[i];
                    }
                }
                k++;
            }

            br.close();


            XStream xs = new XStream();

            Database xml_db = new Database();

            xml_db.setType( "cvs" );

            Table xml_tb = new Table( tableName );

            for ( i = 0; i < columns.length; i++ ) xml_tb.addColumn( columns[i], types[i] );

            System.out.println("Desiciones tomadas:");
            System.out.println("Columna => DatoUsado (Tipo)");
            System.out.println("---------------------------");
            for ( i = 0; i < columns.length; i++ ){
                System.out.println( columns[i] + " => " + desiciones[i] + "(" + types[i] + ")" );
            }

            xml_db.addTable(xml_tb);
            finalXml = xs.toXML(xml_db);

        } catch (Exception e) {
            e.printStackTrace();

        }

        return finalXml;



    }

    private static String getNameOfType( String sFieldType ) {

        sFieldType = sFieldType.toLowerCase();

        String type_string = "string";
        String type_date = "dateTime";
        String type_int = "int";
        String type_float = "float";
        String type_boolean = "bool";


        switch ( sFieldType ) {
            case "char": return type_string;
            case "varchar": return type_string;
            case "text": return type_string;
            case "date": return type_date;
            case "datetime": return type_date;
            case "int": return type_int;
            case "int unsigned": return type_int;
            case "mediumint unsigned": return type_int;
            case "real": return type_float;
            case "float": return type_float;
            case "decimal": return type_float;
            case "double": return type_float;
            case "bit": return type_boolean;
            case "int2": return type_int;
            case "bool": return type_boolean;
            case "bytea": return type_string;
            case "numeric": return type_int;
            case "timestamp": return type_date;

        }


        System.out.println("Type not found: " + sFieldType);

        return sFieldType;
    }

    private static String getDataType( String data ) {
        data = data.replaceAll( ",", "." );

        try {
            Integer.parseInt(data);
            return "int";
        } catch (NumberFormatException e) {}

        try {
            Double.parseDouble(data);
            return "float";
        } catch (NumberFormatException e) {}

        return "string";
    }

}



