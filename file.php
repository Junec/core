<?php
/**
 * 文件处理类
 *
 * @author chenjun <594553417@qq.com>
 * @copyright Copyright (c) 2008-2012 Technologies Inc.
 */
class core_file{
    
    const READ_ALL = '0';
    const READ_FILE = '1';
    const READ_DIR = '2';

    /* 以读的方式打开文件 */
    const READ = 'rb';
    /* 以读写的方式打开文件 */
    const READWRITE = 'rb+';
    /* 以写的方式打开文件 */
    const WRITE = 'wb';
    /* 以读写的方式打开文件 */
    const WRITEREAD = 'wb+';
    /* 以追加写入方式打开文件 */
    const APPEND_WRITE = 'ab';
    /* 以追加读写入方式打开文件 */
    const APPEND_WRITEREAD = 'ab+';

    
    /**
     * 获取文件列表
     * 
     * @param string $dir
     * @param boolean $mode 只读取文件列表,不包含文件夹
     * @return array
     */
    public function readList($dir, $mode = self::READ_ALL) {
        if (!$handle = @opendir($dir)) return array();
        $files = array();
        while (false !== ($file = @readdir($handle))) {
            if ('.' === $file || '..' === $file) continue;
            if ($mode == self::READ_DIR) {
                if ($this->isdir($dir . '/' . $file)) $files[] = $file;
            } elseif ($mode == self::READ_FILE) {
                if ($this->isFile($dir . '/' . $file)) $files[] = $file;
            } else
                $files[] = $file;
        }
        @closedir($handle);
        return $files;
    }

    /**
     * 写文件
     *
     * @param string $fileName 文件绝对路径
     * @param string $data 数据
     * @param string $method 读写模式,默认模式为self::WRITE
     * @param bool $ifLock 是否锁文件，默认为true即加锁
     * @param bool $ifCheckPath 是否检查文件名中的“..”，默认为true即检查
     * @param bool $ifChmod 是否将文件属性改为可读写,默认为true
     * @return int 返回写入的字节数
     */
    public function write($fileName, $data, $method = self::WRITE, $ifLock = true, $ifCheckPath = true, $ifChmod = true) {
        $dir = dirname($fileName);
        $this->mk($dir);
        if (!$handle = fopen($fileName, $method)) return false;
        $ifLock && flock($handle, LOCK_EX);
        $writeCheck = fwrite($handle, $data);
        $method == self::READWRITE && ftruncate($handle, strlen($data));
        fclose($handle);
        $ifChmod && chmod($fileName, 0777);
        return $writeCheck;
    }

    /**
     * 保存文件
     * 
     * @param string $fileName          保存的文件名
     * @param mixed $data               保存的数据
     * @param boolean $isBuildReturn    是否组装保存的数据是return $params的格式，如果没有则以变量声明的方式保存,默认为true则以return的方式保存
     * @param string $method            打开文件方式，默认为rb+的形式
     * @param boolean $ifLock           是否对文件加锁，默认为true即加锁
     */
    public function savePhpDataFile($fileName, $data, $isBuildReturn = true, $method = self::READWRITE, $ifLock = true) {
        $temp = "<?php\r\n ";
        if (!$isBuildReturn && is_array($data)) {
            foreach ($data as $key => $value) {
                if (!preg_match('/^\w+$/', $key)) continue;
                $temp .= "\$" . $key . " = " . String::var2String($value) . ";\r\n";
            }
            $temp .= "\r\n?>";
        } else {
            ($isBuildReturn) && $temp .= " return ";
            $temp .= String::var2String($data) . ";\r\n?>";
        }
        return $this->write($fileName, $temp, $method, $ifLock);
    }

    /**
     * 读取文件
     *
     * @param string $fileName 文件绝对路径
     * @param string $method 读取模式默认模式为rb
     * @return string 从文件中读取的数据
     */
    public function read($fileName, $method = self::READ) {
        $data = '';
        if (!$handle = fopen($fileName, $method)) return false;
        while (!feof($handle))
            $data .= fgets($handle);
        fclose($handle);
        return $data;
    }
    
    /**
     * 删除文件
     * 
     * @param string $filename 文件名称
     * @return boolean
     */
    public function del($filename) {
        return @unlink($filename);
    }

    /**
     * 删除目录
     * 
     * @param string $dir
     * @param boolean $f 是否强制删除
     * @return boolean
     */
    public function rm($dir, $f = false) {
        return $f ? $this->clearRecur($dir, true) : @rmdir($dir);
    }

    /**
     * 删除指定目录下的文件
     * 
     * @param string  $dir 目录
     * @param boolean $delFolder 是否删除目录
     * @return boolean
     */
    public function clear($dir, $delFolder = false) {
        if (!$this->isDir($dir)) return false;
        if (!$handle = @opendir($dir)) return false;
        while (false !== ($file = readdir($handle))) {
            if ('.' === $file[0] || '..' === $file[0]) continue;
            $filename = $dir . '/' . $file;
            if ($this->isFile($filename)) $this->del($filename);
        }
        $delFolder && @rmdir($dir);
        @closedir($handle);
        return true;
    }

    /**
     * 递归的删除目录
     * 
     * @param string $dir 目录
     * @param Boolean $delFolder 是否删除目录
     */
    public function clearRecur($dir, $delFolder = false) {
        if (!$this->isDir($dir)) return false;
        if (!$handle = @opendir($dir)) return false;
        while (false !== ($file = readdir($handle))) {
            if ('.' === $file || '..' === $file) continue;
            $_path = $dir . '/' . $file;
            if ($this->isDir($_path)) {
                $this->clearRecur($_path, $delFolder);
            } elseif ($this->isFile($_path))
                $this->del($_path);
        }
        $delFolder && @rmdir($dir);
        @closedir($handle);
        return true;
    }
    
