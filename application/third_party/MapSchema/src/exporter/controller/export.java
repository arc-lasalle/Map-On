package exporter.controller;


import com.thoughtworks.xstream.XStream;
import exporter.utils.Data;
import exporter.utils.Type;
import utils.MysqlDB;
import utils.OracleDB;
import utils.OracleDB.Column;
import utils.OracleDB.Table;
import java.sql.*;
import java.util.ArrayList;
import java.util.List;

public class export {

    public static void main(String[] args) {
        new export();
    }

    public export() {

        OracleDB oracle = new OracleDB();
        MysqlDB mysql = new MysqlDB();

        oracle.setProperties( "User1", "Pass1", "XML_%" );
        mysql.setProperties( "User1", "Pass1", "newDatabase" );

        mysql.open();
        oracle.open();

        doExport( oracle, mysql, false );

        oracle.close();
        mysql.close();

    }

    public void doExport(OracleDB oracle, MysqlDB mysql, boolean drop ) {

        try {

            if ( drop ) mysql.conn.createStatement().executeUpdate("DROP DATABASE IF EXISTS " + mysql.dbName);
            mysql.conn.createStatement().executeUpdate("CREATE DATABASE IF NOT EXISTS " + mysql.dbName);

            System.out.println("+------------------------------------------------------------------------------------+");
            System.out.println("| Generating SQL                                                                     |");
            System.out.println("+------------------------------------------------------------------------------------+");
            List<String> createTableSqls = generateSqlSchema( oracle, mysql );

            System.out.println("+------------------------------------------------------------------------------------+");
            System.out.println("| Executing SQL                                                                      |");
            System.out.println("+------------------------------------------------------------------------------------+");
            for ( String sql: createTableSqls) {
                System.out.println( sql );
                mysql.conn.createStatement().executeUpdate( sql );
            }

            dumpData( oracle, mysql );


        } catch (Exception e) {
            e.printStackTrace();
        }
    }




    public List<String> generateSqlSchema ( OracleDB oracle, MysqlDB mysql ) throws SQLException {

        DatabaseMetaData meta = oracle.conn.getMetaData();

        ResultSet resultSet = meta.getTables(null, oracle.user, oracle.tablePattern, new String[] {"TABLE"});

        String tableName, pks, sql;
        Type ty = new Type();

        List<String> result = new ArrayList();

        // Tables
        while ( resultSet.next() ) {

            tableName = resultSet.getString("TABLE_NAME");

            sql = "CREATE TABLE IF NOT EXISTS " + mysql.dbName + "." + tableName + " ( \n";

            try {
                // Columns
                ResultSet colsResultSet = meta.getColumns(null, oracle.user, tableName, "%");
                while ( colsResultSet.next() ) {
                    sql += "  " + colsResultSet.getString("COLUMN_NAME") + " ";

                    ty.setOracle(
                            colsResultSet.getString("TYPE_NAME"),
                            colsResultSet.getInt("COLUMN_SIZE"),
                            colsResultSet.getInt("DECIMAL_DIGITS")
                    );

                    sql += ty.getMySQL(true) + ", \n";


                }
                colsResultSet.close();

                sql = sql.substring(0, sql.length()-3);

                // Primary keys
                ResultSet pkResultSet = meta.getPrimaryKeys(null, oracle.user, tableName);
                pks = "";
                while ( pkResultSet.next() ) {
                    pks += pkResultSet.getString("COLUMN_NAME") + ", ";
                }
                if ( pks.length() > 0 ) {
                    pks = pks.substring(0, pks.length()-2);
                    sql += ",\n  PRIMARY KEY ( " + pks + " )";
                }
                pkResultSet.close();

                // FK's
                /*
                ResultSet fkResultSet = meta.getImportedKeys(null, oracle.user, tableName);
                while (fkResultSet.next()) {
                    String fkTableName = fkResultSet.getString("FKTABLE_NAME");
                    String fkColumnName = fkResultSet.getString("FKCOLUMN_NAME");
                    String pkTableName = fkResultSet.getString("PKTABLE_NAME");
                    String pkColumnName = fkResultSet.getString("PKCOLUMN_NAME");
                    xml_tb.addFk(fkTableName, fkColumnName, pkTableName, pkColumnName);
                }
                fkResultSet.close();
                */
                sql += "\n) ENGINE=InnoDB DEFAULT CHARSET=utf8;\n\n";
            } catch( Exception e ) {
                System.err.println( "Error al añadir la tabla " + tableName );
                System.err.println( e.getMessage() );
            }

            result.add( sql );
            System.out.println( sql );
        }

        resultSet.close();



        return result;
    }




    public void dumpData( OracleDB oracle, MysqlDB mysql ) throws SQLException {
        List<Column> colList;
        Data d = new Data();
        String sql;

        System.out.println("+------------------------------------------------------------------------------------+");
        System.out.println("| Data Dump                                                                          |");
        System.out.println("+------------------------------------------------------------------------------------+");

        List<Table> tableList = oracle.getTables();

        for ( Table table : tableList ) {

            sql = "SELECT * FROM " + oracle.user + "." + table.name;
            System.out.println(sql);
            ResultSet rs = oracle.executeQuery(sql);

            colList = oracle.getColumns( table.name );

            while ( rs.next() ) {
                String insertValues = "";
                int i = 1;

                for ( Column col : colList ) {
                    d.setOracle( col.type, rs.getString(i) );

                    insertValues += d.getMySQL() + ",";

                    i++;
                }

                if (insertValues.length() > 0) {
                    try {
                        insertValues = insertValues.substring(0, insertValues.length() - 1);

                        sql = "INSERT INTO " + mysql.dbName + "." + table.name + " VALUES (" + insertValues + ")";

                        mysql.executeUpdate(sql);
                    } catch (Exception e) {
                        boolean repeatedPK =  e.getMessage().substring(e.getMessage().length()-17).equals("for key 'PRIMARY'");
                        System.err.println(sql + " " + repeatedPK);
                        if ( !repeatedPK ) e.printStackTrace();
                    }

                }

            }

            rs.close();

        }
    }


}
