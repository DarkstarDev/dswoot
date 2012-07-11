<?php
/**
 * Item Controller
 *
 * @author Orlando Marin
 */
 
class ItemController
extends Zend_Controller_Action
{

    public function init()
    {
        $this->view->site = ($this->getRequest()->getParam('site')  ? $this->getRequest()->getParam('site') : 'woot');
    }

    public function indexAction()
    {
        $this->_forward('view');
    }

    public function viewAction()
    {
        $this->view->currentProduct = array();
        $wootFetch = Ds_Service_WootFeed_Factory::create();
        $request = $this->getRequest();
        $site = $request->getParam('site');
        if ($request->getParam('id') == 'current') {
            $productData = $wootFetch->getCurrentProduct($site);
        } else {
            $data = $wootFetch->getProductByIdAndSite($request->getParam('id'), $site);
            $productData = $data['requested'];
            $this->view->currentProduct = $data['current'];
        }

        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->json($productData);
        }

        $this->view->productData = $productData;
        $this->view->socketServer = Zend_Registry::get('config')->socketServer->url;
        $this->view->autoRefresh = false;
        $this->view->changeProduct = false;
    }

    public function listAction()
    {
        $this->_helper->layout->setLayout('list-layout');
        $wootFetch = Ds_Service_WootFeed_Factory::create();
        $request = $this->getRequest();
        $site = $request->getParam('site');
        $productData = $wootFetch->getProductListBySite($site, $request->getParam('page'));

        if (!count($productData['items']) && $request->getParam('page') != 1) {
            header('Location: /' . $site . '/list/');
            die();
        }

        $this->view->productData = $productData['items'];
        $this->view->itemCount = $productData['count'];
        $this->view->range = $productData['range'];
        $this->view->page = min(ceil($productData['count']/$productData['range'][1]),$request->getParam('page'));
    }
}