    /**
     * 判断输入是否为文件
     *
     * @param string $fileName
     * @return boolean
     */
    public function isFile($fileName) {
        return $fileName ? is_file($fileName) : false;
    }

    /**
     * 判断输入是否为目录
     *
     * @param string $dir
     * @return boolean
     */
    public function isDir($dir) {
        return $dir ? is_dir($dir) : false;
    }

    /**
     * 取得文件信息
     * 
     * @param string $fileName 文件名字
     * @return array 文件信息
     */
    public function getFileInfo($fileName) {
        return $this->isFile($fileName) ? stat($fileName) : array();
    }

    /**
     * 取得文件后缀
     * 
     * @param string $filename 文件名称
     * @return string
     */
    public function getFileSuffix($filename) {
        if (false === ($rpos = strrpos($filename, '.'))) return '';
        return substr($filename, $rpos + 1);
    }

    /**
     * 取得目录信息
     * 
     * @param string $dir 目录路径
     * @return array
     */
    public function getDirInfo($dir) {
        return $this->isdir($dir) ? stat($dir) : array();
    }

    /**
     * 创建目录
     *
     * @param string $path 目录路径
     * @param int $permissions 权限
     * @return boolean
     */
    public function mk($path, $permissions = 0777, $recursive = false) {
        if(!$this->isDir($path)){
            if(!$this->mk(dirname($path),$permissions)) return false;
            if(!@mkdir($path,$permissions)) return false;
        }
        return true;
    }

    /**
     * 下载远程文件
     *
     * @param string $path 文件路径
     * @param string $isRename 是否重命名
     * @param string $dir 相对于Data/File/下的保存目录
     * @return string or boolean
     */
    public function download($path = '',$isRename = false,$dir = ''){
        set_time_limit(0); //限制最大的执行时间
        $baseName = basename($path);
        $fileName = substr($baseName,0,strrpos($baseName,'.'));
        $hz = substr($baseName,strrpos($baseName,'.'));
        if($isRename == true) $fileName = $this->renaming();

        $localPath = 'Data/File/';
        $localFileName = $fileName.$hz;//本地文件名
        if($dir != '') $localPath .= trim($dir,'/').'/';
        $saveDir = ROOT_DIR.'/'.$localPath;//保存目录
        $this->mk($saveDir);//判断文件夹是否存在
        $localPath .= $localFileName;//文件本地相对路径
        $localFilePath = $saveDir.$localFileName;//文件本地绝对路径

        if(!$file = fopen($path,self::READ)) return false;
        if(!$localFile = fopen($localFilePath,self::WRITE)) return false;
        while(!feof($file)){
            fputs($localFile,fread($file,1024),1024);
        }
        fclose($file);
        fclose($localFile);

        return $localPath;
    }

    /**
     * 文件重命名规则
     *
     * @return string
     */
    public function renaming(){
        return date("ymdHis").rand(1000, 9999);
    }

    /**
     * 上传文件
     *
     * @return string or boolean
     */
    public function upload($file,$maxsize='',$isRename = false,$filetype = array(),$dir = ''){
        $result = array('status'=>'succ','msg'=>'','path'=>'');
        set_time_limit(0); //限制最大的执行时间
        $localPath = 'data/upload/';
        if($dir != '') $localPath = trim($dir,'/').'/';
        $dir = ROOT_DIR.'/'.$localPath;//保存目录

        $maxsize = $maxsize*1024;

        if(!empty($file['error'])){
            switch($file['error']){
                case '1':
                    $error = '超过php.ini允许的大小';
                    break;
                case '2':
                    $error = '超过表单允许的大小';
                    break;
                case '3':
                    $error = '图片只有部分被上传';
                    break;
                case '4':
                    $error = '请选择图片';
                    break;
                case '6':
                    $error = '找不到临时目录';
                    break;
                case '7':
                    $error = '写文件到出错';
                    break;
                default:
                    $error = '未知错误';
            }
            $result['status'] = 'fail';
            $result['msg'] = $error;
            return $result;
        }

        if($file['name'] == '') {
            $result['status'] = 'fail';
            $result['msg'] = '无上传文件';
            return $result;
        }
        //原始文件名
        $fileName = $file['name'];
        //服务器上临时文件名
        $tmpName = $file['tmp_name'];
        //文件大小
        $fileSize = $file['size'];
        //获得文件扩展名
        $tempArr = explode(".", $fileName);
        $fileExt = array_pop($tempArr);
        $fileExt = trim($fileExt);
        $fileExt = strtolower($fileExt);

        //检查是否已上传
        if (@is_uploaded_file($tmpName) === false) {
            $result['status'] = 'fail';
            $result['msg'] = '上传失败';
            return $result;
        }
        //检查文件大小
        if ($fileSize > $maxsize) {
            $result['status'] = 'fail';
            $result['msg'] = '上传文件大小超过限制';
            return $result;
        }

        //检查扩展名
        if (in_array($fileExt, $filetype) === false) {
            $result['status'] = 'fail';
            $result['msg'] = '上传文件扩展名是不允许的扩展名';
            return $result;
        }

        //创建目录
        $this->mk($dir);

        //新文件名
        if($isRename == true) $newFileName = $this->renaming() . '.' . $fileExt;
        else $newFileName = $fileName;

        //移动文件
        $filePath = $dir.$newFileName;
        if (@move_uploaded_file($tmpName, $filePath) === false) {
            $result['status'] = 'fail';
            $result['msg'] = '上传文件失败';
            return $result;
        }
        @chmod($filePath, 0644);

        $localFilePath = $localPath.$newFileName;
        $result['msg'] = '上传成功';
        $result['path'] = $localFilePath;

        return $result;
    }

}
?>