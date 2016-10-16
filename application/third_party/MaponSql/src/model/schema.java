package model;



import com.thoughtworks.xstream.XStream;
import java.sql.*;


/*
String databaseName = "mapon";
String userName = "mapon";
String password = "mapon";
String mySQLPort = "3306";
String  = "127.0.0.1";
*/



public class schema {

    public static String getMysqlSchema ( String databaseName, String userName, String password, String dbPort, String hostUrl ) throws SQLException, ClassNotFoundException {
        System.out.println("Connecting to MySql...");

        Class.forName("com.mysql.jdbc.Driver");
        Connection conn = DriverManager.getConnection("jdbc:mysql://" + hostUrl + ":" + dbPort + "/" + databaseName, userName, password);

        return generateSchema( conn, null );
    }


    public static String getPostgresqlSchema ( String databaseName, String schema_name, String userName, String password, String dbPort, String hostUrl ) throws SQLException, ClassNotFoundException {
        System.out.println("Connecting to Postgresql...");

        Class.forName("org.postgresql.Driver");
        Connection conn;

         if ( schema_name == null || schema_name.equals("")  ) {
             conn = DriverManager.getConnection("jdbc:postgresql://" + hostUrl + ":" + dbPort + "/" + databaseName, userName, password);
             return generateSchema( conn, null );
         }

        conn = DriverManager.getConnection("jdbc:postgresql://" + hostUrl + ":" + dbPort + "/" + databaseName + "?currentSchema=" + schema_name, userName, password);
        conn.setSchema(schema_name);

        return generateSchema( conn, schema_name );

    }

    public static String getOracleSchema ( String databaseName, String userName, String password, String dbPort, String hostUrl ) throws SQLException, ClassNotFoundException {
        System.out.println("Connecting to OracleDB...");

        Class.forName("oracle.jdbc.OracleDriver");
        Connection conn = DriverManager.getConnection("jdbc:oracle:thin:@//" + hostUrl + ":" + dbPort + "/" + databaseName, userName, password);

        return generateSchema( conn, null );
    }

    public static String generateSchema ( Connection conn, String schema ) throws SQLException, ClassNotFoundException {

        XStream xs = new XStream();

        Database xml_db = new Database();

        DatabaseMetaData meta = conn.getMetaData();

        System.out.println("Parsing schema...");

        ResultSet resultSet = meta.getTables(null, schema, "%", new String[] {"TABLE"});

        String tableName = "";

        while (resultSet.next()) {

            tableName = resultSet.getString("TABLE_NAME"); //3

            Table xml_tb = new Table( tableName );

            //System.out.println("Table: " + tableName);

            ResultSet colsResultSet = meta.getColumns(null, null, tableName, "%");
            ResultSet pkResultSet = meta.getPrimaryKeys(null, null, tableName);
            ResultSet fkResultSet = meta.getImportedKeys(null, null, tableName);

            while ( colsResultSet.next() ) {
                String nombreColumna = colsResultSet.getString("COLUMN_NAME"); // 4
                String tipoColumna = colsResultSet.getString("TYPE_NAME").toLowerCase(); // 6
                //tipoColumna = getNameOfType( tipoColumna );

                //System.out.println("Column Name of table " + tableName + " = " + nombreColumna + " -> " + tipoColumna );

                xml_tb.addColumn(nombreColumna, tipoColumna);

            }

            while (pkResultSet.next()) {
                String pk = pkResultSet.getString("COLUMN_NAME");
                xml_tb.addPk( pk );
            }

            while (fkResultSet.next()) {
                String fkTableName = fkResultSet.getString("FKTABLE_NAME");
                String fkColumnName = fkResultSet.getString("FKCOLUMN_NAME");
                String pkTableName = fkResultSet.getString("PKTABLE_NAME");
                String pkColumnName = fkResultSet.getString("PKCOLUMN_NAME");
                xml_tb.addFk( fkTableName, fkColumnName, pkTableName, pkColumnName );
            }

            xml_db.addTable(xml_tb);

        }

        resultSet.close();

        return xs.toXML(xml_db);
        //System.out.println("Test: " + xml);

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



}



