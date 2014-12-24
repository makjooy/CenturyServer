<?php
require_once FRAMEWORK . 'exception/ExceptionConstants.php';
require_once 'MemcacheConstants.php';

class DBModel {
	protected $useMemcache = null;
	/**
	 * @var Redis
	 */
	protected $redis = null;
	
	/**
	 * @var Memcache
	 */
	protected $memcache=null;
	
	//数据分析参数
	protected $selectCount = 0;
	protected $insertCount = 0;
	protected $updateCount = 0;
	protected $deleteCount = 0;
	
	protected $mysqlConnect=null;
	protected static $mongoClient=null;
	
	public function __construct() {
		$config = $GLOBALS ['config'];
		$this->useMemcache = $config ['memcache'];
		$this->model = get_class ( $this );
		
		$redis_config = ISBAIDU ? $config ['redis_base_baidu'] : $config ['redis_base'];
		$this->redis = new Redis ();
		$this->redis->connect ( $redis_config ['host'], $redis_config ['port'] );
		$this->redis->auth ( $redis_config ['password'] );
		
		$cacheConfig = $config ['memcache_base'];
		$this->memcache = new Memcache ();
		$this->memcache->pconnect ( $cacheConfig ['host'], $cacheConfig ['port'] );
	}
	
	
	
	//基本cache操作
	protected function setToCache($key,$value){
		return $this->memcache->set($key,$value,0);
	}
	
	protected function getFromCache($key){
		return $this->memcache->get($key);
	}
	protected function delFromCache($key){
		return $this->memcache->delete($key);
	}
	
	
	
	//基本redis操作
	protected function setRedisHash($key,$field,$value){
		return $this->redis->HMSET ( $key,array($field=>$value));
	}
	
	protected function getRedisHash($key,$field){
		return $this->redis->HGET ( $key,$field);
	}
	
	protected function getRedisHashAll($key){
		return $this->redis->HGETALL($key);
	}
	protected function incrList($key,$field){
		return $this->redis->HINCRBY($key,$field,1);
	}
	
	protected function pushList($key,$field){
		return $this->redis->RPUSH ( $key,$field);
	}
	protected function pushListLeft($key,$field){
		return $this->redis->LPUSH ( $key,$field);
	}
	protected function getListAll($key){
		return $this->redis->LRANGE($key,0,$this->redis->LLEN($key));
	}
	
	protected function getListRange($key,$start,$end){
		return $this->redis->LRANGE($key,$start,$end);
	}
	
	protected function getListLen($key){
		return $this->redis->LLEN($key);
	}
	protected function getHashLen($key){
		return $this->redis->HLEN($key);
	}
	
	protected function removeList($key, $value) {
		return $this->redis->LREM ( $key,$value );
	}
	
	protected function removeHash($key, $value) {
		return $this->redis->HDEL ( $key,$value );
	}
	
	protected function getListValueByIndex($key,$index){
		return $this->redis->LINDEX($key,$index);
	}
	
	protected function delRedis($key){
		return $this->redis->DEL($key);
	}
	
	protected function isExit($key,$value){
		return $this->redis->HEXISTS($key,$value);
	}

	
	//有序集合
	protected function sortAdd($key,$souce,$member){
		return $this->redis->ZADD($key,$souce,$member);
	}
	
	protected function incrSortOne($key,$souce,$member){
		return $this->redis->ZINCRBY($key,$souce,$member);
	}
	
	protected function getSortRankLowToHigh($key,$member){
		return $this->redis->ZREVRANK($key,$member);
	}
	protected function getSortRank($key,$member){
		return $this->redis->ZRANK($key,$member);
	}
	protected function getSortValue($key,$member){
		return $this->redis->ZSCORE($key,$member);
	}
	
	protected function getRankString($key,$start,$end){
		return $this->redis->ZRANGE($key,$start,$end,true);
	}
	protected function getRankStringRev($key,$start,$end){
		return $this->redis->ZREVRANGE($key,$start,$end,true);
	}
	
	
	
	//这块mongodb 太逆天啦，我单独处理一下试试
	
	
	protected function insertMongo($content,$collectionName,$dbname='century'){
		if(!isset($content['_id'])){
			$content['_id']=$this->getIdNew($collectionName);
		}
		if(!isset($content['time'])){
			$content['time']=time();
		}
		try {
			$mongoDB=$this->getMongdb($dbname);
			$mongoCollection = $mongoDB->selectCollection ($collectionName );
			$ret = $mongoCollection->insert ( $content );
			return $content['_id'];
		} catch ( Exception $e ) {
			die ( $e->getMessage () );
		}
		
	}

	
	/**更新mongo内容
	 * @param unknown_type array('nickname'=>'wanbin')
	 * @param unknown_type array('roomid'=>intval ( $id ))
	 * @param unknown_type $dbname
	 * @return boolean
	 */
	protected function updateMongo($content, $where, $collectionName,$dbname='century',$inc=array()) {
		try {
			$mongoDB=$this->getMongdb($dbname);
			$mongoCollection = $mongoDB->selectCollection ($collectionName );
			if (! empty ( $inc )) {
				$result = $mongoCollection->update ( $where, array (
						'$set' => $content,
						'$inc' => $inc 
				) );
			} else {
				$result = $mongoCollection->update ( $where, array (
						'$set' => $content 
				) );
			}
	
			return true;
		} catch ( Exception $e ) {
			die ( $e->getMessage () );
		}
	}
	
