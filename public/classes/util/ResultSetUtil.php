<?php
/**
 * Created by PhpStorm.
 * User: Ming
 * Date: 8/27/2017
 * Time: 8:00 PM
 */

class ResultSetUtil
{
    public static function resultSet_toArray($result){
        $rows = array();
        while($row = $result->fetch_assoc()){
            $rows[] = $row;
        }
        return $rows;
    }

    public static function resultSet_toMap($result){
        $map = array();
        $key_field = '';
        while($field = $result->fetch_field()) { // search for key field
            $fieldName = $field->name;

            if(strpos($fieldName, 'key_') !== false){
                $key_field = $fieldName; break;
            }
        }

        while($row = $result->fetch_assoc()){
            $arrObj = $row;
            unset($arrObj[$key_field]); // remove key field from item

            $map[$row[$key_field]][] = $arrObj; // push item to its corresponding group
        }
        return $map;
    }
}