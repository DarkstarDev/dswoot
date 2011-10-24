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
                "/woot/view/:id",
                array(
                    "controller" => "item",
                    "action" => "view",
                    "id" => "current",
                    "site" => "woot"
                ),
                array(
                    'id' => '[0-9a-f]+'
                )
        );
        Zend_Controller_Front::getInstance()->getRouter()->addRoute('wootView', $route);

        $route = new Zend_Controller_Router_Route(
                "/woot/list/:page",
                array(
                    "controller" => "item",
                    "action" => "list",
                    "site" => "woot",
                    "page" => 1
                )
        );
        Zend_Controller_Front::getInstance()->getRouter()->addRoute('wootList', $route);

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
                "/sellout/view/:id",
                array(
                    "controller" => "item",
                    "action" => "view",
                    "id" => "current",
                    "site" => "sellout"
                ),
                array(
                    'id' => '[0-9a-f]+'
                )
        );
        Zend_Controller_Front::getInstance()->getRouter()->addRoute('selloutView', $route);

        $route = new Zend_Controller_Router_Route(
                "/sellout/list/:page",
                array(
                    "controller" => "item",
                    "action" => "list",
                    "site" => "sellout",
                    "page" => 1
                )
        );
        Zend_Controller_Front::getInstance()->getRouter()->addRoute('selloutList', $route);

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
                "/shirt/view/:id",
                array(
                    "controller" => "item",
                    "action" => "view",
                    "id" => "current",
                    "site" => "shirt"
                ),
                array(
                    'id' => '[0-9a-f]+'
                )
        );
        Zend_Controller_Front::getInstance()->getRouter()->addRoute('shirtView', $route);

        $route = new Zend_Controller_Router_Route(
                "/shirt/list/:page",
                array(
                    "controller" => "item",
                    "action" => "list",
                    "site" => "shirt",
                    "page" => 1
                )
        );
        Zend_Controller_Front::getInstance()->getRouter()->addRoute('shirtList', $route);

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
                "/kids/view/:id",
                array(
                    "controller" => "item",
                    "action" => "view",
                    "id" => "current",
                    "site" => "kids"
                ),
                array(
                    'id' => '[0-9a-f]+'
                )
        );
        Zend_Controller_Front::getInstance()->getRouter()->addRoute('kidsView', $route);

        $route = new Zend_Controller_Router_Route(
                "/kids/list/:page",
                array(
                    "controller" => "item",
                    "action" => "list",
                    "site" => "kids",
                    "page" => 1
                )
        );
        Zend_Controller_Front::getInstance()->getRouter()->addRoute('kidsList', $route);

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
                "/wine/view/:id",
                array(
                    "controller" => "item",
                    "action" => "view",
                    "id" => "current",
                    "site" => "wine"
                ),
                array(
                    'id' => '[0-9a-f]+'
                )
        );
        Zend_Controller_Front::getInstance()->getRouter()->addRoute('wineView', $route);

        $route = new Zend_Controller_Router_Route(
                "/wine/list/:page",
                array(
                    "controller" => "item",
                    "action" => "list",
                    "site" => "wine",
                    "page" => 1
                )
        );
        Zend_Controller_Front::getInstance()->getRouter()->addRoute('wineList', $route);

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

        $route = new Zend_Controller_Router_Route(
                "/moofi/view/:id",
                array(
                    "controller" => "item",
                    "action" => "view",
                    "id" => "current",
                    "site" => "moofi"
                ),
                array(
                    'id' => '[0-9a-f]+'
                )
        );
        Zend_Controller_Front::getInstance()->getRouter()->addRoute('moofiView', $route);

        $route = new Zend_Controller_Router_Route(
                "/moofi/list/:page",
                array(
                    "controller" => "item",
                    "action" => "list",
                    "site" => "moofi",
                    "page" => 1
                )
        );
        Zend_Controller_Front::getInstance()->getRouter()->addRoute('moofiList', $route);

        $route = new Zend_Controller_Router_Route(
                "/home",
                array(
                    "controller" => "item",
                    "action" => "view",
                    "id" => "current",
                    "site" => "home"
                )
        );
        Zend_Controller_Front::getInstance()->getRouter()->addRoute('homeIndex', $route);

        $route = new Zend_Controller_Router_Route(
                "/home/view/:id",
                array(
                    "controller" => "item",
                    "action" => "view",
                    "id" => "current",
                    "site" => "home"
                ),
                array(
                    'id' => '[0-9a-f]+'
                )
        );
        Zend_Controller_Front::getInstance()->getRouter()->addRoute('homeView', $route);

        $route = new Zend_Controller_Router_Route(
                "/home/list/:page",
                array(
                    "controller" => "item",
                    "action" => "list",
                    "site" => "home",
                    "page" => 1
                )
        );
        Zend_Controller_Front::getInstance()->getRouter()->addRoute('homeList', $route);
    }
}