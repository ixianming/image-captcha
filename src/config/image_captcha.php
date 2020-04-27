<?php

return [

    'default' => 'defaultConfig',

    'config' => array(

        'defaultConfig' => array(

            'imgWidth' => 150, //验证码图片宽度。（整型）

            'imgHeight' => 60, //验证码图片高度。（整型）

            'backgroundColor' => '#FFFFFF', //验证码图片底色，十六进制颜色

            'length' => 4, //验证码字符个数。（整型）

            'fontFile' => dirname(__DIR__) . '/font/zcool_wenyi.ttf', //生成验证码所用的字体文件

            //验证码字体尺寸区间
            'fontProportion' => array(
                'min' => 0.45, //与最大尺寸限制相比的最小比例
                'max' => 0.8   //与最大尺寸限制相比的最大比例
            ),

            //验证码 360° 旋转角度区间
            'fontAngle' => array(
                'min' => -30, //最小限制
                'max' => 20   //最大限制
            ),

            //验证码的 RGB 颜色区间
            'captchaRGBScope' => array(
                'min' => 0,
                'max' => 200
            ),

            'interferenceStrNum' => 80, //干扰字符的数量

            //干扰字符字体尺寸区间
            'interferenceFontProportion' => array(
                'min' => 0.05, //与最大尺寸限制相比的最小比例
                'max' => 0.3   //与最大尺寸限制相比的最大比例，不得超过验证码字体尺寸区间的最小比例
            ),

            //干扰字符的 RGB 颜色区间
            'interferenceStrRGBScope' => array(
                'min' => 150,
                'max' => 245
            ),

            'interferenceLineNum' => 3, //干扰线的数量

            //干扰线的 RGB 颜色区间
            'interferenceLineRGBScope' => array(
                'min' => 70,
                'max' => 245
            ),

            //验证码字符库。（数组）
            'charLib' => array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'),
        ),

//        //自定义验证码配置，键名为自定义配置的名称
//        'customConfigName' => array(
//            // ..
//            //复制 defaultConfig 数组下的全部键值对，修改为自己的配置
//        ),

    ),

];
