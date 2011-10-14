<?php
/**
 * Class description
 *
 * @author Orlando Marin
 */
 
class ItemController
extends Zend_Controller_Action
{

    public function indexAction()
    {
        $this->_forward('view');
    }

    public function viewAction()
    {
        $wootFetch = Ds_Service_WootFeed_Factory::create();
        $request = $this->getRequest();
        $site = strtoupper($request->getParam('site'));
        if ($request->getParam('id') == 'current') {
            $productData = $wootFetch->getCurrentProduct($site);
        } else {
            $productData = $wootFetch->getProductByIdAndSite($request->getParam('id'), $site);
        }

        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->json($productData);
        }

        $this->view->productData = $productData;
    }

    public function listAction()
    {
        $wootFetch = Ds_Service_WootFeed_Factory::create();
        $request = $this->getRequest();
        $site = strtoupper($request->getParam('site'));
        $productData = $wootFetch->getProductListBySite($site, $request->getParam('page'));

        $this->view->productData = $productData;
    }
}
