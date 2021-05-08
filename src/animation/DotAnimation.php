<?php


namespace cliMessage\animation;


use cliMessage\utils\Helper;

class DotAnimation extends Animation
{

    /**
     * @inheritDoc
     */
    function animate()
    {
        for ($k = 0; $k < $this->object->lines; $k++) {
            for ($i = 0; $i < count($this->object->dot_array[$k]); $i++) {
                for ($j = 0; $j < count($this->object->dot_array[$k][0]); $j++) {
                    echo $this->object->dot_array[$k][$i][$j];
                    usleep(intval($this->object->frequency / ($this->object->height<<2)));
                }
                Helper::printLine();
            }
        }
    }
}