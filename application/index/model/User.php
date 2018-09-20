<?php

namespace app\index\model;

use think\Model;
use think\model\concern\SoftDelete;

class User extends Model
{
	use SoftDelete;
	protected $deleteTime = 'delete_time';

    //月利息
	public function getyueAttr($value,$data){
		$yue = $data['money']*$data['lixi']*0.01;
		return $yue;
	}

	//总共天数
	public function getalltimeAttr($value,$data){

		//开始时间转化为时间戳
		$start_time = strtotime($data['start_time']);

		//结束时间转化为时间戳
		$end_time = strtotime($data['end_time']);

		//计算借款天数
		$alltime = $end_time - $start_time;

		$days = $alltime/60/60/24;

		return $days;
	}

	//已借天数
	public function getyijietimeAttr($value,$data){

		//结束时间转化为时间戳
		$start_time = strtotime($data['start_time']);

		//计算借款天数
		$yijietime = time() - $start_time;

		$days = floor($yijietime/60/60/24);

		return $days;
	}

	//剩余天数
	public function getshengyutimeAttr($value,$data){

		//结束时间转化为时间戳
		$end_time = strtotime($data['end_time']);

		//计算借款天数
		$alltime = $end_time - time();

		$days = floor($alltime/60/60/24);

		if ($days < 0) {
			$days = abs($days);
			return "<span style='color:red'>逾期{$days}天!</span>";
		}

		return $days;
	}

	//应还利息
	public function getyinghuanlixiAttr($value,$data){
		//开始时间转化为时间戳
		$start_time = strtotime($data['start_time']);

		//计算已经借款月数
		$yue = floor((time() - $start_time)/60/60/24/30);

		//计算应该还的利息
		$yinghuanlixi = $data['money']*$data['lixi']*0.01*$yue;

		return $yinghuanlixi;
	}
}
