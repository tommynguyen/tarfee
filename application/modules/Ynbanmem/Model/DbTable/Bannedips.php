<?php

class Ynbanmem_Model_DbTable_BannedIps extends Engine_Db_Table {

    //  protected $_owner_type = 'user';
//    public function membership() {
//        return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('membership', 'ynevent'));
//    }

    protected $_name = 'core_bannedips';

    public function isAddressBanned($address, $spec = null) {
        $addressObject = new Engine_IP($address);
        $addressBinary = $addressObject->toBinary();

        // Load banned IPs
        if (null === $spec) {
            $bannedIps = $this->select()
                    ->from($this)
                    ->query()
                    ->fetchAll();
        } else {
            $bannedIps = $spec;
        }

        $isBanned = false;
        foreach ($bannedIps as $bannedIp) {
            // @todo ipv4->ipv6 transformations
            if (strlen($addressBinary) == strlen($bannedIp['start'])) {
                if (strcmp($addressBinary, $bannedIp['start']) >= 0 &&
                        strcmp($addressBinary, $bannedIp['stop']) <= 0) {
                    $isBanned = true;
                    break;
                }
            }
        }

        return (bool) $isBanned;
    }

    public function removeAddress($address) {
        $addressObject = new Engine_IP($address);
        if (!$addressObject->isValid()) {
            throw new Engine_Exception('Invalid IP address');
        }

        $addressBinary = $addressObject->toBinary();

        // Delete
        $this->delete(array(
            'start' => $addressBinary,
            'stop' => $addressBinary,
        ));

        return $this;
    }

    public function removeAddressRange($startAddress, $stopAddress) {
        $startAddressObject = new Engine_IP($startAddress);
        $stopAddressObject = new Engine_IP($stopAddress);

        if (!$startAddressObject->isValid()) {
            throw new Engine_Exception('Invalid start IP address');
        }
        if (!$stopAddressObject->isValid()) {
            throw new Engine_Exception('Invalid stop IP address');
        }

        $startAddressBinary = $startAddressObject->toBinary();
        $stopAddressBinary = $stopAddressObject->toBinary();

        // Delete
        $this->delete(array(
            'start' => $startAddressBinary,
            'stop' => $stopAddressBinary,
        ));
        $removedIds = $this->select()
                ->from($this, 'bannedip_id')
                ->where(delete(array(
                            'start' => $startAddressBinary,
                            'stop' => $stopAddressBinary,
                        )))
                ->query()
                ->fetchAll(Zend_Db::FETCH_COLUMN);
        if (count($removedIds) != 0) {
            $extraTable = Engine_Api::_()->getDBTable('extrainfo', 'ynbanmem');
            $extraTable->delete(array(
                'banned_id IN(?)' => $removedIds,
                'banned_type = 1'
            ));
        }

        return $this;
    }

    public function normalizeAddressArray($addresses) {
        $data = array();
        foreach ($addresses as $address) {
            if (is_string($address)) {
                $start = Engine_IP::normalizeAddressToBinary($address);
                $stop = Engine_IP::normalizeAddressToBinary($address);
            } else if (is_array($address)) {
                $start = Engine_IP::normalizeAddressToBinary($address[0]);
                $stop = Engine_IP::normalizeAddressToBinary($address[1]);
            } else {
                continue;
            }
            $data[bin2hex($start) . '-' . bin2hex($stop)] = array(
                'start' => $start,
                'stop' => $stop
            );
        }
        return $data;
    }

    public function setAddresses($addresses, $info) {

        $extraInfoTable = Engine_Api::_()->getDbTable('extrainfo', 'ynbanmem');
        // Build assoc for existing addresses
        $data = $this->select()
                ->from($this)
                ->query()
                ->fetchAll();

        $currentAddresses = array();
        foreach ($data as $datum) {
            $currentAddresses[bin2hex($datum['start']) . '-' . bin2hex($datum['stop'])] = $datum['bannedip_id'];
        }

        // Build assoc array for new addresses
        $newAddresses = $this->normalizeAddressArray($addresses);

        // Get added addresses and removed addresses
        $addedAddresses = array_diff_key($newAddresses, $currentAddresses);
        $removedAddresses = array_diff_key($currentAddresses, $newAddresses);

        // Do added addresses
        foreach ($addedAddresses as $addedAddress) {
            if (empty($addedAddress['start']) && empty($addedAddress['stop'])) {
                continue;
            }
            $banned_id = $this->insert(array(
                'start' => $addedAddress['start'],
                'stop' => $addedAddress['stop'],
                    ));

            $extraInfoTable->addExtraInfo($banned_id, $info);
        }

        return $this;
    }

    public function getAddresses() {
        $extraInfoTable = Engine_Api::_()->getDbTable('extrainfo', 'ynbanmem');
        $data = $this->select()
                ->from($this, array('banned_id' => 'bannedip_id', 'start', 'stop'))
                ->order('start ASC')
                ->query()
                ->fetchAll();

        $addresses = array();
        foreach ($data as $datum) {
            if ($datum['start'] == $datum['stop']) {
                $startStr = Engine_IP::normalizeAddress($datum['start']);
                $extraInfo = $extraInfoTable->getExtraInfo($datum['banned_id'], 1);
                 //if(count($extraInfo) != 0)
                {
                    $bannedip['banned_id'] = $datum['banned_id'];
                    $bannedip['start'] = $startStr;
                    $bannedip['stop'] = "";
                    $bannedip['extra_info'] = $extraInfo;
                    //$addresses[] = $startStr . ' - ' . $stopStr;
                    if ($startStr) {
                        $addresses[] = $bannedip;
                    }
                }
            } else {
                $startStr = Engine_IP::normalizeAddress($datum['start']);
                $stopStr = Engine_IP::normalizeAddress($datum['stop']);

                //Get extra info
                $extraInfo = $extraInfoTable->getExtraInfo($datum['banned_id'], 1);
                 //if(count($extraInfo) != 0)
                {
                    $bannedip['banned_id'] = $datum['banned_id'];
                    $bannedip['start'] = $startStr;
                    $bannedip['stop'] = $stopStr;
                    $bannedip['extra_info'] = $extraInfo;
                    //$addresses[] = $startStr . ' - ' . $stopStr;
                    if ($startStr && $stopStr) {
                        $addresses[] = $bannedip;
                    }
                }
            }
        }
        
        return array_filter($addresses);
    }

    public function unBanIps($ids) {

        if (count($ids) != 0) {
            $this->delete(array(
                'bannedip_id IN(?)' => $ids,
            ));

            $extraTable = Engine_Api::_()->getDBTable('extrainfo', 'ynbanmem');
            $extraTable->delete(array(
                'banned_id IN(?)' => $ids,
                'banned_type = 0'
            ));
        }

        return $this;
    }
public function unBanIp($id) {
        $bannedIpsTable = Engine_Api::_()->getDbtable('bannedips', 'ynbanmem');
        $extraInfoTable = Engine_Api::_()->getDbtable('extrainfo', 'ynbanmem');
        if (count($ids) != 0) {
             $exists = $extraInfoTable->select()
                                ->where('banned_id = ?', $id)
                                ->where('banned_type = ?', 1)
                                ->query()
                                ->fetch();

                        if (count($exists) != 0) {
                            $extraInfoTable->delete(array(
                                'banned_id = ?' => $id,
                                'banned_type = ?' => 1
                            ));
                        }

                        $bannedIpsTable->delete(array('bannedip_id = ?' => $id));
        }

        return $this;
    }

}

