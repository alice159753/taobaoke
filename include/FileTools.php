<?php

class FileTools
{
    static function uploadOne($files, $name)
    {        
        $filename  = $files[$name]['name'];
        $tmp_name  = $files[$name]['tmp_name'];
        $file_size = $files[$name]['size'];

        $ymd = date("Ymd");
        $save_path = DATA_DIR."/". $ymd ."/";
        $save_url  = IMAGE_DIR ."/". $ymd ."/";

        if (!file_exists($save_path)) 
        {
            mkdir($save_path, 0700, true);
        }

        if ( is_writable($save_path) === false ) 
        {
            return array('status' => 'error', 'message' => '上传失败，上传没有写权限');
        }

        $fileNameArray = explode(".", $filename);
        $file_ext = array_pop($fileNameArray);
        $file_ext = trim($file_ext);
        $file_ext = strtolower($file_ext);

        if ( is_uploaded_file($tmp_name) === false ) 
        {
            return array('status' => 'error', 'message' => '上传失败1');
        }

        $new_file_name = date("YmdHis") .'_'. uniqid() . '_' . rand(10000, 99999) . '.' . $file_ext;

        $file_path = $save_path . $new_file_name;

        if ( move_uploaded_file($tmp_name, $file_path) === false ) 
        {
            return array('status' => 'error', 'message' => '上传失败2');
        }

        chmod($file_path, 0644);

        return  array('status' => 'success', 'message' => '', 'filename' => $save_url . $new_file_name, 'file' => $new_file_name);
    }

    static function uploadList($files, $name)
    {
        $nameList    = $files[$name]['name'];
        $typeList    = $files[$name]['type'];
        $tmpNameList = $files[$name]['tmp_name'];
        $sizeList    = $files[$name]['size'];

        $ymd = date("Ymd");
        $save_path = DATA_DIR."/". $ymd ."/";
        $save_url  = IMAGE_DIR ."/". $ymd ."/";

        if (!file_exists($save_path)) 
        {
            mkdir($save_path, 0700, true);
        }

        if ( is_writable($save_path) === false ) 
        {
            return array('status' => 'error', 'message' => '上传失败，上传没有写权限');
        }

        $newFilenameList = array();

        for($i = 0; isset($nameList[$i]); $i++)
        {
            $filename  = $nameList[$i];
            $tmp_name  = $tmpNameList[$i];
            $file_size = $sizeList[$i];

            $fileNameArray = explode(".", $filename);
            $file_ext = array_pop($fileNameArray);
            $file_ext = trim($file_ext);
            $file_ext = strtolower($file_ext);

            if ( is_uploaded_file($tmp_name) === false ) 
            {
                return array('status' => 'error', 'message' => '上传失败');
            }

            $new_file_name = date("YmdHis") .'_'. uniqid() . '_' . rand(10000, 99999) . '.' . $file_ext;

            $file_path = $save_path . $new_file_name;

            if ( move_uploaded_file($tmp_name, $file_path) === false ) 
            {
                return array('status' => 'error', 'message' => '上传失败');
            }

            chmod($file_path, 0644);

            $newFilenameList[]  = $save_url . $new_file_name;
        }

        return  array('status' => 'success', 'message' => '', 'filename' => $newFilenameList);

    }

    static function uploadToOss($files, $name)
    {
        //return FileTools::uploadOne($files, $name);

        // include_once(INCLUDE_DIR.'/autoload.php');
        // use OSS\OssClient;
        // use OSS\Core\OssException;

        $filename  = $files[$name]['name'];
        $tmp_name  = $files[$name]['tmp_name'];
        $file_size = $files[$name]['size'];

        $save_path = INCLUDE_DIR;

        if (!file_exists($save_path)) 
        {
            mkdir($save_path, 0700, true);
        }

        if ( is_writable($save_path) === false ) 
        {
            return array('status' => 'error', 'message' => '上传失败，上传没有写权限');
        }

        $fileNameArray = explode(".", $filename);
        $file_ext = array_pop($fileNameArray);
        $file_ext = trim($file_ext);
        $file_ext = strtolower($file_ext);

        if ( is_uploaded_file($tmp_name) === false ) 
        {
            return array('status' => 'error', 'message' => '上传失败');
        }

        $new_file_name = date("YmdHis") .'_'. uniqid() . '_' . rand(10000, 99999) . '.' . $file_ext;

        $file_path = $save_path."/".$new_file_name;

        if ( move_uploaded_file($tmp_name, $file_path) === false ) 
        {
            return array('status' => 'error', 'message' => '上传失败');
        }

        $myOssClient = new OssClient(OSS_ACCESS_ID, OSS_ACCESS_KEY, OSS_ENDPOINT);

        $oss_filename = $myOssClient->uploadFile(OSS_BUCKET, $new_file_name,  $file_path);

        return  array('status' => 'success', 'message' => '', 'filename' => $oss_filename['info']['url']);
    }

    static function uploadListToOSS($files, $name)
    {
        //return FileTools::uploadList($files, $name);

        // include_once(INCLUDE_DIR.'/autoload.php');
        // use OSS\OssClient;
        // use OSS\Core\OssException;

        $nameList    = $files[$name]['name'];
        $typeList    = $files[$name]['type'];
        $tmpNameList = $files[$name]['tmp_name'];
        $sizeList    = $files[$name]['size'];

        $ymd = date("Ymd");
        $save_path = DATA_DIR."/". $ymd ."/";
        $save_url  = IMAGE_DIR ."/". $ymd ."/";

        if (!file_exists($save_path)) 
        {
            mkdir($save_path, 0700, true);
        }

        if ( is_writable($save_path) === false ) 
        {
            return array('status' => 'error', 'message' => '上传失败，上传没有写权限');
        }

        $newFilenameList = array();

        for($i = 0; isset($nameList[$i]); $i++)
        {
            $filename  = $nameList[$i];
            $tmp_name  = $tmpNameList[$i];
            $file_size = $sizeList[$i];

            $fileNameArray = explode(".", $filename);
            $file_ext = array_pop($fileNameArray);
            $file_ext = trim($file_ext);
            $file_ext = strtolower($file_ext);

            if ( is_uploaded_file($tmp_name) === false ) 
            {
                return array('status' => 'error', 'message' => '上传失败');
            }

            $new_file_name = date("YmdHis") .'_'. uniqid() . '_' . rand(10000, 99999) . '.' . $file_ext;

            $file_path = $save_path . $new_file_name;

            if ( move_uploaded_file($tmp_name, $file_path) === false ) 
            {
                return array('status' => 'error', 'message' => '上传失败');
            }

            chmod($file_path, 0644);

            $myOssClient = new OssClient(OSS_ACCESS_ID, OSS_ACCESS_KEY, OSS_ENDPOINT);

            $oss_filename = $myOssClient->uploadFile(OSS_BUCKET, $new_file_name,  $file_path);

            $newFilenameList[] = $oss_filename['info']['url'];
        }

        return  array('status' => 'success', 'message' => '', 'filename' => $newFilenameList);

    }

}



?>