<?php

class DataHelper {

    public static function createTables() {

        $tables = array(
            'user' => array(
                'id' => 'char(13) NOT NULL',
                'open_id' => 'string NOT NULL',
                'name' => 'string  NOT NULL',
                'surname' => 'string  NULL',
                'password' => 'string  NULL',
                'organisation' => 'string  NULL',
                'email' => 'string  NULL',
                'phone' => 'string  NULL',
                'created_time' => 'timestamp NOT NULL DEFAULT "0000-00-00 00:00:00"',
                'update_time' => 'timestamp NOT NULL DEFAULT "0000-00-00 00:00:00" ON UPDATE CURRENT_TIMESTAMP',
                ' PRIMARY KEY (`id`)'
            ),
            'picture' => array(
                'id' => 'char(13) NOT NULL',
                'user_id' => 'int(11) NOT NULL',
                'url' => 'string  NOT NULL',
                'description' => 'varchar(400)  NULL',
                'created_time' => 'timestamp NOT NULL DEFAULT "0000-00-00 00:00:00"',
                ' PRIMARY KEY (`id`)'
            ),
            'category' => array(
                'id' => 'char(13) NOT NULL',
                'user_id' => 'int(11) NOT NULL',
                'title' => 'string  NULL',
                'public_flag' => ' tinyint(1) NOT NULL DEFAULT "0"',
                'default_flag' => ' tinyint(1) NOT NULL DEFAULT "0"',
                'password' => 'string  NULL',
                'created_time' => 'timestamp NOT NULL DEFAULT "0000-00-00 00:00:00"',
                ' PRIMARY KEY (`id`)'
            ),
            'cat_pic' => array(
                'pic_id' => 'char(13) NOT NULL',
                'cat_id' => 'char(13) NOT NULL',
            ),
            'comment' => array(
                'id' => 'char(13) NOT NULL',
                'pic_id' => 'char(13) NOT NULL',
                'comment' => 'varchar(400)  NULL',
                'created_time' => 'timestamp NOT NULL DEFAULT "0000-00-00 00:00:00"',
                ' PRIMARY KEY (`id`)'
            )
        );
        foreach ($tables as $key => $value) {
            try {
                Yii::app()->db->createCommand()->createTable($key, $value, 'ENGINE=InnoDB  DEFAULT CHARSET=utf8');
            } catch (Exception $e) {
                echo $key . "error:{$e->getMessage()}.<br/>";
            }
        }
    }

    /**
     * 
     * @param type $openId
     */
    public static function checkOpenId($openId) {
        $sql = "SELECT id FROM `user` u WHERE open_id=:open_id";
        $insert_sql = "insert into user(id,open_id,name,password,created_time) values(:id,:open_id,:name,:password,:time)";
        $id = Yii::app()->db->createCommand($sql)->queryScalar(array('open_id' => $openId));
        if (!$id) {
            $id = uniqid();
            $name = "wf{$id}";
            $pwd = md5(md5($name));
            Yii::app()->db->createCommand($insert_sql)->execute(
                    array(
                        'id' => $id,
                        'open_id' => $openId,
                        'name' => $name,
                        'password' => $pwd,
                        'time' => time()
                    )
            );
            //新用户要初始化目录
            self::initCategory($id);
        }
        return $id;
    }

    public static function initCategory($user_id) {
        $insert_sql = "insert into category(id,user_id, title, public_flag, default_flag,created_time) values(:id,:user_id,:title,:public_flag,:default_flag,:time)";
        Yii::app()->db->createCommand($insert_sql)->execute(
                array(
                    'id' => uniqid(),
                    'user_id' => $user_id,
                    'title' => '私人相册',
                    'public_flag' => 0,
                    'default_flag' => 1,
                    'time' => time()
                )
        );
        Yii::app()->db->createCommand($insert_sql)->execute(
                array(
                    'id' => uniqid(),
                    'user_id' => $user_id,
                    'title' => '公开相册',
                    'public_flag' => 1,
                    'default_flag' => 0,
                    'time' => time()
                )
        );
    }

    public static function saveCatPic($cat_id, $pic_id) {
        $insert_sql = "insert into cat_pic(cat_id,pic_id) values(:cat_id,:pic_id)";
        $return = Yii::app()->db->createCommand($insert_sql)->execute(
                array(
                    'cat_id' => $cat_id,
                    'pic_id' => $pic_id,
                )
        );
        return $return == 1;
    }

    public static function savePic($url, $userId) {
        $id = uniqid();
        $cat_id = Yii::app()->db->createCommand("SELECT id FROM category WHERE user_id=:user_id  AND default_flag=1")->queryScalar(array('user_id' => $userId));
        $insert_sql = "insert into picture(id,user_id,url,created_time) values(:id,:user_id,:url,:time)";
        $return = Yii::app()->db->createCommand($insert_sql)->execute(
                array(
                    'id' => $id,
                    'user_id' => $userId,
                    'url' => $url,
                    'time' => time()
                )
        );
        if ($return == 1) {
            return self::saveCatPic($cat_id, $id);
        }
        return false;
    }

}
