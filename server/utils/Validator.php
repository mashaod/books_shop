<?php

Class Validator
{
    static function checkPassword($pass)
    {
        return strlen($pass)>5? true : false;
    }

    static function checkLogin($login)
    {
       return preg_match("/^[a-zA-Z0-9]+$/",$login) && strlen($login)>3? true : false;
    }

    static function checkId($id)
    {
       return $id != NULL &&!empty($id) && preg_match("/^[0-9999]+$/", $id)? true : false;
    }

    static function checkParams($data, $array)
    {     
        foreach ($array as $key)
        {
            if(!$data[$key])
                return false;
        }    
        return true;
    }

    static function clearData($data)
    {
        if(is_string($data))
        {
            $string = strip_tags($data);
            $clearString = str_replace('%20', ' ', $string);
            $result = trim($clearString);
        }
        else
        {
            if(!$data || $data === null || $data === false) return false;

            foreach($data as $key => $value)
            {
                (empty($value) || $value === false || $value == '-' || $value === null)?'':$result[$key] = self::clearData($value);
            }
        }

        return $result;
    }
}
