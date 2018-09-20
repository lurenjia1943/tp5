<?php
namespace app\index\controller;
use sms\api_demo\SmsDemo;
use think\Request;
use think\Controller;
use think\Db;
use app\index\model\User as U;
use app\index\model\Jilu as J;

class Index extends Controller
{
    //protected $middleware = ['Auth'];
    protected $middleware = [
        'Auth' => ['except'  => ['login','sms']],
    ];

    protected $tel = ['13653592881'];

    //登陆
    public function login(){
        $tel = input('tel');
        $captcha = input('captcha');
        
        if (in_array($tel, $this->tel) and $captcha == session($tel)) {
            session('user_id', 'wjl');
            session('captcha', null);
            echo "OK";
        }else{
            echo "NOT OK";
        }
    }

    //退出
    public function loginout(){
        session(null);
        $this->redirect('/index');
    }

	//后台首页
    public function index()
    {
        return view();
    }

    public function welcome()
    {
        $user = new U;
        $count = $user->group('name')->count();
        $zongbenjin = $user->sum('money');//剩余总本金
        //应还利息
        $arr = $user->all();
        $yinghuanlixi = 0;
        foreach($arr as $k=>$v){
            $value = $v->yinghuanlixi;
            $yinghuanlixi += $value;
        }
        $yihuanlixi = $user->sum('yihuanlixi');//已还利息
        $zonglixi = $yinghuanlixi-$yihuanlixi;//剩余总利息
        $zongjine = $zonglixi + $zongbenjin;//剩余总金额

        $this->assign("zongjine",$zongjine);
        $this->assign("zongbenjin",$zongbenjin);
        $this->assign("zonglixi",$zonglixi);
        $this->assign("count",$count);

        $this->assign("phpversion",phpversion());
        $this->assign("SERVER_SOFTWARE",$_SERVER['SERVER_SOFTWARE']);
        return view();
    }

    //借款人列表
    public function member_list()
    {
        return view();
    }

    //借款人列表
    public function jiekuan(){
        $users = U::json()->select();
        $rows = U::count();
        $users = '{"code": 0,"msg": "","count": '.$rows.',"data":'.$users.'}';
        echo $users;
    }

    //借款人列表
    public function jieqing_list()
    {
        return view();
    }

    //结清的账单列表
    public function jieqing(){
        $users = U::onlyTrashed()->json()->select();
        $rows = U::onlyTrashed()->count();
        $users = '{"code": 0,"msg": "","count": '.$rows.',"data":'.$users.'}';
        echo $users;
    }

    

    //新增借款人页面
    public function member_add(){
    	return view();
    }

    //执行新增借款人
    public function member_insert(){
    	$user = new U;
		// 过滤post数组中的非数据表字段数据
		$user->save(input(''));
		$this->success('新增成功');
    }

    //单元格编辑
    public function edit(){
        $id = input('id');
        $field = input('field');
        $value = input('value');

        $user = new U;
        $oldvalue = $user ->find($id)->$field;
        //过滤post数组中的非数据表字段数据
        $m = $user->save([$field=>$value], ['id' => $id]);

        $jilu = new J;
        $jilu->user_id = $id;
        $jilu->field = $field;
        $jilu->newvalue = $value;
        $jilu->oldvalue = $oldvalue;
        $jilu->save();

        if ($m) {
            echo "更新成功";
        }else{
            echo "更新失败";
        }
        
    }

    //结清账单
    public function del(){
        U::destroy(input('id'));
    }

    //还款详情
    public function detail(){
        $id = input('id');
        $jilu = J::where('user_id', $id)->select();
        $num = 1;

        $table = "<table class='layui-table'>";
        $table .= "<thead><tr><th>编号</th><th>操作</th><th>时间</th></tr></thead><tbody>";
        foreach ($jilu as $k => $v) {
            $table .= "<tr><td>{$num}</td><td>将<span style='color:red'>{$v['field']}</span>由<span style='color:red'>{$v['oldvalue']}</span>改为<span style='color:red'>{$v['newvalue']}</span></td><td>{$v['create_time']}</td></tr>";
            $num++;
        }
        $table .= "</tbody></table>";
        
        echo $table;
    }

    //发短信
    public function sms(){
        $rand = mt_rand(100000,999999);

        $tel = input('tel');
        //$tel = "13653592881";

        if (!in_array($tel, $this->tel)) {
            echo "手机号码错误!";
            die();
        }

        $response = SmsDemo::sendSms($tel,$rand);
        session($tel, $rand);

        if ($response->Message == "OK") {
            echo "发送成功";
        }else{
            echo "发送失败";
        }
    }
}
