<?php

    $myMySQL = new MySQL();
    $myMySQL->connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    $mySystemConfig = new SystemConfig($myMySQL);

    $myTemplateCommon = new Template(TEMPLATE_DIR ."/common.html");

    //head
    $systemConfigRow = $mySystemConfig->getRow("*", "1 = 1 ORDER BY no DESC LIMIT 1");

    $dataArray = array();
    $dataArray['{site_title}']       = $systemConfigRow['site_title'];
    $dataArray['{site_name}']        = $systemConfigRow['site_name'];
    $dataArray['{site_description}'] = $systemConfigRow['site_description'];
    $dataArray['{site_keyword}']     = $systemConfigRow['site_keyword'];
    $dataArray['{site_logo}']        = $systemConfigRow['site_logo'];
    $dataArray['{phone}']            = $systemConfigRow['phone'];
    $dataArray['{email}']            = $systemConfigRow['email'];
    $dataArray['{skype}']            = $systemConfigRow['skype'];
    $dataArray['{wechat}']           = $systemConfigRow['wechat'];
    $dataArray['{qq}']               = $systemConfigRow['qq'];
    $dataArray['{comany_address}']   = $systemConfigRow['comany_address'];
    $dataArray['{time}']             = microtime(true);

    $myTemplateCommon->setReplace("head", $dataArray);
    $myTemplateCommon->process();
    
    $myTemplate->setReplaceSegment("head", $myTemplateCommon->getContent());

    //nav
    $myTemplateCommon = new Template(TEMPLATE_DIR ."/common.html");

    $basename = basename($_SERVER['SCRIPT_NAME']);
    
    $dataArray = array();

    $is_active = 0;
    if( strstr($basename, 'index') )
    {
        $dataArray['{index}'] = 'active';
        $is_active = 1;
    }
    elseif( strstr($basename, 'product_lists') )
    {
        $dataArray['{product_lists}'] = 'active';
        $is_active = 1;
    }
    elseif( strstr($basename, 'taoqianggou') )
    {
        $dataArray['{taoqianggou_lists}'] = 'active';
        $is_active = 1;
    }
    else if( strstr($basename, 'subject_') || $basename == 'subject.php' )
    {
        $dataArray['{subject}'] = 'active';
        $is_active = 1;
    }
    elseif( strstr($basename, 'user_') || $basename == 'user.php' )
    {
        $dataArray['{user}'] = 'active';
        $is_active = 1;
    }

    if( !$is_active )
    {
        $dataArray['{index}'] = 'active';
    }

    $myTemplateCommon->setReplace("nav", $dataArray);
    $myTemplateCommon->process();
    $myTemplate->setReplaceSegment("nav", $myTemplateCommon->getContent());

    //footer
    $myTemplateCommon = new Template(TEMPLATE_DIR ."/common.html");

    //微信分享
    $myWechat = new Wechat();
    $weixinShare = $myWechat->getSignPackage();

    $weixinShare['{shareData_imgUrl}'] = '';
    $weixinShare['{shareData_sendFriendLink}'] = Tools::getPageUrl();
    $weixinShare['{shareData_tTitle}'] = '';
    $weixinShare['{shareData_tContent}'] = '';

    $myTemplateCommon->setReplace("footer", $weixinShare);
    $myTemplateCommon->process();

    $myTemplate->setReplaceSegment("footer", $myTemplateCommon->getContent());





?>