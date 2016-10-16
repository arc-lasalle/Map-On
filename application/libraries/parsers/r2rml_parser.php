<?php


class r2rml_parser extends CI_Model {

    public function __construct( ) {
        parent::__construct();
        $this->load->library('parsers/sql_parser', 'sql_parser');
    }

    public function parse( $r2rml_file ) {

        include_once("public/arc2/ARC2.php");
        require_once "public/easyrdf/EasyRdf.php";

        $graph = new EasyRdf_Graph();
        $graph->parseFile($r2rml_file, "turtle");

        $ret = [];

        foreach ( $graph->allOfType('rr:TriplesMap') as $tripleMap ) {
            $ret['triplesMap'][] = $this->parseTripleMap( $tripleMap );
        }

        return $ret;

    }



    private function parseTripleMap( $tripleMap ) {

        $tm = [];
        $tm['logicalTable'] = "";
        $tm['subjectMap'] = "";
        $tm['predicateObjectMap'] = [];

        $tm['logicalTable'] = $this->tripleMap_parseLogicalTable( $tripleMap );

        $tm['subjectMap'] = $this->tripleMap_parseSubjectMap( $tripleMap, $tm['logicalTable'] ); //todo

        $tm['predicateObjectMap'] = $this->tripleMap_parsePredicateObjectMaps( $tripleMap, $tm['logicalTable'] );

        return $tm;
    }


    public function tripleMap_parseLogicalTable( $tripleMap ) {
        $lt['isQuery'] = false;
        $lt['query'] = "";
        $lt['table'] = "";
        $lt['postgresGraph'] = "";

        // Parse logical table
        $logicalTable = $tripleMap->get('rr:logicalTable');

        if ( $logicalTable !== null ) {

            $tablename = $logicalTable->getLiteral('rr:tableName');

            if ( $tablename != null ) {
                $lt['isQuery'] = false;
                $lt['query'] = "";
                $lt['table'] = $tablename->getValue();

                $tableParts = explode( ".", $lt['table'] );
                if ( count($tableParts) > 1 ) {
                    $lt['postgresGraph'] = $tableParts[0];
                    $lt['table'] = $tableParts[1];
                }

            }

            $sqlquery = $logicalTable->getLiteral('rr:sqlQuery');

            if ( $sqlquery != null ){
                $lt['isQuery'] = true;
                $lt['query'] = str_replace('"', "", $sqlquery->getValue() );

                $queryParts = $this->sql_parser->parseSQL( $lt['query'] );
                $lt['table'] = $queryParts['from']['list'][0]['table'];
                if ( !empty($queryParts['from']['list'][0]['postgresGraph']) ) {
                    $lt['postgresGraph'] = $queryParts['from']['list'][0]['postgresGraph'];
                }
            }

        }

        return $lt;
    }

    public function tripleMap_parseSubjectMap( $tripleMap, $logicalTable ) {
        $sm['template'] = "";
        $sm['table'] = "";
        $sm['column'] = "";
        $sm['class'] = "";

        // Parse subject map
        $subjectMap = $tripleMap->get('rr:subjectMap');

        if( $subjectMap != null ) {
            $template = $subjectMap->get('rr:template');

            if ( $template != null ) {
                $auxSubjectTemplate = $template->getValue();

                // Extract the colum from the subject template /aaa/bbb/{"column"}
                $value = $this->parseTemplate($auxSubjectTemplate, $logicalTable['query'], $logicalTable['table']);

                if ( $logicalTable['isQuery'] == false ) {
                    $auxSubjectTemplate = str_replace($value['column'], $value['table'].$value['column'], $auxSubjectTemplate);
                }

                $sm['template'] = $auxSubjectTemplate;
                $sm['table'] = $value['table'];
                $sm['column'] = $value['column'];
            }

            $class = $subjectMap->get('rr:class');

            if ( $class != null ) {
                $sm['class'] = ''.$class;
            }

        }

        return $sm;
    }

