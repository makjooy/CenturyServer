<?php
/**
 * @author WanBin @date 2013-08-03
 * 微信用户表
 */
require_once PATH_MODEL.'BaseModel.php';
class UnderCoverModel extends BaseModel {
	public function __construct( $uid = null) {
		parent::__construct($uid);
		if (empty ( $this->gameuid )) {
			$this->add ( array (
					'uid' => $uid,
					'regtime' => time ()
			) );
			parent::__construct($uid);
		}
	}
	
	
	public function Log($content) {
		$gameuid=$this->gameuid;
		$time=time();
		$sql="insert into wx_log(gameuid,content,time) values ($gameuid,'$content',$time)";
		$this->oneSql($sql);
	}
	public function getMessageCount($msg){
		$sql = "select count(DISTINCT gameuid) count from wx_log where content='$msg'";
		$ret = $this->oneSqlSignle ( $sql );
		return $ret['count'];
	}
	/**
	 * 返回信息类
	 *
	 * @param unknown_type $keyword
	 * @param unknown_type $uid
	 * @return string
	 */
	public function returncontent($keyword) {
		$this->Log ( $keyword );
		$helpStr = $this->getSampleHelpStr ();
		$type = $keyword ;
		if ($type == "帮助" || $type == "【帮助】" || $type == "help" || $type == "?"|| $type == "？") {
			return $this->getHelpStr () . $helpStr;
		}
		if ($type == "规则" || $type == "【规则】" || $type == "rule" || $type == "*") {
			return $this->getRuleStr () . $helpStr;
		}
		$type = $this->changeKeyword ( $keyword );
		if ($type > 3 && $type <= 15) {
			include_once PATH_DATAOBJ . "/cache/UnderCoverRoomCache.php";
			$UnderRoomCache = new UnderCoverRoomCache ( $this->uid );
			$str = $UnderRoomCache->initRoom ( $type );
			return $str . $helpStr."\n回复2或3显示本局惩罚";
		} else if ($type == 1) {
			$str = "谁是卧底游戏创建成功，您为法官，请输入参与人数（不包括法官 4-15人）：";
			return $str.$helpStr;
		} else if ($type == 2) {
			include_once PATH_DATAOBJ . "/cache/UnderCoverRoomCache.php";
			$UnderRoomCache = new UnderCoverRoomCache ($this->uid);
			$str = $UnderRoomCache->getChengfa ( 2 );
			return $str.$helpStr;
		} else if ($type == 3) {
			include_once PATH_DATAOBJ . "/cache/UnderCoverRoomCache.php";
			$UnderRoomCache = new UnderCoverRoomCache ($this->uid);
			$str = $UnderRoomCache->getChengfa ( 3);
			return $str.$helpStr;
		}  else if ($type >= 1000) {
			$gameuid = $UnderCache->gameuid;
			include_once PATH_DATAOBJ . "/cache/UnderCoverRoomCache.php";
			$UnderRoomCache = new UnderCoverRoomCache ($this->uid);
			$str = $UnderRoomCache->getInfo ( $type );
			return $str.$helpStr;
		} else {
			include_once PATH_DATAOBJ . "/cache/UnderCoverCache.php";
			$UnderCoverCache = new UnderCoverCache ( $this->uid );
			$msgCount = $UnderCoverCache->getMessageCount($keyword);
			if ($msgCount > 1) {
				$strtem = "[得意]你是本游戏中第【 $msgCount 】位用户发送这条信息了！这或许就是缘分吧，虽然小编一时半会回答不了你的问题，但相信您一定会在游戏中找到乐趣的~\n===============\n先发个游戏帮助，您先看着，看有需要的内容吗\n================\n";
			} else {
				$strtem = "[可怜]小编找遍了所有用户发来的信息，没有发和和你这条重复的，不知如何是好,又要挨骂了~~\n================\n先发个游戏帮助，您先看着，看有需要的内容吗？\n================\n";
			}
			return $strtem.$this->getHelpStr();
		}
	}
	public function changeKeyword($keyword) {
		if (intval ( $keyword ) > 0) {
			return intval ( $keyword );
		}
		switch ($keyword) {
			case "玩" :
			case "开始" :
			case "开局" :
				return 1;
			case "怎么玩" :
			case "帮忙" :
			case "帮助内容" :
			case "怎么开始" :
				return "help";
		}
	}
	protected function getHelpStr() {
		return "一起来玩吧，体验不一样的谁是卧底游戏！回复以下内容快速开始：\n 1.创建谁是卧底游戏\n 2.真心话大冒险（网络版）\n 3.真心话大冒险（本地版）\n 4-15.创建谁是卧底房间\n 1000-9999.进入相应的房间\n ?.帮助\n *.谁是卧底规则\n 快快来试试吧~";
	}
	//返回制作团队
	protected function getEmail() {
		return "谁是卧底请您选择项目：\n 4-14.创建谁是卧底游戏: \n 输入 20 返回真心话大冒险：";
	}
	protected function getSampleHelpStr() {
		return "\n\n【帮助】帮助内容 \n【规则】游戏规则";
	}
	protected function getRuleStr() {
		return "【谁是卧底】游戏规则 \n"
				."【人数】：法官1人，玩家4至15人\n"
				."【开局】：法官回复参与人数，返回平民及卧底身份及编号，以及房间号\n"
				."【参与】：把房间号通过群告诉所有玩家，参与者向我发送房间号，我会给他们发送身份及说明编号\n"
				."【进行】：法官组织每位玩家依次发言，每位玩家说自己编号及简短的描述自己的身份以\n"
				."【投票】：描述一轮结束后，玩家投票选择卧底，票数较多的玩家身亡。分出结果后，法官公布结果（冤死或卧底）\n"
				."【胜利】：卧底全被揪出，则平民胜利，卧底数大于等于平民数，卧底胜利\n"
				."【惩罚】：回复2或3返回真心话大冒险，输的玩家掷骰子选择惩罚\n";
	}
	

}