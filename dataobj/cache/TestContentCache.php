<?php
/**
 * @author WanBin @date 2012-12-07
 * 测试数据库连接
 * 单记录与多记录同时存在在本类中，需要根据实际情况进行修改
 * 都写为受保护的方法，实际使用时要手动修改
 */
require_once PATH_DATAOBJ . 'TestContentModel.php';
class TestContentCache extends TestContentModel{
	private $item = array ();
	
	/**
	 * 得到所有记录
	 */
	protected function get() {
		if (empty ( $this->item )) {
			$key = $this->getCacheKeyAll ();
			$ret = $this->getFromCache ( $key, $this->gameuid );
			if (empty ( $ret )) {
				$ret = parent::get ();
				if (! empty ( $ret )) {
					$this->setToCache ( $key, $ret, 0, $this->gameuid );
				}
			}
			$this->item = $ret;
		}
		return $this->item;
	}
	
	/**
	 * 得到一条记录
	 *
	 * @param $id unknown_type       	
	 * @return Ambigous <boolean, multitype:, multitype:multitype: >
	 */
	protected function getOne($count) {
		$key = $this->getCacheKey ($count);
		$ret = $this->getFromCache ( $key, $this->gameuid );
		if (empty ( $ret )) {
			$ret = parent::getOne ();
			if (! empty ( $ret )) {
				$this->setToCache ( $key, $ret, 0, $this->gameuid );
			}
		}
		return $ret;
	}
	
		/**
	 * 更新信息
	 *
	 * @param $content unknown_type       	
	 * @return Ambigous <boolean, number, multitype:>
	 */
	protected function update($content) {
		parent::update ( $content );
		return $this->delFromCache ();
	}
	
	protected function updateOne($count, $content) {
		parent::update ( $count, $content );
		return $this->delFromCacheALL ();
	}
	
	/**
	 * 添加一条信息
	 *
	 * @param $content unknown_type       	
	 * @return Ambigous <boolean, number, multitype:>
	 */
	protected function add($content) {
		parent::add ( $content );
		return $this->setToCache ( $this->getCacheKey (), $content, 0, $this->gameuid );
	}
	
	protected function addOne($count, $content) {
		$this->get ();
		$content ['count'] = $count;
		parent::add ( $content );
		$this->item [$count] = $content;
		$key = $this->getCacheKeyAll ();
		return $this->setToCache ( $key, $this->item, 0, $this->gameuid );
	}
	
	public function addarr($content) {
		$this->get ();
		parent::addarr ( $content );
		foreach ( $content as $key => $vlaue ) {
			$this->item [$vlaue ['count']] = $vlaue;
		}
		$key = $this->getCacheKeyAll ();
		return $this->setToCache ( $key, $this->item, 0, $this->gameuid );
	}
	/**
	 * 删除一条信息
	 *
	 * @param $id unknown_type       	
	 * @return number
	 */
	protected function del() {
		parent::del ();
		return $this->delFromCache ();
	}
	
	protected function delOne($count) {
		$this->get ();
		parent::delOne ( $count );
		if (true) {
			unset ( $this->item [$count] );
		} else {
			foreach ( $this->item as $key => $value ) {
				if ($value ['count'] == $count) {
					unset ( $this->item [$key] );
					break;
				}
			}
		}
		$key = $this->getCacheKeyAll ();
		return $this->setToCache ( $key, $this->item, 0, $this->gameuid );
	}
	
	/**
	 +----------------------------------------------------------
	 * 
	 +----------------------------------------------------------
	 * @param unknown_type $count
	 * @return boolean
	 +----------------------------------------------------------
	 */
	protected function delFromCache($count) {
		$key =  $this->getCacheKey ($count);
		return $this->delToCache ($key,$this->gameuid);
	}
	
	protected function delFromCacheALL() {
		return $this->delToCache ( $this->getCacheKeyAll (), $this->gameuid );
	}
	
	private function getCacheKey($count) {
		return sprintf ( MEMCACHE_KEY_TESTCONTENT,$this->gameuid ,$count );
	}
	
	private function getCacheKeyAll() {
		return sprintf ( MEMCACHE_KEY_TESTCONTENT_ALL, $this->gameuid );
	}
}