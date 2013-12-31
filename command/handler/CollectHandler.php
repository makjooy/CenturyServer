<?php
/**
 * @author WanBin @date 2012-12-26
 * 用户建筑类，此类进行操作细化
 * 单记录与多记录同时存在在本类中，需要根据实际情况进行修改
 * 都写为受保护的方法，实际使用时要手动修改
 */
require_once PATH_CACHE . 'CollectCache.php';
class CollectHandler extends CollectCache{
	
	/**
	 * 添加一个新闻公告
	 */
	public function newCollect($id,$type){
		$content=array(
				'gameuid'=>$this->gameuid,
				'time'=>time(),
				'type'=>$type,
				'publish_id'=>$id,
				);
		$this->add($content);
	}
	
	
	public function getPage($page){
		return parent::getPage($page);
	}
}