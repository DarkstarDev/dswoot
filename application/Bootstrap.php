<?php
/**
 * DSWoot Bootstrap
 *
 * @author Orlando Marin
 */
 
class Bootstrap
extends Zend_Application_Bootstrap_Bootstrap
{

    protected function _initDoctype()
    {
        $this->bootstrap('view');
        $view = $this->getResource('view');
        $view->doctype('XHTML1_STRICT');
    }

    protected function _initConfig()
    {
        $config = new Zend_Config($this->getOptions(), true);
        Zend_Registry::set('config', $config);
        return $config;
    }

    protected function _initDb()
    {
        $resource = $this->getPluginResource('db');
        Zend_Registry::set('db', $resource->getDbAdapter());
    }

    protected function _initAutoLoad()
    {
        require_once 'Zend/Loader/Autoloader.php';
        Zend_Loader_Autoloader::getInstance();
    }

    protected function _initRoutes()
    {
        $route = new Zend_Controller_Router_Route(
                "/",
                array(
                    "controller" => "item",
                    "action" => "view",
                    "id" => "current",
                    "site" => "woot"
                )
        );
        Zend_Controller_Front::getInstance()->getRouter()->addRoute('index', $route);

        $route = new Zend_Controller_Router_Route(
                "/woot",
                array(
                    "controller" => "item",
                    "action" => "view",
                    "id" => "current",
                    "site" => "woot"
                )
        );
        Zend_Controller_Front::getInstance()->getRouter()->addRoute('wootIndex', $route);

        $route = new Zend_Controller_Router_Route(
                "/sellout",
                array(
                    "controller" => "item",
                    "action" => "view",
                    "id" => "current",
                    "site" => "sellout"
                )
        );
        Zend_Controller_Front::getInstance()->getRouter()->addRoute('selloutIndex', $route);

        $route = new Zend_Controller_Router_Route(
                "/shirt",
                array(
                    "controller" => "item",
                    "action" => "view",
                    "id" => "current",
                    "site" => "shirt"
                )
        );
        Zend_Controller_Front::getInstance()->getRouter()->addRoute('shirtIndex', $route);

        $route = new Zend_Controller_Router_Route(
                "/kids",
                array(
                    "controller" => "item",
                    "action" => "view",
                    "id" => "current",
                    "site" => "kids"
                )
        );
        Zend_Controller_Front::getInstance()->getRouter()->addRoute('kidsIndex', $route);

        $route = new Zend_Controller_Router_Route(
                "/wine",
                array(
                    "controller" => "item",
                    "action" => "view",
                    "id" => "current",
                    "site" => "wine"
                )
        );
        Zend_Controller_Front::getInstance()->getRouter()->addRoute('wineIndex', $route);

        $route = new Zend_Controller_Router_Route(
                "/moofi",
                array(
                    "controller" => "item",
                    "action" => "view",
                    "id" => "current",
                    "site" => "moofi"
                )
        );
        Zend_Controller_Front::getInstance()->getRouter()->addRoute('moofiIndex', $route);

    }
}