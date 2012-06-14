<?php
/**
 * WootFeed factory
 *
 * @author Orlando Marin
 */
 
class Ds_Service_WootFeed_Factory {
    public static function create()
    {
        $db = Zend_Registry::get('db');

        $dao = new Ds_Service_WootFeed_DbDao($db);

        return new Ds_Service_WootFeed($dao, $db);
    }
}
