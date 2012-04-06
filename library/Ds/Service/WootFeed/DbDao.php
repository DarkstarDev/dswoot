<?php
/**
 * WootFeed Data Access Object
 *
 * @author Orlando Marin
 */
 
class Ds_Service_WootFeed_DbDao {
    protected $_db;

    public function __construct(Zend_Db_Adapter_Abstract $db)
    {
        $this->_db = $db;
    }

    public function fetchLatestItem($site)
    {
        $sql = 'SELECT
                    i.id,
                    i.link,
                    i.condition,
                    i.thread,
                    i.purchase_url,
                    i.price,
                    i.shipping,
                    i.wootoff,
                    i.title,
                    i.subtitle,
                    i.teaser,
                    i.file_extension,
                    p.id AS product_id,
                    p.name,
                    p.quantity,
                    h.id AS history_id,
                    h.comments,
                    h.sold_out,
                    h.percent_sold,
                    h.updated
                FROM
                    item i
                INNER JOIN product p
                    ON p.item_id = i.id
                INNER JOIN history h
                    ON h.item_id = i.id
                WHERE i.site = ?
                ORDER BY h.updated DESC
                LIMIT 0,1';

        $dto = null;

        if ($records = $this->_db->fetchAll($sql, $site)) {
            $dtos = $this->_recordReduce($records);
            $dto = $dtos[$records[0]['id']];
        }

        return $dto;
    }

    public function fetchItemByIdAndSite($id, $site)
    {
        $sql = 'SELECT
                    i.id,
                    i.link,
                    i.condition,
                    i.thread,
                    i.purchase_url,
                    i.price,
                    i.shipping,
                    i.wootoff,
                    i.title,
                    i.subtitle,
                    i.teaser,
                    i.file_extension,
                    p.id AS product_id,
                    p.name,
                    p.quantity,
                    h.id AS history_id,
                    h.comments,
                    h.sold_out,
                    h.percent_sold,
                    h.updated
                FROM
                    item i
                INNER JOIN product p
                    ON p.item_id = i.id
                INNER JOIN history h
                    ON h.item_id = i.id
                WHERE
                    i.id = ?
                    AND i.site = ?
                ORDER BY h.updated ASC';

        $dto = null;

        if ($records = $this->_db->fetchAll($sql, array($id, $site))) {
            $dtos = $this->_recordReduce($records);
            $dto = $dtos[$records[0]['id']];
        }

        return $dto;
    }

    public function fetchItemListBySite($site, $page=1, $limit=10)
    {
        $sql = 'SELECT SQL_CALC_FOUND_ROWS
                    i.id,
                    i.link,
                    i.condition,
                    i.thread,
                    i.purchase_url,
                    i.price,
                    i.shipping,
                    i.wootoff,
                    i.title,
                    i.subtitle,
                    i.teaser,
                    i.file_extension,
                    max(h.comments) AS comments,
                    min(h.updated) AS updated
                FROM
                    item i
                INNER JOIN history h
                    ON h.item_id = i.id
                WHERE i.site = ?
                GROUP BY id
                ORDER BY h.updated DESC
                LIMIT ?,?';

        $dtos = array();

        $lowerLimit = $this->_getLimitRange($page, $limit);
        if ($records = $this->_db->fetchAll($sql, array($site, $lowerLimit, $limit))) {
            $dtos = $this->_listRecordReduce($records);
        }

        $sql = 'SELECT FOUND_ROWS() as count';
        $record = $this->_db->fetchRow($sql);

        return array('count' => $record['count'], 'range' => array($lowerLimit+1, $limit), 'items' => $dtos);
    }

    protected function _getLimitRange($page, $itemsPerPage)
    {
        $page = max($page, 1);
        return ($page-1) * $itemsPerPage;
    }

