<?php

class sql_parser
{
    private $rawSelect = "";
    private $rawFrom = "";
    private $rawJoin = "";
    private $rawWhere = "";

    public function __construct( ) {
    }

    public function setQuery( $query ) {
        $query = str_replace('"', "", $query );

        $this->rawSelect = $this->split_between( $query, "SELECT", "FROM" );
        $this->rawFrom = $this->split_between( $query, "FROM", "INNER JOIN|JOIN|WHERE|$" );
        $this->rawJoin = $this->split_between( $query, "INNER JOIN|JOIN", "WHERE|$", true );
        $this->rawWhere = $this->split_between( $query, "WHERE", "$", true );

        $test = [];
    }

    public function split_between( $query, $left, $right, $optional = false ) {
        $parts = preg_split('@('.$left.')@i', $query);
        if ( count($parts) == 1 ) {
            if ( !$optional ) {
                echo "Error splitting between [" . $left . "] and [" . $right ."]<br> Query: (" . $query . ")<br><br>";
            }
            return "";
        }
        $parts[1] = ltrim($parts[1]); // Remove fisrt space if exist.
        $parts = preg_split('@('.$right.')@i', $parts[1]);
        $parts[0] = trim($parts[0]); // Remove last space if exist.

        return $parts[0];
    }

    // =======================================================================================
    // Public functions
    // =======================================================================================


    public function parseSQL( $query = "" ) {
        $r = [];

        if ( !empty($query) ) $this->setQuery($query);

        // Select
        $r['select']['raw'] = $this->rawSelect;
        $r['select']['list'] = $this->regex_select_parse();

        // From
        $r['from']['raw'] = $this->rawFrom;
        $r['from']['list'] = $this->regex_from_getList();

        // Where
        $r['where']['raw'] = $this->rawWhere;


        return $r;
    }

    public function regex_getAliasInfo( $alias ) {

        $alias_list = $this->regex_select_getAliasList();

        foreach ( $alias_list as $a ) {
            if ( $a['alias'] == $alias ) return $a;
        }

        $aux['column'] = $alias;
        return $aux;
    }

    // =======================================================================================
    // SELECT
    // =======================================================================================

    public function regex_select_parse( ) {
        $select = $this->rawSelect;
        if ( empty($select) ) return;

        $selectAliasList = $this->regex_select_getAliasList( );
        $selectList = $this->regex_select_getList( );

        $retList = $selectAliasList;

        foreach ( $selectList as $col_or_tableRcol ) {

            if ( count($col_or_tableRcol) == 2 ) {
                // Is Table.Col
                $aux['table'] = $col_or_tableRcol[0];
                $aux['column'] = $col_or_tableRcol[1];
                $retList[] = $aux;

            } else {
                // Is a column or an alias.
                $isAlias = false;

                foreach( $selectAliasList as $r ) {
                    if ( $r['alias'] == $col_or_tableRcol[0] ) {
                        $isAlias = true;
                        break;
                    }
                }

                if ( !$isAlias ) {
                    $aux['column'] = $col_or_tableRcol[0];
                    $retList[] = $aux;
                }

            }

        }

        return $retList;

    }

    private function regex_select_getAliasList( ) {
        $select = $this->rawSelect;
        if ( empty($select) ) return;

        $matches = [];
        $regex = '@\\s*(?P<original>[a-zA-Z0-9_.]+) AS (?P<alias>[a-zA-Z0-9_]+),?\\s?@i';

        preg_match_all($regex, $select, $matches, PREG_SET_ORDER);

        $r = [];
        foreach($matches as $m) {
            $tableRcolumn = explode(".", $m['original']);
            if ( count($tableRcolumn) == 2 ) {
                $aux['table'] = $tableRcolumn[0];
                $aux['column'] = $tableRcolumn[1];
            } else {
                $aux['column'] = $tableRcolumn[0];
            }
            $aux['alias'] = $m['alias'];
            $r[] = $aux;
        }

        return $r;
    }

    private function regex_select_getList( ) {
        $select = $this->rawSelect;
        if ( empty($select) ) return;

        $matches = [];
        $regex = '@\\s*([a-zA-Z0-9_.]+)(,|$)\\s?@i';

        preg_match_all($regex, $select, $matches);

        $r = [];
        foreach ( $matches[1] as $m ) {
            $r[] = explode( ".", $m );
        }

        return $r;
    }

    // =======================================================================================
    // SELECT
    // =======================================================================================

    private function regex_from_getList( ) {
        $from = $this->rawFrom;
        if ( empty($from) ) return;

        $matches = [];
        $regex = '@\\s*([a-zA-Z0-9_.]+)(,|$)\\s?@i';

        preg_match_all($regex, $from, $matches);

        $r = [];
        foreach ( $matches[1] as $m ) {
            $parts = explode( ".", $m );
            if ( count($parts) == 2 ) {
                $aux['postgresGraph'] = $parts[0];
                $aux['table'] = $parts[1];
            } else {
                $aux['table'] = $parts[0];
            }
            $r[] = $aux;
        }

        return $r;
    }



}