<?php


namespace cliMessage\animation;


abstract class Animation
{
    public AnimationObject $object;

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


    public function clear()
    {
        usleep($this->object->frequency);
        $cmd = [];
        for ($i = 0; $i < $this->object->lines * $this->object->height; $i++) {
            $cmd[] = "tput cuu1";
            $cmd[] = "tput el";
        }
        $cmds = implode('&&', $cmd);
        system($cmds);
    }

    public function appendFontEdge()
    {
        $appended_array = [];
        $origin_array = $this->object->dot_array;
        foreach ($origin_array as $k => $v) {
            for ($i = 0; $i < $this->object->per_line_quantity; $i++) { //行数
                $tmp = []; //一个字的点阵数据
                for ($h = 0; $h < $this->object->height; $h++) {
                    for ($j = 0; $j < $this->object->height; $j++) {
                        $tmp[$h][$j] = $v[$i * $this->object->height + $h][$j];
                    }
                }
                $appended_array[$k][] = $this->appendArrayWithFont($tmp);
            }
        }
        return $appended_array;
    }

    public function initFontFilledArray(int $length, string $fill): array
    {
        $filled_array = [];
        for ($i = 0; $i < $length; $i++) {
            $filled_array[] = array_fill(0, $length, $fill);
        }

        return $filled_array;
    }

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

    public function printFont($arr) {
        foreach ($arr as $v) {
            foreach ($v as $v1) {
                echo $v1;
            }
            echo PHP_EOL;
        }
        exit;
    }
}