    protected function _recordReduce($records)
    {
        $dto = array();
        $idMap = array();
        $productMap = array();
        $historyMap = array();
        foreach ($records as $record) {
            if (!in_array($record['id'], $idMap)) {
                $dto[$record['id']] = array(
                    'id' => $record['id'],
                    'link' => $record['link'],
                    'condition' => $record['condition'],
                    'thread' => $record['thread'],
                    'purchase_url' => $record['purchase_url'],
                    'price' => number_format($record['price'],2),
                    'shipping' => round($record['shipping'],2),
                    'wootoff' => (bool)$record['wootoff'],
                    'title' => $record['title'],
                    'subtitle' => $record['subtitle'],
                    'teaser' => $record['teaser'],
                    'file_extension' => $record['file_extension']
                );
                $idMap[] = $record['id'];
            }

            if (array_key_exists('history_id', $record) && !in_array($record['history_id'], $historyMap)) {
                if (!array_key_exists('history', $dto[$record['id']])) {
                    $dto[$record['id']]['history'] = array();
                }
                array_push($dto[$record['id']]['history'],array(
                    'comments' => $record['comments'],
                    'sold_out' => (bool)$record['sold_out'],
                    'percent_sold' => round($record['percent_sold'],2),
                    'updated' => date('c', strtotime($record['updated']))
                ));
                $historyMap[] = $record['history_id'];
            }

            if (array_key_exists('product_id', $record) && !in_array($record['product_id'], $productMap)) {
                if (!array_key_exists('products', $dto[$record['id']])) {
                    $dto[$record['id']]['products'] = array();
                }
                array_push($dto[$record['id']]['products'],array(
                    'name' => $record['name'],
                    'quantity' => $record['quantity']
                ));
                $productMap[] = $record['product_id'];
            }
        }

        return $dto;
    }

    protected function _listRecordReduce($records)
    {
        $dto = array();
        $idMap = array();

        foreach ($records as $record) {
            if (!in_array($record['id'], $idMap)) {
                $dto[$record['id']] = array(
                    'id' => $record['id'],
                    'link' => $record['link'],
                    'condition' => $record['condition'],
                    'thread' => $record['thread'],
                    'purchase_url' => $record['purchase_url'],
                    'price' => number_format($record['price'],2),
                    'shipping' => round($record['shipping'],2),
                    'wootoff' => (bool)$record['wootoff'],
                    'title' => $record['title'],
                    'subtitle' => $record['subtitle'],
                    'teaser' => $record['teaser'],
                    'file_extension' => $record['file_extension'],
                    'comments' => $record['comments'],
                    'updated' => strtotime($record['updated'])
                );
                $idMap[] = $record['id'];
            }
        }

        return $dto;
    }

    public function insertItem(array $row)
    {
        $this->_db->insert('item', $row);
    }

    public function insertProducts($itemId, array $row)
    {
        $sql = 'INSERT INTO `product` (`id`, `item_id`, `name`, `quantity`) VALUES';
        $values = array();

        foreach($row as $value) {
            $values[] = '(' . $this->_db->quote(sha1(uniqid())) . ',' . $this->_db->quote($itemId) . ',' . $this->_db->quote($value['name']) . ',' . $value['quantity'] . ')';
        }

        $sql .= implode(', ', $values);

        $this->_db->query($sql);
    }

    public function insertItemHistory($itemId, array $row)
    {
        unset($row['updated']);
        $row['id'] = sha1(uniqid());
        $this->_db->insert('history', array_merge(array('item_id' => $itemId), $row));
    }

    public function getLockStatus($site)
    {
        $sql = 'SELECT `locked` FROM `status` WHERE site = ?';
        $lock = $this->_db->fetchOne($sql, array($site));
        return $lock;
    }

    public function lockApplicationUpdates($site)
    {
        $this->_db->update('status', array('locked' => 1), array('site = ?' => $site));
    }

    public function unlockApplicationUpdates($site)
    {
        $this->_db->update('status', array('locked' => 0), array('site = ?' => $site));
    }
}
