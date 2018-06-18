<?php

    $basename = basename($_SERVER['SCRIPT_NAME']);
    $basename = str_replace(array(".php", "_add", "_add_make", "_modify", "_modify_make", "_delete","_delete_more"), "", $basename);

    $converName = array('category' => 'product','slideshow' => 'product','taoqianggou'=>'product',
                        'suggest' => 'user','user_integral' => 'user', 'pay_order' => 'user', 'search' => 'user', 'open_user' => 'user',
                        'system_config' => 'baseconfig','admin' => 'baseconfig', 'role' => 'baseconfig', 


                     );

    $come_from = isset($_REQUEST["come_from"]) ? $_REQUEST["come_from"] : '';
    $come_from = empty($come_from) && isset($converName[$basename]) ? $converName[$basename] : $come_from;
    $come_from = empty($come_from) ? $basename : $come_from;

    //navigation
    $myTemplateNavigation = new Template(TEMPLATE_DIR ."/navigation.html");
    $myTemplateNavigation->setClearTag(true);
    $myTemplateNavigation->setNothing("nav");
    $dataArray = array();
    $dataArray['{account}'] = $_SESSION['account'];
    $myTemplateNavigation->setReplace("admin", $dataArray, 2);
    $myTemplateNavigation->process();
    $myTemplate->setReplaceSegment("nav", $myTemplateNavigation->getContent());

    //menu
    $myTemplateMenu = new Template(TEMPLATE_DIR ."/menu.html");
    $myTemplateMenu->setClearTag(true);
    $myTemplateMenu->setAutoBrackets(true);
    $dataArray = array();
    $dataArray[$come_from."_in"] = 'am-in';
    $dataArray[$come_from."_height"] = '';

    $myTemplateMenu->setReplace("menu", $dataArray);

    //normal
    $normalArray = array("banner");

    foreach ($normalArray as $index => $item) 
    {
        if( strstr($adminRow['permission'], "#".$item."#") )
        {
            $myTemplateMenu->setNothing($item, 2);
        }
    }

    $myTemplateMenu->process();
    $myTemplate->setReplaceSegment("menu", $myTemplateMenu->getContent());

    //footer
    $myTemplateFooter = new Template(TEMPLATE_DIR ."/footer.html");
    $myTemplateFooter->setClearTag(true);
    $myTemplateFooter->setAutoBrackets(true);
    $myTemplateFooter->setNothing("footer");

    $myTemplateFooter->process();
    $myTemplate->setReplaceSegment("footer", $myTemplateFooter->getContent());

?>    


