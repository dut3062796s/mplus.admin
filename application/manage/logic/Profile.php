<?php
/**
 * 个人设置逻辑层
 * User: Zachary Liang
 * Date: 16-11-28
 * Time: 下午5:34
 */

namespace app\manage\logic;
use app\common\common;
use app\manage\common\logic\BaseLogic;
use think\Config;
use think\Session;

/**
 * Class Profile
 * @package app\manage\logic
 */
class Profile extends BaseLogic
{
    /**
     * 上传头像
     * @param $param
     * @return array
     */
    public function upload_avatar($param){
        $base64_image = str_replace(' ', '+', $param['avatar']);
        //post的数据里面，加号会被替换为空格，需要重新替换回来，如果不是post的数据，则注释掉这一行
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image, $result)){
            //匹配成功
            if($result[2] == 'jpeg'){
                $image_name = Session::get('admin.account').'_'.common::get_uniqueness_id().'.jpg';
                //纯粹是看jpeg不爽才替换的
            }else{
                $image_name = Session::get('admin.account').'_'.common::get_uniqueness_id().'.'.$result[2];
            }
            $image_file = Config::get('UPLOAD_PATH')."/avatar/{$image_name}";
            //服务器文件存储路径
            if (file_put_contents($image_file, base64_decode(str_replace($result[1], '', $base64_image)))){
                $result = db('admin_profile')
                    ->where('uid', Session::get('admin.id'))
                    ->update(['avatar'=>ltrim($image_file, '.')]);
                if ($result) {
                    Session::set('admin.avatar', ltrim($image_file, '.'));
                    return \app\manage\common::return_result('200', AVATAR_UPDATE_SUCCESS_TEXT, null);
                } else {
                    return \app\manage\common::return_result('500', AVATAR_UPDATE_FAILED_TEXT, null);
                }
            }else{
                return \app\manage\common::return_result('500', AVATAR_UPDATE_FAILED_TEXT, null);
            }
        }else{
            return \app\manage\common::return_result('500', AVATAR_UPDATE_FAILED_TEXT, null);
        }
    }
}