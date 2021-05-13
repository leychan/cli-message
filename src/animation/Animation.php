<?php


namespace cliMessage\animation;


use cliMessage\utils\Helper;

abstract class Animation
{
    public AnimationObject $object;

    public int $frequency = 0;

    public array $fonts;

    const APPEND_LENGTH = 3;

    public function __construct(AnimationObject $object)
    {
        $this->object = $object;
    }

    /**
     * @desc 动画
     * @user lei
     * @date 2021/5/7
     * @return mixed
     */
    abstract function animate();


    /**
     * @desc 清除上次的标准输出
     * @user lei
     * @date 2021/5/8
     */
    public function clear()
    {
        $this->object->frequency = $this->frequency ?: $this->object->frequency;
        usleep($this->object->frequency);
        $dot_lines = $this->object->lines * $this->object->height;
        system("tput cuu {$dot_lines}");
    }

    /**
     * @desc 对每个字的数据点阵进行边框扩充,以实现动画
     * @user lei
     * @date 2021/5/8
     * @return array
     */
    public function appendFontEdge()
    {
        $this->splitDotArrayToFont();
        foreach ($this->fonts as $font) {
            $tmp[] = $this->appendArrayWithFont($font);
        }
        $this->object->dot_array = $tmp;
        $this->resetHeight();
    }

    public function splitDotArrayToFont() {
        $origin_array = $this->object->dot_array;
        foreach ($origin_array as $v) {
            for ($i = 0; $i < $this->object->per_line_quantity; $i++) { //每个字
                $tmp = []; //一个字的点阵数据
                for ($h = 0; $h < $this->object->height; $h++) {
                    for ($j = 0; $j < $this->object->height; $j++) {
                        $tmp[$h][$j] = $v[$h][$i * $this->object->height + $j] ?? $this->object->fill_icon;
                    }
                }
                $this->fonts[] = $tmp;
            }
        }
        if (!$this->object->with_white_font) {
            $this->fonts = array_slice($this->fonts, 0, $this->object->font_count);
        }
    }

    /**
     * @desc 初始化空白数据点阵数组
     * @user lei
     * @date 2021/5/8
     * @param int $length
     * @param string $fill
     * @return array
     */
    public function initFontFilledArray(int $length, string $fill): array
    {
        $filled_array = [];
        for ($i = 0; $i < $length; $i++) {
            $filled_array[] = array_fill(0, $length, $fill);
        }

        return $filled_array;
    }

    /**
     * @desc 对空白填充数组进行数据填充(将数据填充到扩充后的数组中)
     * @user chenlei11
     * @date 2021/5/8
     * @param array $font_array
     * @return array
     */
    public function appendArrayWithFont(array $font_array)
    {
        $length = self::APPEND_LENGTH * 2 + $this->object->height;
        $init_array = $this->initFontFilledArray($length, $this->object->fill_icon);
        for ($i = 0; $i < $this->object->height; $i++) {
            for ($j = 0; $j < $this->object->height; $j++) {

                if ($font_array[$i][$j] == $this->object->center_icon) {
                    $init_array[$i + self::APPEND_LENGTH][$j + self::APPEND_LENGTH] = $this->object->center_icon;
                }
            }
        }
        return $init_array;
    }

    private function resetHeight() {
        $this->object->height = count($this->object->dot_array[0]);
    }

    public function printFont($arr) {
        foreach ($arr as $v) {
            foreach ($v as $v1) {
                echo $v1;
            }
            Helper::printLine();
        }
        //exit;
    }
}