	protected function removeMongo( $where, $collectionName,$dbname='century') {
		try {
			$mongoDB=$this->getMongdb($dbname);
			$mongoCollection = $mongoDB->selectCollection ($collectionName );
			$result = $mongoCollection->remove ( $where );
			return true;
		} catch ( Exception $e ) {
			die ( $e->getMessage () );
		}
		return ;
	}
	
	protected function getFromMongo($where, $collectionName,$sort=array('_id'=>-1),$skip=0,$limit=100,$dbname='centurywar') {
		if($sort!=1){
			$sort=-1;
		}
		$ret=array();
		try {
			$mongoDB=$this->getMongdb($dbname);
			$mongoCollection = $mongoDB->selectCollection ( $collectionName );
			$mongoCursor = $mongoCollection->find ( $where )->sort(array('_id'=>$sort))->skip($skip)->limit($limit);
			while ( $mongoCursor->hasNext () ) {
				$ret[]= $mongoCursor->getNext ();
			}
			return $ret;
		} catch ( Exception $e ) {
			die ( $e->getMessage () );
		}
		return array();
	}
	
	protected function getOneFromMongo($where, $collectionName,$dbname='century') {
		$ret=array();
		try {
			$mongoDB=$this->getMongdb($dbname);
			$mongoCollection = $mongoDB->selectCollection ( $collectionName );
			$mongoCursor = $mongoCollection->find ($where)->limit(1);
			while ( $mongoCursor->hasNext () ) {
				$ret [] = $mongoCursor->getNext ();
			}
			return $ret[0];
		} catch ( Exception $e ) {
			die ( $e->getMessage () );
		}
		return ;
	}
	
	
		// =================================MYSQL============================================//
		
	// 不支持同一业务的数据库水平部署在不同服务器上
	protected function getDBInstance($tableName) {
		static $DBInstances = array ();
		if (isset ( $DBInstances [$tableName] )) {
			return $DBInstances [$tableName];
		}
		$DBHandler = new DBHandler ( $tableName, $this->gameuid, $this->server );
		$DBInstances [$tableName] = $DBHandler;
		return $DBHandler;
	}
	
	
	public function oneSqlSignle($sql) {
		$ret=$this->oneSql($sql);
		return $ret[0];
	}
	
	public function oneSql($sql) {
		$this->baidudebug($sql);
		$DBHandler = $this->getDBInstance ( $this->getTableName () );
		$tem = explode ( ' ', $sql );
		if (in_array ( $tem [0], array (
				'insert',
				'update',
				'replace',
				'delete'
		) )) {
			$sqlarr=explode(';', $sql);
			foreach ($sqlarr as $key=>$value){
				 $this->BaiduExecute ( $value );
			}
			return;
		} else {
			return $this->BaiduContent($sql);
		}
	}
	

	protected function getTableName() {
		return '';
	}
	
	/**
	 +----------------------------------------------------------
	 * 连接值的字符串
	 +----------------------------------------------------------
	 * @param array $values
	 * @return string
	 +----------------------------------------------------------
	 */
	private function joinValuesStr($values)
	{
		foreach ( $values as $key => $val )
		{
			$str .= $key . "='" . $val . "',";
		}
		$str = rtrim ( $str, ',' );
		return $str;
	}
	
	
	public function writeSqlError($sql, $e) {
		if (ISBAIDU) {
			require_once FRAMEWORK."/BaeLog.class.php";
			$user = BAIDU_AK;
			$pwd = BAIDU_SK;
			$logger=BaeLog::getInstance(array('user'=>$user, 'passwd'=> $pwd));
			$logger->setLogLevel(16);
			$logger->setLogTag("sql_error");
			$logger->Fatal($e);
		}else{
			$fileName = date ( "Y-m-d", time () ) . "sqlerror.sql";
			$temtime = date ( "Y-m-d H:i:s", time () );
			$strAdd = "#[$temtime]\n";
			file_put_contents ( PATH_ROOT . "/log/$fileName", $strAdd . $e . $sql, FILE_APPEND );
		}
	}
	
	//=========================================Cache=====================================//

	/**
	 * 打印调试信息
	 *
	 * @param $msg string
	 *       	 消息
	 * @param $var mixed
	 *       	 附加的变量值
	 */
	protected function debug($msg, $var = null) {
		if (DEBUG) {
			$file = PATH_LOG.'/model_debug_' . date ( 'Y-m-d' ) . '.log';
			$m = '[' . date ( 'Y-m-d H:i:s' ) . '] ' . $msg."\n";
			if (isset ( $var )) {
				$m .= print_r ( $var, true );
			}
			try {
				file_put_contents ( $file, $m . "\n", FILE_APPEND );
			} catch ( Exception $e ) {
			}
		}
	}
	
	protected function getIdNew($idname) {
		return $this->redis->HINCRBY( "REDIS_KEY_ADD_ID",$idname,1);
	}
	
	protected function getMongdb($dbname='centurywar') {
			$mongoClient = new MongoClient ( "mongodb://localhost:27017");
			$mongoDb = $mongoClient->selectDB ($dbname);
			return $mongoDb;
	}
	
	public function  getTimeStr($time){
		if(time()-$time<60){
			return "刚刚";
		}
		if(time()-$time<600){
			return "几分钟前";
		}
		if(time()-$time<3600){
			return "一小时前";
		}
		return date("Y-m-d",$time);
	}
}