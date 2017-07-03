<?php
namespace Admin\Controller;
use Think\Controller;
class UserController extends AllowController {
    //_empty 在访问一个不存在控制器里不存在的方法的时候 会自动的访问_empty 
   public function _empty(){
   
    echo "您访问的控制器下的方法".ACTION_NAME."不存在";
   }
   public function search(){
        // $data="Asdf";
        // $mod=M("Users");
        // var_dump($_GET);exit;
        // $like=$_POST['search'];
        // $list=$mod->query("select * from users where username like '%$like%'");
        // var_dump($data);

        // $this->assign('data',$data);
        // //加载模板
        // $this->display('User/index');

        

   }

   public function index(){
   	    
        $mod=M('Users');
        // if($_POST){
        //     echo "xx";exit;
        // }
        // var_dump($_POST);
        //获取数据总条数
        $tot=$mod->Count();
        //定义每页显示的数据条数
        $rev=4;
        //实例化分页类
        $page=new \Think\Page($tot,4);
        //设置分页
        $page->setConfig('prev','上一页');
        $page->setConfig('next','下一页');
        $page->setConfig('first','首页');
        $page->setConfig('last','末页');
        $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
        // var_dump($pp);exit;
         //获取结果集
        $list=$mod->limit($page->firstRow,$page->listRows)->select();
        
        
        // $this->assign('list',$list);
        //组装分页
        $this->assign('pageinfo',$page->show());
        $this->assign('list',$list);
        //加载模板
        $this->display('User/index');
    }
    //加载添加，模板
    public function add(){
        $this->display("User/add");
    }

    //用户详情
    public function details(){
        $mod=new \Think\Model('users');
        $stu=$mod->find($_GET['id']);
        // var_dump($stu);exit;
        $this->assign('stu',$stu);
        $this->display("User/details");

    }

    //执行添加
    public function insert(){
        
        if(!empty($_FILES['pic']['name'])){
            //实例化
            $upload=new \Think\Upload();
            //大小
            $upload->maxSize=123234435345;
            //类型
            $upload->exts=array('jpg','png','gif','jpeg');
            //保存路径
            $upload->rootPath="./Public/Uploads/";
            //是否具有日期目录
            $upload->autoSub=true;

            //执行上传
            $info=$upload->upload();
            if(!$info){
                //显示错误信息
                $this->error($upload->getError());
            }else{
                //遍历
                foreach($info as $file){
                    //日期目录
                    $savepath=$file['savepath'];
                    //获取上传以后的图片
                    $savename=$file['savename'];
                    $pic=$savepath.$savename;
                }
            }
        }
        $user=D('User');
        if(!$user->create()){
            //创建数据对象失败 把错误的提示信息输出
            $this->error($user->getError());
        }
        //实例化model
        $mod=M('users');  
        $data['username']=$_POST['username'];
        $data['password']=$_POST['password'];
        $data['email']=$_POST['email'];
        $data['status']=0;
        $data['token']=$_POST['token'];
        $data['addtime']=time();
        $data['rid']=$_POST['rid'];
        $data['pic']=$pic;
        //执行添加  
        if($mod->add($data)){
            $this->success('添加成功',U('User/index'),1);
            // echo "cg";
        }else{
            $this->error('添加失败',U('User/add'));
            // echo "sb";
        }
        
    }
    

