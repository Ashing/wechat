<?php
/**
 * 小区租售
 */

namespace Admin\Controller;


class RentalController extends AdminController
{
    //小区租售显示
    public function index()
    {
        //实例化对象,查询所有租售信息
        $map['status'] = ['GT',0];
        $rentals = M('rental')->where($map)->select();
        $this->assign('rentals',$rentals);
        $this->display();
    }

    //新增
    public function add()
    {
        if(IS_POST){
            //实例化模型
            $rental = D('Rental');
            $data = $rental->create();
            if ($data){
                if ($rental->add()){
                    $this->success('新增成功',U('index'));
                }else{
                    $this->error('新增失败');
                }
            }else{
                $this->error($rental->getError());
            }
        }else{
            $danwei = ['danwei'=>[1=>'元/月',2=>'万元']];
            $this->assign($danwei);
            $this->display('edit');
        }
    }

    //编辑
    public function edit($id)
    {
        if (IS_POST){
            //实例化模型对象
            $repair = D('Rental');
            //根据表单提交的post数据创建数据对象
            $data = $repair->create();
            if ($data){
                //修改
                if($repair->save()){
                    //记录行为
                    $this->success('编辑成功',U('index'));
                }else{
                    $this->error('编辑失败'.$repair->getError());
                }
            }else{
                $this->error($repair->getError());
            }
        }else{
            //根据id取出数据
            $info = M('rental')->find($id);
            if ($info == null){
                $this->error('数据未找到');
            }
            //回显到页面
            $this->meta_title = '编辑小区租售';
            $danwei = ['danwei'=>[1=>'元/月',2=>'万元']];
            $this->assign('info',$info);
            $this->assign($danwei);
            $this->display();
        }
    }

    //删除
    public function del($id = 0)
    {
        //判断是否选择操作的数据
        if (empty($id)){
            $this->error('请选择要操作的数据');
        }
        $data['status'] = -1;
        if (M('rental')->data($data)->where('id='.$id)->save()){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }
}