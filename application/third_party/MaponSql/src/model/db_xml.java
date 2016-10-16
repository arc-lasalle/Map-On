package model;


import java.util.ArrayList;
import java.util.List;



class Column {
    public String name;
    public String type;
}

class foreginKey {
    public String fkTableName;
    public String fkColumnName;
    public String pkTableName;
    public String pkColumnName;
}


class Table {
    private String name;
    private List<Column> columns;
    private List<String> primaryKeys;
    private List<foreginKey> foreginKeys;

    public Table( String name ) {
        columns = new ArrayList<Column>();
        primaryKeys = new ArrayList<String>();
        foreginKeys = new ArrayList<foreginKey>();
        this.name = name;
    }

    public void addColumn( String name, String type ) {
        Column col = new Column();
        col.name = name;
        col.type = type;
        columns.add(col);
    }

    public void addFk( String fkTableName, String fkColumnName, String pkTableName, String pkColumnName ) {
        foreginKey fk = new foreginKey();
        fk.fkTableName = fkTableName;
        fk.fkColumnName = fkColumnName;
        fk.pkTableName = pkTableName;
        fk.pkColumnName = pkColumnName;
        foreginKeys.add(fk);
    }

    public void addPk( String pkColumnName ) {
        primaryKeys.add( pkColumnName );
    }
}


class Database {

    private List<Table> tables = new ArrayList<Table>();

    public void addTable( Table tbl ) {
        this.tables.add( tbl );
    }

}
