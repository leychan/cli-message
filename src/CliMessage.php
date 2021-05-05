<?php

namespace cliMessage;

class CliMessage
{
    const WIDTH = 16;
    const HEIGHT = 16;
    const PER_FONT_BINARY_LENGTH = 256;
    const PER_FONT_DOT_LENGTH = 32;
    const FONT_FILE = __DIR__ . '/font_file/HZK16';
    private $per_line_font_quantity = 4;
    private $lines = 0;
    private $message = '';
    private $gb2312_message = '';
    private $dot_message = '';
    private $inner_show = '■';
    private $outer_show = '♡';
    private $dot_array = [];
    private $print_line_frequency = 130000;
    private $message_array = [];

    /**
     * @desc 设置每一行的打印字的数量
     * @user lei
     * @date 2021/5/5
     * @param int $num
     */
    public function setPerLineFontQuantity(int $num)
    {
        $this->per_line_font_quantity = $num;
    }

    /**
     * @desc 设置要打印的内容
     * @user lei
     * @date 2021/5/5
     * @param string $message
     */
    public function setMessage(string $message)
    {
        $this->message = $message;
    }

    public function setInnerShow(string $inner_show) {
        $this->inner_show = $inner_show;
    }

    public function setOuterShow(string $outer_show) {
        $this->outer_show = $outer_show;
    }

    /**
     * @desc 读取点阵字库里面的数据
     * @user lei
     * @date 2021/5/5
     * @throws \Exception
     */
    private function readHZK()
    {
        $fp = fopen(self::FONT_FILE, 'r+');
        $gb2312_message_len = strlen($this->gb2312_message);
        for ($i = 0; $i < $gb2312_message_len; $i++) {
            // 当前字, 如果是汉字,则占两个字节(GB2312),如果非汉字,则占一个字节
            $tmp = $this->gb2312_message[$i];
            //转换成整形,以判断是不是汉字
            $binary_num = ord($tmp);
            if ($binary_num < 160) { //非汉字
                $location = ($binary_num + 156 - 1) * self::PER_FONT_DOT_LENGTH;
            } else { //汉字第一个字节是区码, 第二个字节是位码
                $q_code = $binary_num - 160;
                $w_code = ord($this->gb2312_message[++$i]) - 160; //因为汉字占两个字节, 所以这里++i, 否则下次循环会定位到当前汉字的位码
                $location = (94 * ($q_code - 1) + ($w_code - 1)) * self::PER_FONT_DOT_LENGTH;
            }
            //定位到location的位置
            fseek($fp, $location, SEEK_SET);
            $dot_string = fread($fp, self::PER_FONT_DOT_LENGTH);
            $this->dot_message .= $this->formatDotString($dot_string);
        }
        fclose($fp);
    }

    /**
     * @desc 将点阵体数据转换成二进制
     * @user lei
     * @date 2021/5/5
     * @param string $str
     * @return string
     */
    private function formatDotString(string $str): string
    {
        $len = strlen($str);
        $dots = '';
        for ($i = 0; $i < $len; $i++) {
            $dots .= sprintf("%08b", ord($str[$i]));
        }
        return $dots;
    }

    /**
     * @desc 转换要打印的内容到gb2312
     * @user lei
     * @date 2021/5/5
     */
    private function convertToGB2312()
    {
        $this->gb2312_message = iconv('utf-8', 'gb2312//IGNORE', $this->message);
    }

    /**
     * @desc 检查要打印的内容是否为空
     * @user lei
     * @date 2021/5/5
     * @throws \Exception
     */
    private function checkMessage()
    {
        if ($this->message === '') {
            throw new \Exception('please set message that you want to print');
        }
    }

    /**
     * @desc 二进制数据装进数组,准备打印
     * @user lei
     * @date 2021/5/5
     */
    private function shapeDotsToArray()
    {
        $len = count($this->message_array);
        for ($line = 0; $line < $len; $line++) { //每一行
            for ($i = 0; $i < $this->per_line_font_quantity; $i++) { //每一行的字数
                for ($j = 0; $j < self::PER_FONT_BINARY_LENGTH; $j++) { //每个字的点阵图数据长度
                    $x = $j % self::WIDTH; //数组的列数
                    $y = floor($j / self::HEIGHT); //数组的行数
                    $offset = $i * self::PER_FONT_BINARY_LENGTH + $j; //当前点阵图数据在数组中的位置
                    $this->dot_array[$line][$y][$x + $i * self::WIDTH] =
                        isset($this->message_array[$line][$offset]) && $this->message_array[$line][$offset] == 1
                            ? $this->inner_show : $this->outside_show;
                }
            }
        }
    }

    /**
     * @desc 按照设置的每行显示的字数分割点阵数据
     * @user lei
     * @date 2021/5/5
     */
    private function explodeDots()
    {
        $message_len = mb_strlen($this->message);
        $this->lines = intval(ceil($message_len / $this->per_line_font_quantity));
        $block_len = self::PER_FONT_BINARY_LENGTH * $this->per_line_font_quantity;
        for ($i = 0; $i < $this->lines; $i++) {
            $this->message_array[] = substr($this->dot_message, $block_len * $i, $block_len);
        }
    }

    /**
     * @desc 输出打印点阵体内容
     * @user lei
     * @date 2021/5/5
     */
    private function print()
    {
        for ($k = 0; $k < $this->lines; $k++) {
            for ($i = 0; $i < count($this->dot_array[$k]); $i++) {
                for ($j = 0; $j < count($this->dot_array[$k][0]); $j++) {
                    echo $this->dot_array[$k][$i][$j];
                }
                echo PHP_EOL;
                usleep($this->print_line_frequency);
            }
        }

    }


    public function run()
    {
        try {
            $this->checkMessage();
            $this->convertToGB2312();
            $this->readHZK();
            $this->explodeDots();
            $this->shapeDotsToArray();
            $this->print();
        } catch (\Exception $e) {
            echo $e->getMessage();
            exit;
        }

    }
}