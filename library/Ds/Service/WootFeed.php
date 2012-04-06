<?php
/**
 * This class fetches, parses, and saves Woot! RSS data
 *
 * @author Orlando Marin
 */
 
class Ds_Service_WootFeed {
    /**
     * @var Ds_Service_WootFeed_DbDao
     */
    protected $_dao;

    /**
     * @var \Zend_Db_Adapter_Abstract
     */
    protected $_db;

    const WOOT = 'www';
    const WINE = 'wine';
    const SHIRT = 'shirt';
    const KIDS = 'kids';
    const MOOFI = 'moofi';
    const SELLOUT = 'sellout';
    const HOME = 'home';

    protected $_allowedSites = array(
        self::WOOT => 'woot',
        self::WINE => 'wine',
        self::SHIRT => 'shirt',
        self::KIDS => 'kids',
        self::MOOFI => 'moofi',
        self::SELLOUT => 'sellout',
        self::HOME => 'home'
    );

    public function __construct($dao, Zend_Db_Adapter_Abstract $db)
    {
        $this->_dao = $dao;
        $this->_db = $db;
    }

    public function getCurrentProduct($site)
    {
        if (!$wootSite = array_search(strtolower($site), $this->_allowedSites)) {
            throw new Exception('Unrecognized woot site', 404);
        }

        $timeDifference = $now = time();

        if ($item = $this->_dao->fetchLatestItem($site)) {
            $lastUpdate = new DateTime($item['history'][0]['updated']);
            $timeDifference = $now - $lastUpdate->format('U');
        }

        if (
            ($item['wootoff'] && $timeDifference <= 30)
            || (!$item['wootoff'] && $timeDifference <= 90)
        ) {
            return $item;
        }

        $data = $item;

        if (!$this->_dao->getLockStatus($site)) {
            $this->_dao->lockApplicationUpdates($site);
            try {
                $this->_db->beginTransaction();
                $feed = $this->_fetchWootRss($wootSite);
                $feed['item'] = array_merge(
                    $feed['item'],
                    array('file_extension' => $feed['images']['file_extension']),
                    array('site' => $this->_allowedSites[$wootSite])
                );
                if (!$item || $item['id'] != $feed['item']['id']) {
                    $this->_dao->insertItem(
                        $feed['item']
                    );
                    $this->_dao->insertProducts($feed['item']['id'], $feed['products']);
                }
                $this->_dao->insertItemHistory($feed['item']['id'], $feed['history']);
                $this->_dao->unlockApplicationUpdates($site);
                $this->_db->commit();
                $data = $feed['item'];
                $data['history'][0] = $feed['history'];
                $data['products'] = $feed['products'];
            } catch (Exception $e) {
                //Unable to fetch data from woot or insert data.
                $this->_db->rollBack();
                $this->_dao->unlockApplicationUpdates($site);
                throw new Exception($e->getMessage(), 500, $e);
            }
        }
        return $data;
    }

    public function getProductByIdAndSite($id, $site)
    {
        if (!array_search(strtolower($site), $this->_allowedSites)) {
            throw new Exception('Unrecognized woot site', 404);
        }

        //Refresh the current site's stats since we're already here
        $current = $this->getCurrentProduct($site);
        $requested = $this->_dao->fetchItemByIdAndSite($id, $site);

        return array('current' => $current, 'requested' => $requested);
    }

    public function getProductListBySite($site, $page)
    {
        if (!array_search(strtolower($site), $this->_allowedSites)) {
            throw new Exception('Unrecognized woot site', 404);
        }

        return $this->_dao->fetchItemListBySite($site, $page);
    }

    protected function _fetchWootRss($site)
    {
        $ch = curl_init('http://api.woot.com/1/sales/current.rss/'.$site.'.woot.com');
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $xml = curl_exec($ch);

        if (curl_errno($ch)) {
            $error = curl_error($ch);
            throw new Exception($error);
        }
        curl_close($ch);

        $data = $this->_parseWootRss($xml);
        $data['item']['site'] = $site;
        $this->_fetchProductImages($data['item']['id'], $data['images']);

        return $data;
    }

