<?php
/**
 * Class description
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
                    ON h.item_id = i.id AND h.updated = (
                        SELECT
                            updated
                        FROM
                            history

                        JOIN item
                            ON history.item_id = item.id
                        WHERE item.site = i.site
                        ORDER BY updated DESC
                        LIMIT 0,1
                    )
                WHERE i.site = ?
                ORDER BY h.updated DESC';

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
                ORDER BY h.updated ASC
                LIMIT ?,?';

        $dto = null;

        $lowerLimit = $this->_getLimitRange($page, $limit);
        if ($records = $this->_db->fetchAll($sql, array($site, $lowerLimit, $limit))) {
            $dtos = $this->_recordReduce($records);
        }

        return $dtos;
    }

    protected function _getLimitRange($page, $itemsPerPage)
    {
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
                    'price' => round($record['price'],2),
                    'shipping' => round($record['shipping'],2),
                    'wootoff' => (bool)$record['wootoff'],
                    'title' => $record['title'],
                    'subtitle' => $record['subtitle'],
                    'teaser' => $record['teaser'],
                    'file_extension' => $record['file_extension'],
                    'history' => array(),
                    'products' => array()
                );
                $idMap[] = $record['id'];
            }

            if (!in_array($record['history_id'], $historyMap)) {
                array_push($dto[$record['id']]['history'],array(
                    'comments' => $record['comments'],
                    'sold_out' => (bool)$record['sold_out'],
                    'percent_sold' => round($record['percent_sold'],2),
                    'updated' => $record['updated']
                ));
                $historyMap[] = $record['history_id'];
            }

            if (!in_array($record['product_id'], $productMap)) {
                array_push($dto[$record['id']]['products'],array(
                    'name' => $record['name'],
                    'quantity' => $record['quantity']
                ));
                $productMap[] = $record['product_id'];
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
        $row['id'] = sha1(uniqid());
        $this->_db->insert('history', array_merge(array('item_id' => $itemId), $row));
    }

    public function getLockStatus()
    {
        $sql = 'SELECT `locked` FROM `status`';
        $lock = $this->_db->fetchOne($sql);
        return $lock;
    }

    public function lockApplicationUpdates()
    {
        $this->_db->update('status', array('locked' => 1));
    }

    public function unlockApplicationUpdates()
    {
        $this->_db->update('status', array('locked' => 0));
    }
}
