<?php

namespace cliMessage\animation;

class AnimationObject {

    /**
     * @var int 打印的行数
     */
   public int $lines;

    /**
     * @var int 动画频率
     */
   public int $frequency = 750000;

    /**
     * @var int 一个字体的数组高度
     */
   public int $height = 16;

    /**
     * @var array 点阵二进制数组
     */
   public array $dot_array;

    /**
     * @var int 每行展示的字数
     */
   public int $per_line_quantity;

   public string $center_icon;

   public string $fill_icon;
}