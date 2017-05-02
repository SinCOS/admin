<?php

namespace  App\Libs;


class VerifyCode {
    public function __invoke($request, $response, $args){
        ob_clean();
        $imagewidth = 130;
        $imageheight = 45;
        $authimage = imagecreate($imagewidth, $imageheight);
        $black = ImageColorAllocate($authimage, 0, 0, 0);
        $white = ImageColorAllocate($authimage, 255, 255, 255);
        $red = ImageColorAllocate($authimage, 255, 0, 0);
        $gray = ImageColorAllocate($authimage, 200, 200, 200);
        $authcode='';
        //背景颜色为灰色
        imagefill($authimage, 0, 0, $gray);

        //随机的生成一些干扰像素
        for ($i = 0; $i < 400; $i++) {
            $randcolor = ImageColorallocate($authimage, rand(10, 255), rand(10, 255), rand(10, 255));
            imagesetpixel($authimage, rand() % $imagewidth, rand() % $imageheight, $randcolor);
        }

        //随机的画几条线段
        for ($i = 0; $i < 6; $i++) {
            imageline($authimage, rand() % $imagewidth, rand() % $imageheight,
                rand() % $imagewidth, rand() % $imageheight, $black);
        }

        //生成验证串
        $array = "0123456789abcdefghijklmnopqrstuvwxyz";
        for ($i = 0; $i < 4; $i++) {
            $authcode .= substr($array, rand(0, 35), 1);
        }
        @imagettftext($authimage, 30, rand(-5, 10), 20, 33, $red, APP_PATH .'/assert/font/SourceCodePro-Regular.ttf', $authcode);
        ImagePNG($authimage);
        ImageDestroy($authimage);
        $_SESSION['authcode'] = $authcode;
        return $response->withHeader("Content-type","image/PNG");
    }
}