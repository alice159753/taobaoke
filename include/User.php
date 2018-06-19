<?php

	include_once("Table.php");
    include_once(INCLUDE_DIR. "/OpenUser.php");
    include_once(INCLUDE_DIR ."/Password.php");
    include_once(INCLUDE_DIR ."/UserIntegral.php");
    
	class User extends Table
	{
	    function User($myMySQL, $table = "user")
	    {
	        $this->myMySQL = $myMySQL;
	        $this->table = DB_PRE.$table;
	    }

        function getSexMap()
        {
            return  array(0 => '未知', 1 => '男', 2=>'女');
        }

        function login($userRow)
        {
            $_SESSION['user_no'] = isset($userRow['no']) ? $userRow['no'] : 0;
            $_SESSION['user_nickname'] = isset($userRow['nickname']) ? $userRow['nickname'] : '';
            $_SESSION['unionid'] = isset($userRow['unionid']) ? $userRow['unionid'] : '';

            //记录登录时间
            $myUserLogin = new UserLogin($this->myMySQL);
            $myUserLogin->login($userRow);

            header('Location:index.php');
        }

        function setRank($userRow)
        {
            $add_time = $userRow['add_time'];

            $diff = (strtotime($add_time) - strtotime('now')) / 3600 / 24;

            $rank = '1';
            if( $diff <= 90 )
            {
                $rank = '1';
            }
            else if( $diff > 90 and $diff <= 180)
            {
                $rank = '2';
            }
            else if( $diff > 180 and $diff <= 270 )
            {
                $rank = '3';
            }
            else if( $diff > 270 and $diff <= 360)
            {
                $rank = '4';
            }
            else if( $diff > 360 and $diff <= 450)
            {
                $rank = '5';
            }
            else if( $diff > 450 and $diff <= 540)
            {
                $rank = '6';
            }
            else if( $diff > 540 and $diff <= 630)
            {
                $rank = '7';
            }
            else if( $diff > 630 and $diff <= 720)
            {
                $rank = '8';
            }
            else if( $diff > 720 and $diff <= 810)
            {
                $rank = '9';
            }

            $dataArray = array();
            $dataArray['rank'] = $rank;

            $this->update($dataArray, "no = ".$userRow['no']);
        }

        function register($user, $login_type = 'weixin')
        {
            $userArray = array();
            $userArray['unionid']    = isset($user['unionid']) ? $user['unionid'] : '';
            $userArray['openid']     = isset($user['openid']) ? $user['openid'] : '';
            $userArray['nickname']   = $user['nickname'];
            $userArray['sex']        = $user['sex'];
            $userArray['headimgurl'] = $user['headimgurl'];
            $userArray['reg_ip']     = $_SERVER['REMOTE_ADDR'];
            $userArray['add_time']   = 'now()';

            $this->addRow($userArray);
        
            $user_no = $this->getInsertID();

            $myOpenUser = new OpenUser($this->myMySQL);

            $dataArray = array();
            $dataArray['user_no']    = $user_no;
            $dataArray['unionid']    = isset($user['unionid']) ? $user['unionid'] : '';
            $dataArray['openid']     = $user['openid'];
            $dataArray['nickname']   = $user['nickname'];
            $dataArray['sex']        = $user['sex'];
            $dataArray['language']   = $user['language'];
            $dataArray['city']       = $user['city'];
            $dataArray['province']   = $user['province'];
            $dataArray['country']    = $user['country'];
            $dataArray['headimgurl'] = $user['headimgurl'];
            $dataArray['privilege']  = json_encode($user['privilege']);
            $dataArray['login_type'] = $login_type;
            $dataArray['add_time'] = 'now()';

            $myOpenUser->addRow($dataArray);

            $userArray['no'] = $user_no;

            $myUserIntegral = new UserIntegral($this->myMySQL);

            //赠送用户积分
            $dataArray = array();
            $dataArray['user_no']  = $user_no;
            $dataArray['title']    = '注册赠送88积分';
            $dataArray['note']     = '注册赠送88积分';
            $dataArray['integral'] = 88;
            $dataArray['add_time'] = 'now()';

            $myUserIntegral->addRow($dataArray);

            return $userArray;
        }

        //小程序注册
        function register2($user, $login_type = 'xiaochengxu')
        {
            $userArray = array();
            $userArray['unionid']    = isset($user['unionid']) ? $user['unionid'] : '';
            $userArray['openid']     = isset($user['openid']) ? $user['openid'] : '';
            $userArray['nickname']   = $user['nickName'];
            $userArray['sex']        = $user['gender'];
            $userArray['headimgurl'] = $user['avatarUrl'];
            $userArray['reg_ip']     = $_SERVER['REMOTE_ADDR'];
            $userArray['add_time']   = 'now()';

            $this->addRow($userArray);
        
            $user_no = $this->getInsertID();

            $myOpenUser = new OpenUser($this->myMySQL);

            $dataArray = array();
            $dataArray['user_no']    = $user_no;
            $dataArray['unionid']    = isset($user['unionid']) ? $user['unionid'] : '';
            $dataArray['openid']     = $user['openid'];
            $dataArray['nickname']   = $user['nickName'];
            $dataArray['sex']        = $user['gender'];
            $dataArray['language']   = $user['language'];
            $dataArray['city']       = $user['city'];
            $dataArray['province']   = $user['province'];
            $dataArray['country']    = $user['country'];
            $dataArray['headimgurl'] = $user['avatarUrl'];
            $dataArray['privilege']  = '';
            $dataArray['login_type'] = $login_type;
            $dataArray['add_time'] = 'now()';

            $myOpenUser->addRow($dataArray);

            $userArray['no'] = $user_no;

            $myUserIntegral = new UserIntegral($this->myMySQL);

            //赠送用户积分
            $dataArray = array();
            $dataArray['user_no']  = $user_no;
            $dataArray['title']    = '注册赠送88积分';
            $dataArray['note']     = '注册赠送88积分';
            $dataArray['integral'] = 88;
            $dataArray['add_time'] = 'now()';

            $myUserIntegral->addRow($dataArray);

            return $userArray;
        }

        //手机号码注册
        function  registerByPhone($phone, $password)
        {
            $userArray = array();
            $userArray['phone']    = $phone;

            $salt = Password::getSlat(32);
            $userArray["password_salt"] = $salt;
            $userArray["password"] = Password::encrypt($password, $salt);

            $this->addRow($userArray);

            $user_no = $this->getInsertID();

            $userArray['no'] = $user_no;

            $myUserIntegral = new UserIntegral($this->myMySQL);

            //赠送用户积分
            $dataArray = array();
            $dataArray['user_no']  = $user_no;
            $dataArray['title']    = '注册赠送88积分';
            $dataArray['note']     = '注册赠送88积分';
            $dataArray['integral'] = 88;
            $dataArray['add_time'] = 'now()';

            $myUserIntegral->addRow($dataArray);

            return $userArray;
        }

        //手机号码绑定, 忘记密码
        function  boundByPhone($user_no, $phone, $password)
        {
            $userArray = array();
            $userArray['phone']    = $phone;

            $salt = Password::getSlat(32);
            $userArray["password_salt"] = $salt;
            $userArray["password"] = Password::encrypt($password, $salt);

            $this->update($userArray, "no = ". $user_no);

            $userArray['no'] = $user_no;

            return $userArray;
        }

        //忘记密码
        function  forgetPassword($phone, $password)
        {
            $userArray = array();

            $salt = Password::getSlat(32);
            $userArray["password_salt"] = $salt;
            $userArray["password"] = Password::encrypt($password, $salt);

            $this->update($userArray, "phone = '$phone'");

            $userArray['no'] = $user_no;

            return $userArray;
        }

        function getData($row)
        {
            $sexMap = $this->getSexMap();

            $myUserIntegral = new UserIntegral($this->myMySQL);

            $dataArray = array();
            $dataArray['{no}']              = $row['no'];
            $dataArray['{phone}']           = $row['phone'];
            $dataArray['{nickname}']        = $row['nickname'];
            $dataArray['{user_name}']       = $row['nickname'];
            $dataArray['{sex}']             = $row['sex'];
            $dataArray['{sex_title}']       = $sexMap[ $row['sex'] ];
            $dataArray['{grade}']           = $row['grade'];
            $dataArray['{rank}']            = $row['rank'];
            $dataArray['{headimgurl}']      = empty($row['headimgurl']) ? '/images/default_user.png' : $row['headimgurl'];
            $dataArray['{signature}']       = empty($row['signature']) ? '这个家伙很懒，什么都没留下' : $row['signature'];
            $dataArray['{rank}']            = $row['rank'];
            $dataArray['{wheel_num}']       = $row['wheel_num'];
            $dataArray['{unionid}']         = $row['unionid'];
            $dataArray['{reg_ip}']          = $row['reg_ip'];
            $dataArray['{last_login_time}'] = $row['last_login_time'];
            $dataArray['{add_time}']        = $row['add_time'];
            $dataArray['{update_time}']     = $row['update_time'];
            $dataArray['{email}']           = $row['email'];

            $userIntegralRow = $myUserIntegral->getRow('sum(integral) as sum_integral', "user_no = ". $row['no']);

            $dataArray['{total_integral}']  = empty($userIntegralRow['sum_integral']) ? 0 : $userIntegralRow['sum_integral'];

            return $dataArray;
        }

	}

?>