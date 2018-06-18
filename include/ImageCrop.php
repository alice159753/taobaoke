<?php
/**
* 
*/
class ImageCrop 
{
    private $max_size = 2097152; //1M
    private $allowTypeList = [
        'image/gif',
        'image/jpg',
        'image/jpeg',
        'image/png',
        'image/x-png',
        'image/pjpeg',
        'jpg',
        'gif',
        'png',
        'jpeg'
    ];

    private $save_dir = '';
    private $error_id = 0;
    private $error_msg = '';

    function __construct()
    {
        # code...
    }

    public function checkType($type)
    {
        if (!in_array($type, $this->allowTypeList)) {
            $this->setError(1);
            return FALSE;
        }

        return TRUE;
    }

    public function checkSize($size)
    {
        if ($size > $this->max_size) {
            $this->setError(2);
            return FALSE;
        }

        return TRUE;
    }

    public function setError($error_id)
    {
        $this->error_id = $error_id;

        switch ($error_id) {
            case 1:
                $this->error_msg = '文件类型错误';
                break;
            case 2:
                $this->error_msg = '文件大小错误';
                break;
            case 3:
                $this->error_msg = '未能获取文件信息';
                break;
            default:
                # code...
                break;
        }

        return TRUE;
    }

    public function getError()
    {
        return [
            'error_id' => $this->error_id,
            'error_msg' => $this->error_msg
        ];
    }

    public function getImgInfo($img)
    {
        $infoList = getimagesize($img);

        if (!$infoList) return FALSE; 

        $type = strtolower(substr(image_type_to_extension($infoList[2]), 1));
        $size = filesize($img);
        $resultArray = [
            "width" => $infoList[0],
            "height"=> $infoList[1],
            "type"  => $type,
            "size"  => $size,
            "mime"  => $infoList['mime'],
            "type_simple"  => $infoList['2'],
        ];

        return $resultArray;
    }

    public function thumbnailImg($img, $cover = FALSE, $max_w = 400, $max_h = 400)
    {
        $infoList = $this->getImgInfo($img);

        if (!$infoList) {
            $this->setError(3);
            return FALSE;
        }

        if (!$this->checkType($infoList['type'])) return FALSE; 
        if (!$this->checkSize($infoList['size'])) return FALSE; 

        $src_w = $infoList['width'];
        $src_h = $infoList['height'];
        $src_type = strtolower($infoList['type']);

        $scale = min($max_w / $src_w, $max_h / $src_h); // 计算缩放比例
        // 缩略图尺寸
        $new_w = $src_w;
        $new_h = $src_h;
        //原图过大进行缩放
        if ($scale < 1) {
            // 缩略图尺寸
            $new_w  = (int)($src_w * $scale);
            $new_h = (int)($src_h * $scale);
        }

        // 载入原图
        $createFun = 'imagecreatefrom'. ($src_type == 'jpg' ? 'jpeg' : $src_type);
        $src_img = $createFun($img);

        //创建缩略图
        if(function_exists('imagecreatetruecolor'))
            $thumb_img = imagecreatetruecolor($new_w, $new_h);
        else
            $thumb_img = imagecreate($new_w, $new_h);

        // 复制图片
        if(function_exists("imagecopyresampled"))
            imagecopyresampled($thumb_img, $src_img, 0, 0, 0, 0, $new_w, $new_h, $src_w, $src_h);
        else
            imagecopyresized($thumb_img, $src_img, 0, 0, 0, 0, $new_w, $new_h, $src_w, $src_h);

        if('gif' == $src_type || 'png' == $src_type) {
            $bg_color = imagecolorallocate($thumb_img,  0,255,0);  //  指派一个绿色
            imagecolortransparent($thumb_img,$bg_color);  //  设置为透明色，若注释掉该行则输出绿色的图
        }

        // 对jpeg图形设置隔行扫描
        if('jpg'== $src_type || 'jpeg' == $src_type)    
            imageinterlace($thumb_img, TRUE);

        $imageFun = 'image'. ($src_type == 'jpg' ? 'jpeg' : $src_type); 

        if ($cover == TRUE) 
        {
            $target_file = $img;
        } 
        else 
        {
            $name = $this->getName($img);

            $target_file = dirname($img) .'/'. $name ."_$max_w*$max_h.". $src_type;
        }

        $imageFun($thumb_img, $target_file);

        imagedestroy($src_img);
        imagedestroy($thumb_img);

        return $target_file;
    }

