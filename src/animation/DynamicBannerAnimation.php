<?php


namespace cliMessage\animation;


class DynamicBannerAnimation extends Animation
{

    public function __construct(AnimationObject $object)
    {
        parent::__construct($object);
        $this->frequency = $this->object->frequency / $this->object->height;
    }

    function animate()
    {
        $this->splitDotArrayToFont();
        //将字体拼接成一行
        $new_dot_array = [];
        for ($i = 0; $i < $this->object->font_count; $i++) {
            for ($j = 0; $j < $this->object->height; $j++) {
                for ($k = 0; $k < $this->object->height; $k++) {
                    $new_dot_array[$k][$j + $i * $this->object->height] = $this->fonts[$i][$k][$j];
                }
            }
        }
        while (true) {
            for ($i = 0; $i < ($this->object->font_count) * $this->object->height; $i++) {
                for ($j = 0; $j < $this->object->height; $j++) {
                    for ($k = 0; $k < $this->object->height; $k++) {
                        echo $new_dot_array[$j][$k + $i] ?? $this->object->fill_icon;
                    }
                    echo PHP_EOL;
                }
                $this->clear();
            }
        }
    }
}