    protected function _fetchProductImages($itemId, array &$data)
    {
        preg_match('/(\.[a-zA-Z]{3})$/', $data['standard'], $matches);
        $fileExtension = $matches[1];

        if (
            file_exists(APPLICATION_PATH . '/../public/images/products/' . $itemId . $fileExtension)
            && file_exists(APPLICATION_PATH . '/../public/images/products/' . $itemId . '_detail' . $fileExtension)
            && file_exists(APPLICATION_PATH . '/../public/images/products/' . $itemId . '_thumbnail' . $fileExtension)
        ) {
            //Skip downloading images if they already exist
            $data['file_extension'] = $fileExtension;
            return;
        }

        $ch = curl_init($data['standard']);
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $standard = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new Exception(curl_error($ch));
        }
        curl_close($ch);

        $ch = curl_init($data['detail']);
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $detail = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new Exception(curl_error($ch));
        }
        curl_close($ch);

        $ch = curl_init($data['thumbnail']);
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $thumbnail = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new Exception(curl_error($ch));
        }
        curl_close($ch);

        file_put_contents(APPLICATION_PATH . '/../public/images/products/' . $itemId . $fileExtension, $standard);
        file_put_contents(APPLICATION_PATH . '/../public/images/products/' . $itemId . '_detail' . $fileExtension, $detail);
        file_put_contents(APPLICATION_PATH . '/../public/images/products/' . $itemId . '_thumbnail' . $fileExtension, $thumbnail);

        $data['file_extension'] = $fileExtension;
    }

    protected function _parseWootRss($xml)
    {
        $item = $history = $images = array();
        $xml = new SimpleXMLElement($xml);
        $wootNamespace = $xml->channel->item->children('woot', true);

        $item['id'] = sha1($xml->channel->item->guid);
        $item['link'] = (string)$xml->channel->item->link;
        $item['condition'] = (string)$wootNamespace->condition;
        $item['thread'] = (string)$wootNamespace->discussionurl;
        $item['purchase_url'] = (string)$wootNamespace->purchaseurl;
        $item['price'] = number_format((float)str_replace('$','',$wootNamespace->price),2);

        preg_match('/\$([\d\.]*)/',$wootNamespace->shipping, $matches);

        $item['shipping'] = (float)$matches[1];
        $item['wootoff'] = (strtolower((string)$wootNamespace->wootoff) == 'true') ? true : false;
        $item['title'] = (string)$xml->channel->item->title;
        $item['subtitle'] = (string)$wootNamespace->subtitle;
        $item['teaser'] = (string)$wootNamespace->teaser;
        $history['comments'] = (int)$wootNamespace->comments;
        $history['sold_out'] = (strtolower((string)$wootNamespace->soldout) == 'true') ? true : false;
        $history['percent_sold'] = (float)$wootNamespace->soldoutpercentage;
        $history['updated'] = date('c');
        $images['standard'] = (string)$wootNamespace->standardimage;
        $images['detail'] = (string)$wootNamespace->detailimage;
        $images['thumbnail'] = (string)$wootNamespace->thumbnailimage;

        //Woot tends to use UTF-8 characters in their filenames.  cURL doesn't like this.
        $standardUrl = parse_url($images['standard']);
        $standardUrl['path'] = substr($standardUrl['path'], 1);
        $images['standard'] = str_replace($standardUrl['path'], urlencode($standardUrl['path']), $images['standard']);

        $detailUrl = parse_url($images['detail']);
        $detailUrl['path'] = substr($detailUrl['path'], 1);
        $images['detail'] = str_replace($detailUrl['path'], urlencode($detailUrl['path']), $images['detail']);
        
        $thumbnailUrl = parse_url($images['thumbnail']);
        $thumbnailUrl['path'] = substr($thumbnailUrl['path'], 1);
        $images['thumbnail'] = str_replace($thumbnailUrl['path'], urlencode($thumbnailUrl['path']), $images['thumbnail']);

        $products = array();

        foreach($wootNamespace->products->product as $product) {
            //The following line shouldn't be necessary but getting the quantity doesn't seem to work without it.
            $attributes = $product->attributes();
            $products[] = array(
                'name' => (string)$product,
                'quantity' => (int)$attributes['quantity']
            );
        }

        return array(
            'item' => $item,
            'history' => $history,
            'products' => $products,
            'images' => $images
        );
    }
}
