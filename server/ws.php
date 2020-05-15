<?php

class Websocket{
	
	public $server = null;	
	public $fdList = [];
	public $fdNameList = []; // 名称与fd映射表

	public function __construct()
	{

		$this->server = new Swoole\WebSocket\Server(WS_HOST, WS_PORT);

		// 服务端设置, 使用到 task 必须设置
		$this->server->set([
			'worker_num' => 1,
			// 'task_worker_num' => 1,
		]);

		// 基础事件绑定
		$this->server->on('open', [$this, 'onOpen'] );
		$this->server->on('message',[$this, 'onMessage']);
		$this->server->on('close',[$this, 'onClose']);
		
		echo PHP_EOL.'服务已启动...';
		echo PHP_EOL.'监听地址：'.WS_HOST;
		echo PHP_EOL.'监听端口：'.WS_PORT;

		$this->server->start();
	}

	/**
	 * 监听打开事件
	 * @param  [type] $ws      [description]
	 * @param  [type] $request [description]
	 * @return [type]          [description]
	 */
	public function onOpen($ws,$request)
	{
		$nickname = randName();

		// 返回昵称
		$nameData = [
			'type'   =>  'nickname',
			'msg'    =>  $nickname
		];
		$this->sendMsg($ws, [$request->fd], $nameData);
		
		// 存放加入用户
		$this->fdList[] =  $request->fd;
		$this->fdNameList[ $request->fd ] = $nickname;

		// 通知新用户加入
		$data = [
			'type'   =>  'system',
			'msg'    =>  '欢迎 ' .$nickname. ' 加入房间'
		];

		$this->sendMsg( $ws, $this->fdList, $data );

		// 返回当前在线列表
		$memberData = [
			'type' => 'member',
			'memberList'  => array_values($this->fdNameList) 
		];

		$this->sendMsg( $ws, $this->fdList, $memberData );
	}

	/**
	 * 监听消息事件
	 * @param  [type] $ws    [description]
	 * @param  [type] $frame [description]
	 * @return [type]        [description]
	 */
	public function onMessage($ws,$frame)
	{
		echo '--onMessage--'.PHP_EOL;
		// 接收方去掉自己
		$fdList = array_diff($this->fdList, [$frame->fd]);

		$data = [
			'type' => 'user',
			'name' => $this->fdNameList[$frame->fd],
			'msg'  => $frame->data
		];

		$this->sendMsg($ws,$fdList,$data);
	}

	/**
	 * 断开连接事件
	 * @param  [type] $wd [description]
	 * @param  [type] $fd [description]
	 * @return [type]     [description]
	 */
	public function onClose($ws,$fd)
	{
		$this->fdList = array_diff($this->fdList, [$fd]);

		$data = [
			'type'   =>  'system',
			'msg'    =>  $this->fdNameList[$fd] . ' 已离开房间'
		];

		$this->sendMsg($ws,$this->fdList,$data);

		unset($this->fdNameList[$fd]);

		$memberData = [
			'type' => 'member',
			'memberList'  => array_values($this->fdNameList) 
		];

		// 返回当前在线列表
		$this->sendMsg( $ws, $this->fdList, $memberData );
	}

	/**
	 * 发送消息方法
	 * @param  [type] $ws     [description]
	 * @param  [type] $fdList [准备发送的 fd 的集合]
	 * @param  [type] $data   [arr]
	 */
	private function sendMsg($ws,$fdList,$data)
	{
		foreach ($fdList as $fd) {
			$ws->push( $fd, json_encode($data,JSON_UNESCAPED_UNICODE) );
		}
	}

}

include_once './config.php';
include_once './function.php';

new Websocket();
