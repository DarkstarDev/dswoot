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
        $this->view->site = $this->getRequest()->getParam('site');
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
    }

    public function listAction()
    {
        $wootFetch = Ds_Service_WootFeed_Factory::create();
        $request = $this->getRequest();
        $site = $request->getParam('site');
        $productData = $wootFetch->getProductListBySite($site, $request->getParam('page'));

        $this->view->productData = $productData;
    }
}
