<?php


namespace cliMessage\animation;


class FlashAnimation extends Animation
{
    public function animate() {
        while (true) {
            for ($k = 0; $k < $this->object->lines; $k++) {
                $tmp = '';
                for ($i = 0; $i < count($this->object->dot_array[$k]); $i++) {
                    for ($j = 0; $j < count($this->object->dot_array[$k][0]); $j++) {
                        $tmp .= $this->object->dot_array[$k][$i][$j];
                    }
                    $tmp .= PHP_EOL;
                }
                echo $tmp;
            }
            $this->clear();

            for ($k = 0; $k < $this->object->lines; $k++) {
                $tmp = '';
                for ($i = 0; $i < count($this->object->dot_array[$k]); $i++) {
                    for ($j = 0; $j < count($this->object->dot_array[$k][0]); $j++) {
                        $tmp .= $this->object->dot_array[$k][$i][$j] == $this->object->center_icon ? $this->object->fill_icon : $this->object->center_icon;
                    }
                    $tmp .= PHP_EOL;
                }
                echo $tmp;
            }
            $this->clear();
        }
    }

}