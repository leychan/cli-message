<?php

namespace cliMessage;

use League\CLImate\CLImate;

class CliMessage
{
    const WIDTH = 16; //一个字二进制表示的数组宽度
    const HEIGHT = 16; //一个字二进制表示的数组长度
    const PER_FONT_BINARY_LENGTH = 256; //一个字二进制串的长度
    const PER_FONT_DOT_LENGTH = 32; //一个字符(非汉字)在汉字库中的长度
    const FONT_FILE = __DIR__ . '/font_file/HZK16'; //汉字库文件
    /**
     * @var int 每行打印的字数
     */
    private int $per_line_quantity = 4;

    /**
     * @var int 根据输入的字符数量和每行打印的字数算出的行数
     */
    private int $lines = 0; //根据输入的字符数量和每行打印的字数算出的行数

    /**
     * @var string 要打印的字符
     */
    private string $message = '';

    /**
     * @var string 要打印的字符的gb2312编码后的字符
     */
    private string $gb2312_message = '';

    /**
     * @var string 输入的字符的二进制形式
     */
    private string $binary_message = '';

    /**
     * @var string 打印输出时展示的命中图标
     */
    private string $inner_show = '■';

    /**
     * @var string 打印输出时展示的未命中的图标
     */
    private string $outer_show = '♡';

    /**
     * @var array 最终的要打印的二进制数组(按照每一行)
     */
    private array $dot_array = [];

    /**
     * @var int 打印的速度(频率), 微秒
     */
    private int $print_line_frequency = 130000;

    /**
     * @var array 二进制的字符串形式的数组(按照每一行)
     */
    private array $message_array = [];

    /**
     * @var string 命令行获取输入的参数的提示信息
     */
    private string $cli_input_tips = '';

    private CLImate $cli;

    public function __construct() {
        $this->cli = new CLImate();
    }

    /**
     * @desc 设置每一行的打印字的数量
     * @user lei
     * @date 2021/5/5
     * @param int $num
     */
    public function setPerLineQuantity(int $num)
    {
        $this->per_line_quantity = $num;
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
            $this->binary_message .= $this->formatDotStringToBinary($dot_string);
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
    private function formatDotStringToBinary(string $str): string
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
            for ($i = 0; $i < $this->per_line_quantity; $i++) { //每一行的字数
                for ($j = 0; $j < self::PER_FONT_BINARY_LENGTH; $j++) { //每个字的点阵图数据长度
                    $x = $j % self::WIDTH; //数组的列数
                    $y = floor($j / self::HEIGHT); //数组的行数
                    $offset = $i * self::PER_FONT_BINARY_LENGTH + $j; //当前点阵图数据在数组中的位置
                    $this->dot_array[$line][$y][$x + $i * self::WIDTH] =
                        isset($this->message_array[$line][$offset]) && $this->message_array[$line][$offset] == 1
                            ? $this->inner_show : $this->outer_show;
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
        $this->lines = intval(ceil($message_len / $this->per_line_quantity));
        $block_len = self::PER_FONT_BINARY_LENGTH * $this->per_line_quantity;
        for ($i = 0; $i < $this->lines; $i++) {
            $this->message_array[] = substr($this->binary_message, $block_len * $i, $block_len);
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

    /**
     * @desc 获取命令行输入的参数
     * @user lei
     * @date 2021/5/5
     * @return mixed
     */
    private function getArgs() {
        $input = $this->cli->input($this->cli_input_tips);
        return $input->prompt();
    }

    /**
     * @desc 命令行获取参数时的提示
     * @user lei
     * @date 2021/5/5
     * @param string $tips
     */
    private function setCliInputTips(string $tips) {
        $this->cli_input_tips = $tips;
    }

    private function checkPerLineQuantity() {
        if (!is_numeric($this->per_line_quantity)) {
            throw new \Exception('please set message that you want to print');
        }
    }

    private function cliRun() {
        $this->setCliInputTips('请输入您想打印的内容:');
        $this->message = $this->getArgs();
        $this->checkMessage();
        $this->setCliInputTips('请输入您想打印时,每一行展示的字数:');
        $this->per_line_quantity = $this->getArgs();
        $this->checkPerLineQuantity();
        $this->setCliInputTips("请输入您想打印时,命中时展示的样式, 默认为'{$this->inner_show}':");
        $inner_show = $this->getArgs();
        $this->inner_show = empty($inner_show) ? $this->inner_show : $inner_show;
        $this->setCliInputTips("请输入您想打印时,未命中时展示的样式, 默认为'{$this->outer_show}':");
        $outer_show = $this->getArgs();
        $this->outer_show = empty($outer_show) ? $this->outer_show : $outer_show;
    }

    public function run()
    {
        try {
            $this->cliRun();
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