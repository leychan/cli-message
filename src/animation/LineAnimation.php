<?php


namespace cliMessage\animation;


class LineAnimation extends Animation
{

    /**
     * @inheritDoc
     */
    function animate()
    {
        for ($k = 0; $k < $this->object->lines; $k++) {
            for ($i = 0; $i < count($this->object->dot_array[$k]); $i++) {
                $tmp = '';
                for ($j = 0; $j < count($this->object->dot_array[$k][0]); $j++) {
                    $tmp .= $this->object->dot_array[$k][$i][$j];
                }
                $tmp .= PHP_EOL;
                echo $tmp;
                usleep(intval($this->object->frequency / $this->object->height));
            }

        }
    }
}