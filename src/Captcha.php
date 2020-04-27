<?php

namespace Ixianming\ImageCaptcha;

use Intervention\Image\ImageManager;

class Captcha
{
    private $verificationCode;
    private $imageCaptcha;
    private $imageEncode;

    private $config;

    public function __construct(string $configName = null)
    {
        $config = include __DIR__ . '/config/image_captcha.php';

        if ($configName == null) {
            $configName = $config['default'] ?? null;
        }

        if (!isset($config['config'][$configName])) {
            throw new \InvalidArgumentException('名称为 `' . $configName . '` 的验证码配置项尚未定义');
        }

        $this->config = $config['config'][$configName];

        $this->code();
    }

    /**
     * 生成验证码字符串
     *
     * @return mixed
     */
    public function code()
    {
        if ($this->verificationCode != null) {
            return $this->verificationCode;
        }

        $charLibCount = count($this->config['charLib']) - 1;
        for ($i = 0; $i < $this->config['length']; $i++) {
            $randKey = random_int(0, $charLibCount);
            $this->verificationCode .= $this->config['charLib'][$randKey];
        }

        return $this->verificationCode;
    }

    /**
     * 销毁当前验证码
     *
     * @return bool
     */
    public function destroy()
    {
        $this->verificationCode = null;
        $this->imageCaptcha = null;
        $this->imageEncode = null;
        return true;
    }

    /**
     * 创建验证码图片
     *
     * @return \Intervention\Image\Image
     * @throws FileNotFoundException
     */
    private function build()
    {
        if (empty($this->verificationCode)) {
            throw new \InvalidArgumentException('验证码尚未生成，无法创建图片！');
        }

        //检查字体文件是否存在
        if (!file_exists($this->config['fontFile'])) {
            throw new \InvalidArgumentException('字体文件 `' . $this->config['fontFile'] . '` 不存在');
        }

        //创建画布
        $this->imageCaptcha = (new ImageManager())->canvas($this->config['imgWidth'], $this->config['imgHeight'], $this->config['backgroundColor']);

        //最大字体尺寸限制
        $maxFontSize = $this->config['imgWidth'] <= $this->config['imgHeight'] ? $this->config['imgWidth'] : $this->config['imgHeight'];

        //画干扰字符
        $disturbCharLib = array('@', '#', '$', '%', '^', '&', '*', '!', '-', '=', '+', '?', '>', '<', '(', ')', '~');
        $charLib = array_merge($this->config['charLib'], $disturbCharLib);
        $charLibCount = count($charLib) - 1;
        for ($i = 0; $i < $this->config['interferenceStrNum']; $i++) {
            $interferenceStr = $charLib[random_int(0, $charLibCount)];
            $this->imageCaptcha->text($interferenceStr, random_int(0, $this->config['imgWidth']), random_int(0, $this->config['imgHeight']), function ($font) use ($maxFontSize) {
                $font->file($this->config['fontFile']);
                $font->size(random_int((int)($maxFontSize * $this->config['interferenceFontProportion']['min']), (int)($maxFontSize * $this->config['interferenceFontProportion']['max'])));
                $font->color(
                    array(
                        random_int($this->config['interferenceStrRGBScope']['min'], $this->config['interferenceStrRGBScope']['max']),
                        random_int($this->config['interferenceStrRGBScope']['min'], $this->config['interferenceStrRGBScope']['max']),
                        random_int($this->config['interferenceStrRGBScope']['min'], $this->config['interferenceStrRGBScope']['max'])
                    )
                );
                $font->align('left');
                $font->valign('top');
                $font->angle(random_int(0, 360));
            });
        }

        // 画干扰线
        for ($i = 0; $i < $this->config['interferenceLineNum']; $i++) {
            $this->imageCaptcha->ellipse(random_int((int)($this->config['imgWidth'] / 3), $this->config['imgWidth'] * 2), random_int((int)($this->config['imgHeight'] / 3), $this->config['imgHeight'] * 2), random_int(0, $this->config['imgWidth']), random_int(0, $this->config['imgHeight']), function ($draw) {
                $draw->border(1, array(
                    random_int($this->config['interferenceLineRGBScope']['min'], $this->config['interferenceLineRGBScope']['max']),
                    random_int($this->config['interferenceLineRGBScope']['min'], $this->config['interferenceLineRGBScope']['max']),
                    random_int($this->config['interferenceLineRGBScope']['min'], $this->config['interferenceLineRGBScope']['max'])
                ));
            });
        }

        //画验证码
        $len = strlen($this->verificationCode);

        $safeMargin = $maxFontSize * 0.1; //安全边距
        $xSafeMin = $safeMargin; //起始的 x 轴坐标不小于安全边距，以便为字体旋转留出空间

        //字体尺寸限制
        $fontMinSize = $maxFontSize * $this->config['fontProportion']['min'];
        $fontMaxSize = $maxFontSize * $this->config['fontProportion']['max'];

        for ($i = 0; $i < $len; $i++) {
            $size = random_int($fontMinSize, $fontMaxSize);

            $fontBox = imagettfbbox($size, 0, $this->config['fontFile'], $this->verificationCode[$i]); //字符串文本框
            $fontWidth = $fontBox[2] - $fontBox[0]; //字符串文本框宽度
            $fontHeight = $fontBox[3] - $fontBox[5]; //字符串文本框高度
            $fontHalfWidth = $fontWidth / 2;

            //本轮 x 轴最大安全限制，随机的 x 坐标不能超过次限制
            $xSafeMax = $this->config['imgWidth'] - $fontHalfWidth - $safeMargin;

            $xMin = $xSafeMin + $fontHalfWidth;
            $xMin = $xMin < $xSafeMax ? $xMin : $xSafeMax;

            $xMax = $safeMargin + (($i + 1) * (($this->config['imgWidth'] - $safeMargin * 2) / $len)) - $fontHalfWidth;
            $xMax = $xMax < $xSafeMax ? $xMax : $xSafeMax;
            $xMin = $xMin < $xMax ? $xMin : $xMax;

            $x = random_int((int)$xMin, (int)$xMax);
            $x = $x > $xSafeMax ? $xSafeMax : $x;

            $xSafeMin = $x + $fontHalfWidth + $safeMargin;

            $yMin = $fontHeight + $safeMargin;
            $yMax = $this->config['imgHeight'] - $safeMargin;
            $yMax = $yMin < $yMax ? $yMax : $yMin;
            $y = random_int($yMin, $yMax);

            $this->imageCaptcha->text($this->verificationCode[$i], $x, $y, function ($font) use ($size) {
                $font->file($this->config['fontFile']);
                $font->size($size);
                //为了区别于背景，颜色不超过200，背景和干扰的不小于200
                $font->color(
                    array(
                        random_int($this->config['captchaRGBScope']['min'], $this->config['captchaRGBScope']['max']),
                        random_int($this->config['captchaRGBScope']['min'], $this->config['captchaRGBScope']['max']),
                        random_int($this->config['captchaRGBScope']['min'], $this->config['captchaRGBScope']['max'])
                    )
                );
                $font->align('center');
                $font->valign('bottom');
                $font->angle(random_int($this->config['fontAngle']['min'], $this->config['fontAngle']['max']));
            });
        }

        return $this->imageCaptcha;
    }

