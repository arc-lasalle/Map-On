package exporter.utils;

public class Type {
    public static enum dt {
        UNKNOWN, TEXT, VARCHAR, NUMBER, DATE, DATE_TIME, CHAR, BLOB
    }
    private int size;
    private int digits;
    private dt datatype;


    public void setOracle( String typeName, int size, int digits ) {
        this.datatype = oracleTypeToGeneralType( typeName );
        this.size = size;
        this.digits = digits;
    }

    public String getMySQL( boolean full ) {

        if ( this.datatype == dt.VARCHAR ) {
            if ( !full ) return "varchar";
            if ( this.size <= 0 ) return "varchar(255)";
            return "varchar(" + this.size + ")";

        } else if ( this.datatype == dt.NUMBER ) {
            if ( !full ) {
                if ( digits > 0 ) return "numeric";
                return "int";
            }
            if ( size <= 0 ) return "int(11)";
            if ( digits <= 0 ) return "int(" + size + ")";
            return "numeric(" + size + "," + digits + ")";

        } else if ( this.datatype == dt.DATE || this.datatype == dt.DATE_TIME ) {
            return "timestamp";
        } else if ( this.datatype == dt.CHAR ) {
            if ( !full || this.size <= 0 ) return "char";
            return "char(" + size + ")";

        } else if ( this.datatype == dt.TEXT ) {
            if ( size <= 0 ) return "text(" + size + ")";
            return "text";

        } else if ( this.datatype == dt.BLOB ) {
            if ( size <= 0 ) return "blob(" + size + ")";
            return "blob";

        } else if ( this.datatype == dt.UNKNOWN ) {
            System.err.println("Generated invalid mysql query due to an unknown type.");

        } else {
            System.err.println("Error, MySql type not controlled.");
        }


        return "UNKNOWN";
    }




    public static dt oracleTypeToGeneralType( String typeName ) {
        switch ( typeName.toLowerCase() ) {
            case "varchar": return dt.VARCHAR;
            case "varchar2": return dt.VARCHAR;
            case "number": return dt.NUMBER;
            case "date": return dt.DATE;
            case "timestamp(6)": return dt.DATE_TIME; //El tipo tiene el (6) incluido.
            case "char": return dt.CHAR;
            case "blob": return dt.BLOB;
        }

        System.err.println("Oracle type not foundd: " + typeName.toLowerCase());
        return dt.UNKNOWN;
    }


    public static boolean isStringType( String typeName ) {
        dt typeNum = oracleTypeToGeneralType( typeName );
        return ( typeNum == dt.VARCHAR || typeNum == dt.TEXT );
    }


}
