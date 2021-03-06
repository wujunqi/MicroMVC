<?php
/**
 * 日志配置实体类
 *
 * ps :
 *     考虑日志类的使用情况，在判断是否重新获取文件指针的时候，并未考虑参数 mode 的变化。
 *     由于是用于日志，所以文件名是随时间变化的，也就是你两次获取的文件路径或文件指针是不同的。
 *
 * @author zhiyuan <zhiyuan12@staff.weibo.com>
 */
namespace Framework\Entities;
class LogConfig {
    private $root_path          = '.';
    private $file_name          = '';
    private $suffix_date_format = 'Ymd';
    private $_new_date          = '';
    private $_last_date         = '';
    private $_last_fp           = null;
    private $_last_file         = null;
    public function __construct(array $config = array()) {
        foreach ($config as $key => $value) {
            $this->$key = $value;
        }
    }
    public function __set($var_name, $value) {
        $this->$var_name = $value;
    }
    public function __get($var_name) {
        return $this->$var_name;
    }
    /**
     * 获取日志文件名
     * @return string
     */
    public function getLogFileName() {
        $this->_new_date = date($this->suffix_date_format);
        $ret             = $this->file_name . $this->_new_date . '.log';
        return $ret;
    }
    /**
     * 获取日志文件绝对路径
     * @return string
     */
    public function getFilePath() {
        $file_path = $this->root_path . "/" . $this->getLogFileName();
        return $file_path;
    }
    /**
     * 获取文件指针
     * @param  string     $mode
     * @return resource
     */
    public function getHandle($mode = 'w+') {
        $file_name = $this->getLogFileName();
        if ($this->_last_date !== $this->_new_date) {
            if ( ! is_dir($this->root_path)) {
                $tmp = mkdir($this->root_path, 0777, true);
                if (false === $tmp) {
                    //很多时候日志记录并不造成致命错误，所以不再抛出异常
                    return false;
                }
            }
            $file_path = $this->root_path . "/" . $this->getLogFileName();
            $fp               = fopen($file_path, $mode);
            $this->_last_fp   = $fp;
            $this->_last_date = $this->_new_date;
            $this->_last_file = $file_path;
        }
        return $this->_last_fp;
    }
    /**
     * 关闭文件指针
     * @return bool
     */
    public function closeHandle() {
        $this->_last_date = '';
        return fclose($this->_last_fp);
    }
}