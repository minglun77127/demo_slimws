<?php
require 'classes/service/Database.php';

$classID = 'VivianC_140731_0930';
$result = Database::getInstance()
    ->query("SELECT cs.csID AS key_csID, s.id AS e_id, s.firstName AS e_firstName, s.lastName AS e_lastName
                         FROM `tblcourseschedule` cs 
                         LEFT JOIN `tblstudcourse` sc ON cs.csID = sc.csID
                         LEFT JOIN `tblstudent` s ON s.id = sc.studID
                      WHERE classID = \"$classID\"");

var_dump(toMap($result));

function resultSet_toArray($result){
    $rows = array();
    while($row = $result->fetch_assoc()){
        $rows[] = $row;
    }
    return $rows;
}

/*function _group_by($array, $key) {
    $return = array();
    foreach($array as $val) {
        $return[$val[$key]][] = $val;
    }
    return $return;
}*/

function toMap($result){
    $array = array();
    foreach($result as $key => $item){
        $array[$item['key_csID']][$key] = $item;
    }

    return $array;
}

function resultSet_toMap($result){
    $map = array();
    $key_field = '';
    $e_fields  = array();

    for($i = 0; $i < $result->num_fields($result); $i++) {
        $field = $result->fetch_field($result, $i);

        if(strpos($field, 'key_') !== false){
            $key_field = $field;
        }else{
            array_push($e_fields, $field);
        }
    }

    while($row = $result->fetch_assoc()){
        //$array = array(explode('_', $key_field)[1] => $row[$key_field])
        echo var_dump($row);
    }
}