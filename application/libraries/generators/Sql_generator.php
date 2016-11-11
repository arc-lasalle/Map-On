<?php


class sql_generator
{

    /*
    Example:
    $query['select']
                    [0]['table'] = usuario
                    [0]['column'] = id
                    [1]['table'] = documento
                    [1]['column'] = sha
    $query['from']
                    [0]['postgresGraph'] = theGraph
                    [0]['table'] = usuario
                    [1]['postgresGraph'] = theGraph
                    [1]['table'] = documento

    $query['join']
                    [0]['table'] = usuario
                    [0]['table1'] = usuario
                    [0]['column1'] = id
                    [0]['table2'] = usuario
                    [0]['column2'] = id
    $query['where']
                    [0]['table'] = usuario
                    [0]['column'] = id
    */

    public function generateSql( $sqlParts, $q = "" ) {

        $i = 0;
        $sql = "SELECT ";

        foreach ( $sqlParts['select'] as $select ) {
            if ( $i++ > 0 ) $sql .= ", ";
            $sql .= $q . $select['table'] . $q . "." . $q . $select['column'] . $q;
            $sql .= " AS " . $q . $select['table'] . $select['column'] . $q;
        }

        $i = 0;
        $sql .= " FROM ";

        foreach ( $sqlParts['from'] as $from ) {
            if ( $i++ > 0 ) $sql .= ", ";

            if ( !empty($from['postgresGraph']) ) {
                $sql .= $q . $from['postgresGraph'] . $q . ".";
            }

            $sql .= $q . $from['table'] . $q;
        }

        foreach ( $sqlParts['join'] as $join ) {
            $sql .= " INNER JOIN " . $join['table'];
            $sql .= " ON ";
            $sql .=       $q . $join['table1'] . $q . "." . $q . $join['column1'] . $q;
            $sql .=       " = ";
            $sql .=       $q . $join['table2'] . $q . "." . $q . $join['column2'] . $q;
        }

        if ( !empty($sqlParts['where']) ) {

            $i = 0;
            $sql .= " WHERE ";

            foreach ( $sqlParts['where'] as $where ) {
                if ($i++ > 0) $sql .= " AND ";

                $sql .= $q . $where['table'] . $q . "." . $q . $where['column'] . $q . " IS NOT NULL";
            }

        }

        return $sql;

    }

    /*
    public function generateSql_old( $selects, $tables, $joins, $quote = "" ) {

        $sql = "SELECT ";

        $selkeys = array_keys($selects);

        for ( $i = 0; $i < count($selkeys); $i++ ) {
            $sql = $sql.$selkeys[$i]." AS ".strtolower(str_replace(".", "", $selkeys[$i]));

            if($i < count($selkeys) -1) $sql .= ", ";
        }

        $tabkeys = array_keys ($tables);
        $joikeys = array_keys ($joins);

        if ( count($tabkeys) > 0 ) {
            $sql .= " FROM ".$tabkeys[0];

            for($i = 1; $i < count($tabkeys); $i++) {
                $sql .= " INNER JOIN ".$tabkeys[$i]. " ON ".$joikeys[$i-1];
            }
        }

        // where to control not null values
        if( count($selkeys) > 0)
            $sql = $sql . " WHERE ";

        for($i = 0; $i < count($selkeys); $i++) {
            $sql = $sql.$selkeys[$i]." IS NOT NULL";

            if($i < count($selkeys) -1) $sql .= " AND ";
        }

        return $sql;

    }*/


}




/*
//old method
$sql .= " FROM ";

$tabkeys = array_keys ($tables);
for($i = 0; $i < count($tabkeys); $i++) {
    $sql = $sql.$tabkeys[$i];

    if($i < count($tabkeys) -1) $sql .= ", ";
}

$joikeys = array_keys ($joins);

if(count($joikeys) > 0) {
    $sql .= " WHERE ";

    for($i = 0; $i < count($joikeys); $i++) {
        $sql = $sql.$joikeys[$i];

        if($i < count($joikeys) -1) $sql .= " AND ";
    }
}
        */