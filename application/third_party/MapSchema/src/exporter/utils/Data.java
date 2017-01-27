package exporter.utils;

public class Data {
    private String data;
    private Type.dt datatype;


    public void setOracle( String typeName, String data ) {
        this.datatype = Type.oracleTypeToGeneralType( typeName );
        this.data = data;
    }

    public String getMySQL() {
        if ( data == null ) return null;

        data = data.replaceAll("\'", "\\\\'");

        if ( datatype == Type.dt.VARCHAR || datatype == Type.dt.TEXT || datatype == Type.dt.CHAR ) {
            return "'" + data + "'";
        }

        if ( datatype == Type.dt.DATE || datatype == Type.dt.DATE_TIME ) {
            return "'" + data + "'";
            //String[] date_parts = data.split("/");
            //return date_parts[2] + "/" + date_parts[1] + "/" + date_parts[0];
        }



        return data;
    }




}
