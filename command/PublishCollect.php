<?php
// 收藏，喜欢一个真心话
include_once 'BaseCommand.php';
include_once 'handler/CollectHandler.php';
include_once PATH_HANDLER . 'PunishHandler.php';
class PublishCollect extends BaseCommand {
	protected function executeEx($params) {
		$id = $params ['id'];
		$type = $params ['type'];
		if (empty ( $id ) || empty ( $type )) {
			$this->throwException ( 'id or type is empty', 1101 );
		}
		$cillect = new CollectHandler ( $this->uid );
		$publish = new PunishHandler ( $this->uid );
		if ($type == 1) {
			$count = $cillect->like ( $id );
			$publish->like ( $id, $count );
		} else if ($type == 2) {
			$count = $cillect->dislike ( $id );
			$publish->dislike ( $id, $count );
		}
		$this->reutrnDate ( COMMAND_ENPTY );
	}
}