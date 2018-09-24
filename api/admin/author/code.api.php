<?php

/**
 * 订阅码
 */
class CodeApi extends BaseAdminAuth {

    /**
     * 购买过的可看的作者
     * @return array
     */
    public function index($params){

        $author_id=$params['author_id'];
        $user_id=$params['uid'];
        $type=$params['type'];
        $page=$params['page'];
        $limit=$params['limit'];

        $page<1 && $page=1;
        !$limit && $limit=20;

        $where=[];

        $sql="SELECT c.*,count(1) as author_total,GROUP_CONCAT(a.id,'$$',a.name SEPARATOR '##') as author_list FROM oa_code c LEFT JOIN oa_author_code ac ON c.id=ac.code_id LEFT JOIN oa_author a ON ac.author_id=a.id";

        $total_sql="SELECT count(DISTINCT c.id) as count FROM oa_code c LEFT JOIN oa_author_code ac ON c.id=ac.code_id ";

        !empty($author_id) && $where[]=" ac.author_id=$author_id";
        !empty($user_id) && $where[]=" c.user_id=$user_id";

        !empty($where) && $sql.=' WHERE '.implode(' AND ', $where);
        !empty($where) && $total_sql.=' WHERE '.implode(' AND ', $where);

        $sql.=" GROUP BY c.id ORDER BY c.id DESC LIMIT ".($page-1)*$limit.",$limit";
        // echo $sql;exit;
        $data=t('code')->query($sql);
        $total=t('code')->one($total_sql)['count'];

        $result=[];

        foreach ($data as $d) {

            $d['create_time']=date('Y-m-d H:i:s',$d['date_add']);
            $d['expired_time']=date('Y-m-d',$d['date_expired']);
            $d['user_phone']='';

            if($d['user_id']){

                $userinfo=t('user')->where(['id'=>$d['user_id']])->get();

                $d['user_phone']=$userinfo['phone'];
            }
            
            if($d['author_list']){

                $author_list=explode('##', $d['author_list']);

                foreach ($author_list as $author) {
                    
                    list($auhtor_id,$name)=explode('$$', $author);

                    $d['authors'][]=['id'=>$author_id,'name'=>$name];
                }
            }

            unset($d['author_list']);

            $result[]=$d;
        }

        $this->ok(['codes'=>$result,'total'=>intval($total)]);
    }

    public function detail($params){

        $code_id=$params['code_id'];

        if(empty($code_id)) $this->error('参数不全');

        $code=t('code')->where(['id'=>$code_id])->get();

        if(empty($code)) $this->error('邀请码不存在');

        $code['date_expired']=date('Y-m-d',$code['date_expired']);

        $authors=t('author_code')->where(['code_id'=>$code_id])->find();

        foreach ($authors as $author) {
            
            $code['authors'][]=$author['author_id'];
        }

        $this->ok($code);
    }

    public function add($params){

        $expired_date=$params['date_expired'];
        $authors=$params['authors'];
        $count=$params['count'];

        if(empty($count) || empty($expired_date) || empty($authors)) $this->error('参数不全');

        try{

            t('code')->start();

            for ($i = 0; $i < $count; $i++) {
                
                $code_name=uniqid();

                $timestamp=strtotime($expired_date);
                $year=date('Y',$expired_date);
                $month=date('m',$expired_date);

                $code_id=t('code')->insert(['code'=>$code_name,'date_expired'=>$timestamp,'year'=>$year,'month'=>$month]);

                foreach ($authors as $author_id) {
                    
                    t('author_code')->insert(['author_id'=>$author_id,'code_id'=>$code_id,'code'=>$code_name]);
                }
            }

            t('code')->commit();
        }catch(Exception $e){

            t('code')->rollback();

            $this->error('操作失败,请重试');
        }

        $this->ok();
    }

    public function edit($params){

        $code_id=$params['code_id'];
        $expired_date=$params['expired_date'];
        $authors=$params['authors'];

        if(empty($code_id) || empty($expired_date)) $this->error('参数不全');

        $code=t('code')->where(['id'=>$code_id])->get();

        if(empty($code)) $this->error('邀请码不存在');

        $timestamp=strtotime($expired_date);

        t('code')->where(['id'=>$code_id])->update(['date_expired'=>$timestamp]);

        t('author_code')->delete(['code_id'=>$code_id]);

        if(!empty($authors)){

            foreach ($authors as $author_id) {
                
                t('author_code')->insert(['code_id'=>$code_id,'author_id'=>$author_id]);
            }
        }

        $this->ok();
    }