    /**
     * 返回图片的 Base64 编码数据
     *
     * @param int $quality
     * @return \Intervention\Image\Image
     * @throws FileNotFoundException
     */
    public function base64(int $quality = 90)
    {
        if (isset($this->imageEncode['data-url_' . $quality])) {
            return $this->imageEncode['data-url_' . $quality];
        }

        return $this->build()->encode('data-url', $quality);
    }

    /**
     * 返回图片的编码数据
     *
     * @param string $imgType
     * @param int $quality
     * @return \Intervention\Image\Image
     * @throws FileNotFoundException
     */
    public function encode(string $imgType = 'base64', int $quality = 90)
    {
        if ($imgType == 'base64') {
            $imgType = 'data-url';
        }

        if (isset($this->imageEncode[$imgType . '_' . $quality])) {
            return $this->imageEncode[$imgType . '_' . $quality];
        }

        $this->imageEncode[$imgType . '_' . $quality] = $this->build()->encode($imgType, $quality);

        return $this->imageEncode[$imgType . '_' . $quality];
    }

    /**
     * 返回验证码图片的 HTTP Response
     *
     * @param string $imgType
     * @param int $quality
     * @return \Intervention\Image\Image|mixed
     * @throws FileNotFoundException
     */
    public function response(string $imgType = 'jpg', int $quality = 90)
    {
        if ($imgType == 'base64') {
            return $this->build()->encode('data-url', $quality);
        }

        return $this->build()->response($imgType, $quality);
    }

}


