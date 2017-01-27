package utils;


import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.ResultSet;

public class MysqlDB {

    public String hostName = "localhost";
    //public int port;
    public String user = "";
    public String pass = "";
    public String dbName = "";
    public Connection conn;

    public void setProperties() {

    }

    public void setProperties( String user, String pass, String dbName ) {
        this.user = user;
        this.pass = pass;
        this.dbName = dbName;
    }

    public void open() {
        try {
            String c = "jdbc:mysql://"+hostName+"/?user="+user+"&password="+pass;
            conn = DriverManager.getConnection( c );
        } catch (Exception e) {
            e.printStackTrace();
        }
    }
    public void close() {
        try {
            conn.close();
        } catch (Exception e) {
            e.printStackTrace();
        }
    }


    public ResultSet executeQuery(String sql ) throws java.sql.SQLException {
        return this.conn.createStatement().executeQuery(sql);
    }
    public int executeUpdate( String sql ) throws java.sql.SQLException {
        return this.conn.createStatement().executeUpdate(sql);
    }

}
