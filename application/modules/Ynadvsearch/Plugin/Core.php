<?php
class Ynadvsearch_Plugin_Core {
	public function onItemDeleteAfter($event) {
		$payload = $event->getPayload();
		$allowSearhType = array_keys(Engine_Api::_()->ynadvsearch()->getAllowSearchTypes());
		if (in_array($payload['type'], $allowSearhType)) {
			Engine_Api::_()->getDbTable('sportmaps', 'ynadvsearch')->removeItem($payload['type'], $payload['identity']);
		}
	}
	
	public function onItemUpdateAfter($event) {
		$payload = $event -> getPayload();
		$allowSearhType = array_keys(Engine_Api::_()->ynadvsearch()->getAllowSearchTypes());
		if (in_array($payload->getType(), $allowSearhType) && method_exists($payload, 'getSportId')) {
			Engine_Api::_()->getDbTable('sportmaps', 'ynadvsearch')->updateItem($payload);
		}
	}
	
	public function onItemCreateAfter($event) {
		$payload = $event -> getPayload();
		$allowSearhType = array_keys(Engine_Api::_()->ynadvsearch()->getAllowSearchTypes());
		if (in_array($payload->getType(), $allowSearhType) && method_exists($payload, 'getSportId')) {
			Engine_Api::_()->getDbTable('sportmaps', 'ynadvsearch')->updateItem($payload);
		}
	}	
}