    /**
     * 批量添加邀请码
     * @param  array $params 
     * @return 
     */
    public function multiadd($params){

        $uid=$params['uid'];
        $multi=$params['multi'];

        if(empty($multi)) $this->error('请指定作者及生成的数量');

        foreach ($multi as $index) {

            $authors=$index['authors'];
            $count=$index['count'];
            $expired_date=$index['date_expired'];

            if(empty($authors) || empty($count) || empty($expired_date)) $this->error("参数为空");

            $timestamp=strtotime($expired_date);

            // if(time()>$timestamp) $this->error('过期时间应大于今天');
        }

        try{

            t('code')->start();

            foreach ($multi as $index) {

                $authors=$index['authors'];
                $count=$index['count'];
                $timestamp=strtotime($index['date_expired']);
                
                for ($i = 0; $i < $count; $i++) {
                    
                    $code=uniqid($author_id.'a');

                    $year=date('Y',$timestamp);
                    $month=date('m',$timestamp);

                    $code_id=t('code')->insert(['code'=>$code,'date_expired'=>$timestamp,'year'=>$year,'month'=>$month]);

                    foreach ($authors as $author_id) {
                        
                        t('author_code')->insert(['author_id'=>$author_id,'code_id'=>$code_id,'code'=>$code]);
                    }
                }
            }

            t('code')->commit();
        }catch(Exception $e){
            
            t('code')->rollback();

            $this->error("异常,请重试");
        }

        $this->ok();
    }

    /**
     * 批量编辑 
     * @param  array $params 
     * @return 
     */
    public function multiedit($params){

        $ids=$params['ids'];
        $expired_date=$params['date_expired'];

        if(empty($ids) || !is_array($ids)) $this->error('未指定邀请码');

        $timestamp=strtotime($expired_date);

        // if(time()>$timestamp) $this->error('过期时间应大于今天');

        try{
            t('code')->start();

            $year=date('Y',$timestamp);
            $month=date('m',$timestamp);
            
            t('code')->where(['id'=>['$in'=>$ids]])->update(['date_expired'=>$timestamp,'month'=>$month,'year'=>$year]);

            t('code')->commit();
        }catch(Exception $e){

            t('code')->rollback();
        }

        $this->ok();
    }

    /**
     * 分配邀请码给用户
     * @param   $params 
     * @return
     */
    public function assign($params){

        $user_id=$params['uid'];
        $code_id=$params['code_id'];

        if(empty($user_id) || empty($code_id)) $this->error("参数不全");

        $detail=t('code')->where(['id'=>$code_id])->get();

        if(empty($detail)) $this->error('邀请码不存在');

        if($detail['user_id']) $this->error('该邀请码已关联用户');

        t('code')->where(['id'=>$code_id])->update(['user_id'=>$user_id]);
        t('author_code')->where(['code_id'=>$code_id])->update(['user_id'=>$user_id]);

        $this->ok();
    }

    public function unassign($params){

        $user_id=$params['uid'];
        $code_id=$params['code_id'];

        if(empty($user_id) || empty($code_id)) $this->error("参数不全");

        $detail=t('code')->where(['id'=>$code_id,'user_id'=>$user_id])->get();

        if(empty($detail)) $this->error('邀请码不存在');

        t('code')->where(['id'=>$code_id])->update(['user_id'=>0]);
        t('author_code')->where(['code_id'=>$code_id])->update(['user_id'=>0]);

        $this->ok();
    }

    public function del($params){

        $code_id=$params['code_id'];

        if(empty($code_id)) $this->error('参数不全');

        // t('code')->where(['id'=>$code_id])->update(['status'=>0]);
        t('code')->delete(['id'=>$code_id]);

        $this->ok();
    }

    public function multidel($params){

        $codes=$params['codes'];

        if(empty($codes)) $this->error('参数不全');

        if(!is_array($codes)) $this->error('参数不对');

        $codes=array_unique($codes);

        t('code')->delete(['id'=>['$in'=>$codes]]);

        $this->ok();
    }

    public function changeexipred($params){

        $code_id=$params['code_id'];
        $date_expired=$params['expired_date'];

        if(empty($code_id) || empty($date_expired)) $this->error('参数不全');

        $code=t('code')->where(['id'=>$code_id])->get();

        if(empty($code)) $this->error('邀请码不存在');

        $timestamp=strtotime($date_expired);
        $year=date('Y',$timestamp);
        $month=date('m',$timestamp);

        t('code')->where(['id'=>$code_id])->update(['date_expired'=>$timestamp,'year'=>$year,'month'=>$month]);

        $this->ok();
    }

    public function clearexpired(){

        $expired=t('code')->field('id')->where(['date_expired'=>['$lt'=>time()]])->find();

        if(empty($expired)) $this->ok();

        $codes=array_column($expired, 'id');

        t('code')->delete(['id'=>['$in'=>$codes]]);
        t('author_code')->delete(['code_id'=>['$in'=>$codes]]);

        $this->ok();
    }
}