     //执行删除
    public function delete(){
        $mod=new \Think\Model('users');
        //获取id
        $id=$_GET['id'];
        // echo $id;
        if($mod->delete($id)){
            $this->success("删除成功",U("User/index"));
        }else{
            $this->error('删除失败',U('User/index'));

        }
    }
    // 加载修改模板
     public function edit(){
         $mod=new \Think\Model('users');
         if($_GET['id']==""){

         }else{
         $stu=$mod->find($_GET['id']);

         }
         // var_dump($stu);exit;
         $this->assign('stu',$stu);
         $this->display('User/edit');
    }
    //权限管理
    public function quanxiangl(){
        $mod=new \Think\Model('role');
        $list=$mod->select();
        // var_dump($list);exit;
        $this->assign('list',$list);

        $this->display('User/quanxiangl'); 
    }
    //添加管理员
    public function addgl(){
        $this->display('User/addgl'); 

    }
    //执行管理员添加
    public function insertgl(){
        // var_dump($_POST);
        if(M("role")->add($_POST)){
            $this->success("管理员添加成功，请去设置管理模块",U("User/quanxiangl"));
            
        }else{
            $this->error('修改失败',U('User/addgl'));
            echo "sb";
        }
        
    }
    //修改管理模块
    public function level(){
        $id=$_GET['id'];
        $mod=M("node");
        // $list=;
        // $this->assign("role",M("role")->find($id));
        $data=$mod->field("id,name")->select();
        $level=M("role_node")->where("rid={$id}")->select();
        // var_dump($level['nid']);exit;

        $rids=array();
        foreach ($level as $v) {
            $rids[]=$v['nid'];
            // var_dump($rids);exit;
        }

        // var_dump($rids);exit;
        $this->assign('id',$id);
        $this->assign('rids',$rids);
        $this->assign('data',$data);        
        $this->display("User/level");
    }
    //执行修改模块管理
    public function uplevel(){
        $nid=I("post.rid");
        // var_dump($uid);
        // // var_dump($_GET);
        $rid=I("get.id");
        M("role_node")->where("rid=$rid")->delete();
        // var_dump($nid);exit;
        foreach($nid as $v){
            $data['nid']=$v;
            $data['rid']=$rid;
            M("role_node")->add($data);
        }
      $this->success("角色分配成功",U("User/quanxiangl"));
        

    }
    //权限修改模板
    public function quanxian(){
        // $stu=M('user_role');
        // $user=$stu->select();
        // $data=M("users")->query("select role.name from __PREFIX__role role,__PREFIX__users us where us.rid=role.id");
        
        $mod=M('users');
        $data=M('role')->select();
        // var_dump($data);

        // $datas=array();
        // foreach($data as $val){
        //     $datas['id']=$val['id'];
        //     $datas['name']=$val['name'];
        //     // var_dump($datas);

        //     // var_dump($mr);exit;
        //     // var_dump($datas);
        // }
        // $stu=array('11','22','33');
        // foreach($data as $v){
        //     $stu[]=$v['id'];

        // }
        $arr=array('无','超级管理员','普通管理员','123');
        // ->where("rid=$data['id']")
        // $list=M("users")->where
        $this->assign('arr',$arr);
        $this->assign('data',$data);
        $this->assign('list',M('users')->select());
        $this->display('User/quanxian');
    }
    //执行权限修改
    public function updateqx(){
        $user=new \Think\Model('users');
        // var_dump($_GET);
        // var_dump($_POST);exit;
        $data['id']=$_GET['id'];
        $data['rid']=$_POST['rid'];
        if($user->save($data)){
            $this->success("修改成功",U("User/quanxian"));
        }else{
            $this->error('修改失败',U('User/quanxian'));

        }
       
    }
    // 执行修改
    public function update(){
        $user=new \Think\Model('users');
        $stu=$user->find($_POST['id']);
        unlink("Public/Uploads/".$stu['pic']);
        // var_dump("__ROOT__/Public/Uploads/".$stu['pic']);exit;
        if(!empty($_FILES['pic']['name'])){

            //实例化
            $upload=new \Think\Upload();
            //大小
            $upload->maxSize=123234435345;
            //类型
            $upload->exts=array('jpg','png','gif','jpeg');
            //保存路径
            $upload->rootPath="./Public/Uploads/";
            //是否具有日期目录
            $upload->autoSub=true;

            //执行上传
            $info=$upload->upload();
            if(!$info){
                //显示错误信息
                $this->error($upload->getError());
            }else{
                //遍历
                foreach($info as $file){
                    //日期目录
                    $savepath=$file['savepath'];
                    //获取上传以后的图片
                    $savename=$file['savename'];
                    $data['pic']=$savepath.$savename;
                }
            }
        }
        
         if($_POST['repassword']!==$stu['password']){
             $this->error('原始密码不正确',U('User/index'));
           
         }
         if($_POST['username']==""){
             $this->error('用户名不能为空',U('User/index'));
         }
         //封装数据
         // $user->create();
        // var_dump($_POST);
        // var_dump($stu);exit;
        $data['id']=$_POST['id'];
        $data['username']=$_POST['username'];
        $data['password']=$_POST['password'];
        // $data['level']=$_POST['level'];
         if($user->save($data)){
           
            $this->success("修改成功",U("User/index"));
         }else{
            // $user->getError();exit;
            $this->error('修改失败',U('User/index'));

         }
    }
}