    public function cropImg($img, $x, $y, $w, $h)
    {
        list($filename, $ext) = explode(".", $img);

        $infoList = $this->getImgInfo($img);

        if (!$infoList) {
            $this->setError(3);
            return FALSE;
        }

        if (!$this->checkType($infoList['type'])) return FALSE; 
        if (!$this->checkSize($infoList['size'])) return FALSE; 

        $src_w = $infoList['width'];
        $src_h = $infoList['height'];
        $src_type = strtolower($infoList['type']);

        // 载入原图
        $createFun = 'imagecreatefrom'. ($src_type == 'jpg' ? 'jpeg' : $src_type);
        $src_img = $createFun($img);

        $name = $this->getName($img);

        $imageFun = 'image'. ($src_type == 'jpg' ? 'jpeg' : $src_type); 

        $resultList = array();

        //3:4
        $widthHeight = array("72x96", "90x120", "99x132", "114x152", "177x236", "339x452", "375x500");

        //5:6
        $widthHeight = array("70x84", "90x108", "100x120", "115x138", "175x210", "340x408", "375x450");

        foreach ($widthHeight as $index => $width_height) 
        {
            list($width, $height) = explode("x", $width_height);

            $target_file  = dirname($img) .'/'. $name .'_'."$width".'x'."$height".'.'. $ext;

            $thumb_img = imagecreatetruecolor($width,$height);

            imagecopyresampled($thumb_img, $src_img, 0, 0, $x, $y, $width, $height, $w, $h);

            $imageFun($thumb_img, $target_file, 90);

            imagedestroy($thumb_img);

            $resultList[] = $target_file;
        }

        imagedestroy($src_img);

        return $resultList;
    }
   
    function imageZoom($img, $w, $h) 
    {
        list($filename, $ext) = explode(".", $img);

        $infoList = $this->getImgInfo($img);

        if (!$infoList) {
            $this->setError(3);
            return FALSE;
        }

        if (!$this->checkType($infoList['type'])) return FALSE; 
        if (!$this->checkSize($infoList['size'])) return FALSE; 

        $src_w   = $infoList['width'];
        $src_h   = $infoList['height'];
        $src_t   = strtolower($infoList['type']);
        $src_t_s = strtolower($infoList['type_simple']);
        $src_m   = $infoList['mime'];

        $src_img = imagecreatefromjpeg($img);

        // if (($w / $src_w) >($h / $src_h)) 
        // {
        //     $bili = $h / $src_h;
        // } else {
        //     $bili = $w / $src_h;
        // }

        // $dst_w = $src_w * $bili;
        // $dst_h = $src_h * $bili;

        $dst_w = $w;
        $dst_h = $h;

        $dst_img = imagecreatetruecolor($dst_w, $dst_h);
        imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $dst_w, $dst_h, $src_w, $src_h);
        header("content-type:{$src_m}");
        switch ($src_t_s) {
            case 1:
                $imgout = "imagegif";
                break;
            case 2:
                $imgout = "imagejpeg";
                break;
            case 3:
                $imgout = "imagepng";
                break;
            default:
                echo "The type was wrong!";
                break;
        }

        $name = $this->getName($img, $src_type);

        $target_file = dirname($img) .'/'. $name .'.'. $ext;

        $imgout($dst_img, $target_file);
        imagedestroy($dst_img);

        return $target_file;
    }

    function getName($img)
    {
        $name = basename($img);

        list($name, $temp) = explode(".", $name);
        $name = preg_replace("/\_[0-9]{1,}\*[0-9]{1,}/","", $name);
        $name = preg_replace("/\_[0-9]{1,}x[0-9]{1,}/","", $name);
        $name = preg_replace("/\_original/","", $name);
        $name = preg_replace("/\_upload/","", $name);

        return $name;
    }

}
?>