    public function tripleMap_parsePredicateObjectMaps( $tripleMap, $logicalTable ) {

        $poms = [];

        foreach ( $tripleMap->all('rr:predicateObjectMap') as $predicatemap ) {

            $pom = [];
            $pom['predicate'] = "";
            $pom['template'] = "";
            $pom['table'] = "";
            $pom['column'] = "";

            // Parse predicate
            $pom['predicate'] = ''.$predicatemap->get('rr:predicate');

            // Parse object map
            $objectMap = $predicatemap->get('rr:objectMap');

            if ( $objectMap != null ) {

                $column = $objectMap->get("rr:column");

                if ( $column !== null ) {
                    // It can be an alias. Change the alias with the real table.column
                    $this->sql_parser->setQuery($logicalTable['query']);
                    $aliasInfo = $this->sql_parser->regex_getAliasInfo($column);
                    $pom['table'] = $aliasInfo['table'];
                    $pom['column'] = $aliasInfo['column'];
                }

                $template = $objectMap->get("rr:template");

                if ( $template !== null ) {

                    $value = $this->parseTemplate($template, $logicalTable['query'], $logicalTable['table']);

                    $pom['table'] = $value['table'];
                    $pom['column'] = $value['column'];

                    if ( $logicalTable['isQuery'] === false ) {
                        $template = str_replace($value['column'], $value['table'] . $value['column'], $template);
                    }

                    $pom['template'] = ''.$template;

                }

            }

            $poms[] = $pom;
        }

        return $poms;
    }



    private function parseTemplate ( $template, $query, $table ) {
        $ret['table'] = "";
        $ret['column'] = "";

        $inx = stripos($template, "{");
        if ($inx !== false) {
            $templateValue = substr($template, $inx+1, stripos($template, "}") -$inx-1);
        }

        $templateValue = str_replace( '"', "", $templateValue );

        if ( $query === "" || $query === null ) {
            // Is not an alias. Append the table to the column.
            $ret['table'] = $table;
            $ret['column'] = $templateValue;
        } else {
            // It can be an alias. Change the alias with the real table.column
            $this->sql_parser->setQuery($query);
            $aliasInfo = $this->sql_parser->regex_getAliasInfo($templateValue);
            $ret['table'] = $aliasInfo['table'];
            $ret['column'] = $aliasInfo['column'];
        }

        return $ret;
    }

    /*
    private function parseQuery($query) {

        $query_return = [];
        $table = "";

        $inx = stripos($query, " from ");
        if ($inx !== false) {
            $inx+= 6;
            $inx2 = stripos($query, "where");
            $tot= strlen($query);

            if ($inx2 !== false)
                $tot = $inx2;
            $inx3 = stripos($query, "inner");
            if ($inx3 !== false && $inx3 < $inx2)
                $tot = $inx3;

            $table = substr($query, $inx, $tot -$inx-1);
        }

        $query_return['table'] = $table;

        return $query_return;
    }
*/












/*

    private function regex_get_query_parts( $query ) {
        $matches = [];
        $regex = '@SELECT (?<select>.*) FROM (?<from>.*)@i';
        preg_match($regex, $query, $matches);
        return $matches;
    }

    private function regex_get_query_alias_list( $query ) {

        $query_parts = $this->regex_get_query_parts($query);
        if ( empty($query_parts['select']) ) return [];

        $matches = [];
        $regex = '@\\s*(?P<original>[a-zA-Z0-9_.]+) AS (?P<alias>[a-zA-Z0-9_]+),?\\s?@i';

        preg_match_all($regex, $query_parts['select'], $matches, PREG_SET_ORDER);

        return $matches;
    }

    private function regex_get_name_from_alias( $query, $alias ) {

        $alias_list = $this->regex_get_query_alias_list($query);

        foreach ( $alias_list as $a ) {
            if ( $a['alias'] == $alias ) return $a['original'];
        }

        return $alias;
    }
*/



}