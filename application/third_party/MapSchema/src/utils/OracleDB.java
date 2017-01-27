package utils;


import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.ResultSet;
import java.util.ArrayList;
import java.util.List;

public class OracleDB {

    public String hostName = "localhost";
    public int port = 1521;
    public String user = "";
    public String pass = "";
    public String sid = "xe";
    public String tablePattern = "";
    public Connection conn;

    public class Column {
        public String name;
        public String type;
        public int size;
        public int nullable;
    }

    public class Table {
        public String name;
    }

    public void setProperties( String user, String pass, String tablePattern ) {
        this.user = user;
        this.pass = pass;
        this.tablePattern = tablePattern;
    }

    public boolean open() {
        try {
            Class.forName("oracle.jdbc.OracleDriver");
            String c = "jdbc:oracle:thin:@//"+hostName+":"+port+"/"+sid;
            conn = DriverManager.getConnection( c, user, pass );

        } catch (Exception e) {
            e.printStackTrace();
            return false;
        }

        return true;
    }
    public boolean close() {
        try {
            conn.close();
        } catch (Exception e) {
            e.printStackTrace();
            return false;
        }
        return true;
    }


	// for (Column col : colList)
    public List<Column> getColumns( String tableName ) {
        List<Column> columList = new ArrayList<>();

        try {
            ResultSet res = this.conn.getMetaData().getColumns( null, this.user, tableName, "%" );

            while ( res.next() ) {
                Column c = new Column();
                c.name = res.getString("COLUMN_NAME");
                c.type = res.getString("TYPE_NAME");
                c.size = res.getInt("COLUMN_SIZE");
                c.nullable = res.getInt("NULLABLE");
                columList.add( c );
            }
            res.close();

        } catch (Exception e) {
            e.printStackTrace();
        }

        return columList;
    }

    public List<Table> getTables( ) {
        List<Table> tableList = new ArrayList<>();

        try {
            ResultSet res = this.conn.getMetaData().getTables(null, this.user, this.tablePattern, new String[] {"TABLE"});

            while ( res.next() ) {
                Table t = new Table();
                t.name = res.getString("TABLE_NAME");
                tableList.add(t);
            }

            res.close();

        } catch (Exception e) {
            e.printStackTrace();
        }

        return tableList;
    }

    public ResultSet executeQuery( String sql ) throws java.sql.SQLException {
        return this.conn.createStatement().executeQuery(sql);
    }
    public int executeUpdate( String sql ) throws java.sql.SQLException {
        return this.conn.createStatement().executeUpdate(sql);
    }


}
