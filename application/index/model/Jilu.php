<?php

namespace app\index\model;

use think\Model;

class Jilu extends Model
{
	//月利息
	public function setfieldAttr($value){
		switch ($value) {
			case 'name':
					return "姓名";
				break;
			case 'tel':
					return "手机号";
				break;
			case 'money':
					return "借款";
				break;
			case 'lixi':
					return "利息";
				break;
			case 'start_time':
					return "开始时间";
				break;
			case 'end_time':
					return "结束时间";
				break;
			case 'yihuanlixi':
					return "已还利息";
				break;
			default:
				# code...
				break;
		}
	}
}
