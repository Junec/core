<?php
/**
 * Zip
 *
 * @author June Chen <594553417@qq.com>
 * @copyright  Copyright (c) 2015.
*/

class core_zip{

    private $ctrl_dir     = array();
    private $datasec      = array();



    // ------------------------------------------------------ //
    // #解压文件
    //
    // $archive   = new Zip();
    // $zipfile   = "ZIP压缩文件名";
    // $savepath  = "解压缩目录名";
    // $zipfile   = $unzipfile;
    // $savepath  = $unziptarget;
    // $array     = $archive->GetZipInnerFilesInfo($zipfile);
    // $filecount = 0;
    // $dircount  = 0;
    // $failfiles = array();
    // set_time_limit(0);  // 修改为不限制超时时间(默认为30秒)
    //
    // for($i=0; $i<count($array); $i++) {
    //     if($array[$i][folder] == 0){
    //         if($archive->unZip($zipfile, $savepath, $i) > 0){
    //             $filecount++;
    //         }else{
    //             $failfiles[] = $array[$i][filename];
    //         }
    //     }else{
    //         $dircount++;
    //     }
    // }
    // set_time_limit(30);
    //printf("文件夹:%d&nbsp;&nbsp;&nbsp;&nbsp;解压文件:%d&nbsp;&nbsp;&nbsp;&nbsp;失败:%d<br>\r\n", $dircount, $filecount, count($failfiles));
    //if(count($failfiles) > 0){
    //    foreach($failfiles as $file){
    //        printf("&middot;%s<br>\r\n", $file);
    //    }
    //}
    // ------------------------------------------------------ //

    public function unZip($zipfile, $to, $index = Array(-1)){
        $ok  = 0;
        $zip = @fopen($zipfile, 'rb');
        if(!$zip){ return(-1); }

        $cdir      = $this->ReadCentralDir($zip, $zipfile);
        $pos_entry = $cdir['offset'];

        if(!is_array($index)){ $index = array($index); }
        for($i=0; $index[$i]; $i++){
            if(intval($index[$i]) != $index[$i] || $index[$i] > $cdir['entries'])
            {
                return(-1);
            }
        }

        for($i=0; $i<$cdir['entries']; $i++){
            @fseek($zip, $pos_entry);
            $header          = $this->ReadCentralFileHeaders($zip);
            $header['index'] = $i;
            $pos_entry       = ftell($zip);
            @rewind($zip);
            fseek($zip, $header['offset']);
            if(in_array("-1", $index) || in_array($i, $index))
            {
                $stat[$header['filename']] = $this->ExtractFile($header, $to, $zip);
            }
        }
        fclose($zip);
        return $stat;

    }

    // ------------------------------------------------------ //
    // #获取被压缩文件的信息
    //
    // $archive = new PHPZip();
    // $array = $archive->GetZipInnerFilesInfo(ZIP压缩文件名);
    // for($i=0; $i<count($array); $i++) {
    //     printf("<b>&middot;%s</b><br>\r\n", $array[$i][filename]);
    //     foreach($array[$i] as $key => $value)
    //         printf("%s => %s<br>\r\n", $key, $value);
    //     print "\r\n<p>------------------------------------<p>\r\n\r\n";
    // }
    // ------------------------------------------------------ //
    public function GetZipInnerFilesInfo($zipfile){
        $zip = @fopen($zipfile, 'rb');
        if(!$zip){ return(0); }
        $centd = $this->ReadCentralDir($zip, $zipfile);

        @rewind($zip);
        @fseek($zip, $centd['offset']);
        $ret = array();

        for($i=0; $i<$centd['entries']; $i++){
            $header          = $this->ReadCentralFileHeaders($zip);
            $header['index'] = $i;
            $info = array(
                'filename'        => $header['filename'],                   // 文件名
                'stored_filename' => $header['stored_filename'],            // 压缩后文件名
                'size'            => $header['size'],                       // 大小
                'compressed_size' => $header['compressed_size'],            // 压缩后大小
                'crc'             => strtoupper(dechex($header['crc'])),    // CRC32
                'mtime'           => date("Y-m-d H:i:s",$header['mtime']),  // 文件修改时间
                'comment'         => $header['comment'],                    // 注释
                'folder'          => ($header['external'] == 0x41FF0010 || $header['external'] == 16) ? 1 : 0,  // 是否为文件夹
                'index'           => $header['index'],                      // 文件索引
                'status'          => $header['status']                      // 状态
            );
            $ret[] = $info;
            unset($header);
        }
        fclose($zip);
        return $ret;

    }

    // ------------------------------------------------------ //
    // #获取压缩文件的注释
    // $archive = new PHPZip();
    // echo $archive->GetZipComment(ZIP压缩文件名);
    // ------------------------------------------------------ //
    public function GetZipComment($zipfile){
        $zip = @fopen($zipfile, 'rb');
        if(!$zip){ return(0); }
        $centd = $this->ReadCentralDir($zip, $zipfile);
        fclose($zip);
        return $centd[comment];

    }


    private function ReadCentralDir($zip, $zipfile){
        $size     = filesize($zipfile);
        $max_size = ($size < 277) ? $size : 277;

        @fseek($zip, $size - $max_size);
        $pos   = ftell($zip);
        $bytes = 0x00000000;

        while($pos < $size){
            $byte  = @fread($zip, 1);
            $bytes = ($bytes << 8) | Ord($byte);
            $pos++;
            if($bytes == 0x504b0506){ break; }
        }

        $data = unpack('vdisk/vdisk_start/vdisk_entries/ventries/Vsize/Voffset/vcomment_size', fread($zip, 18));
        $centd['comment']      = ($data['comment_size'] != 0) ? fread($zip, $data['comment_size']) : '';  // 注释
        $centd['entries']      = $data['entries'];
        $centd['disk_entries'] = $data['disk_entries'];
        $centd['offset']       = $data['offset'];
        $centd['disk_start']   = $data['disk_start'];
        $centd['size']         = $data['size'];
        $centd['disk']         = $data['disk'];
        return $centd;
    }

    private function ReadCentralFileHeaders($zip){
        $binary_data = fread($zip, 46);
        $header      = unpack('vchkid/vid/vversion/vversion_extracted/vflag/vcompression/vmtime/vmdate/Vcrc/Vcompressed_size/Vsize/vfilename_len/vextra_len/vcomment_len/vdisk/vinternal/Vexternal/Voffset', $binary_data);
        $header['filename'] = ($header['filename_len'] != 0) ? fread($zip, $header['filename_len']) : '';
        $header['extra']    = ($header['extra_len']    != 0) ? fread($zip, $header['extra_len'])    : '';
        $header['comment']  = ($header['comment_len']  != 0) ? fread($zip, $header['comment_len'])  : '';
        if($header['mdate'] && $header['mtime']){
            $hour    = ($header['mtime']  & 0xF800) >> 11;
            $minute  = ($header['mtime']  & 0x07E0) >> 5;
            $seconde = ($header['mtime']  & 0x001F) * 2;
            $year    = (($header['mdate'] & 0xFE00) >> 9) + 1980;
            $month   = ($header['mdate']  & 0x01E0) >> 5;
            $day     = $header['mdate']   & 0x001F;
            $header['mtime'] = mktime($hour, $minute, $seconde, $month, $day, $year);
        }else{
            $header['mtime'] = time();
        }
        $header['stored_filename'] = $header['filename'];
        $header['status'] = 'ok';
        if(substr($header['filename'], -1) == '/'){ $header['external'] = 0x41FF0010; }  // 判断是否文件夹
        return $header;
    }

    private function ReadFileHeader($zip){
        $binary_data = fread($zip, 30);
        $data        = unpack('vchk/vid/vversion/vflag/vcompression/vmtime/vmdate/Vcrc/Vcompressed_size/Vsize/vfilename_len/vextra_len', $binary_data);
        $header['filename']        = fread($zip, $data['filename_len']);
        $header['extra']           = ($data['extra_len'] != 0) ? fread($zip, $data['extra_len']) : '';
        $header['compression']     = $data['compression'];
        $header['size']            = $data['size'];
        $header['compressed_size'] = $data['compressed_size'];
        $header['crc']             = $data['crc'];
        $header['flag']            = $data['flag'];
        $header['mdate']           = $data['mdate'];
        $header['mtime']           = $data['mtime'];
        if($header['mdate'] && $header['mtime']){
            $hour    = ($header['mtime']  & 0xF800) >> 11;
            $minute  = ($header['mtime']  & 0x07E0) >> 5;
            $seconde = ($header['mtime']  & 0x001F) * 2;
            $year    = (($header['mdate'] & 0xFE00) >> 9) + 1980;
            $month   = ($header['mdate']  & 0x01E0) >> 5;
            $day     = $header['mdate']   & 0x001F;
            $header['mtime'] = mktime($hour, $minute, $seconde, $month, $day, $year);
        }else{
            $header['mtime'] = time();
        }
        $header['stored_filename'] = $header['filename'];
        $header['status']          = "ok";
        return $header;
    }

    private function ExtractFile($header, $to, $zip){
        $header = $this->readfileheader($zip);

        if(substr($to, -1) != "/"){ $to .= "/"; }
        if(!@is_dir($to)){ @mkdir($to, 0777); }

        $pth = explode("/", dirname($header['filename']));
        for($i=0; isset($pth[$i]); $i++){
            if(!$pth[$i]){ continue; }
            $pthss .= $pth[$i]."/";
            if(!is_dir($to.$pthss)){ @mkdir($to.$pthss, 0777); }
        }

        if(!($header['external'] == 0x41FF0010) && !($header['external'] == 16))
        {
            if($header['compression'] == 0)
            {
                $fp = @fopen($to.$header['filename'], 'wb');
                if(!$fp){ return(-1); }
                $size = $header['compressed_size'];

                while($size != 0)
                {
                    $read_size   = ($size < 2048 ? $size : 2048);
                    $buffer      = fread($zip, $read_size);
                    $binary_data = pack('a'.$read_size, $buffer);
                    @fwrite($fp, $binary_data, $read_size);
                    $size       -= $read_size;
                }
                fclose($fp);
                touch($to.$header['filename'], $header['mtime']);
            }else{
                $fp = @fopen($to.$header['filename'].'.gz', 'wb');
                if(!$fp){ return(-1); }
                $binary_data = pack('va1a1Va1a1', 0x8b1f, Chr($header['compression']), Chr(0x00), time(), Chr(0x00), Chr(3));

                fwrite($fp, $binary_data, 10);
                $size = $header['compressed_size'];
                while($size != 0)
                {
                    $read_size   = ($size < 1024 ? $size : 1024);
                    $buffer      = fread($zip, $read_size);
                    $binary_data = pack('a'.$read_size, $buffer);
                    @fwrite($fp, $binary_data, $read_size);
                    $size       -= $read_size;
                }

                $binary_data = pack('VV', $header['crc'], $header['size']);
                fwrite($fp, $binary_data, 8);
                fclose($fp);

                $gzp = @gzopen($to.$header['filename'].'.gz', 'rb') or die("Cette archive est compress!");

                if(!$gzp){ return(-2); }
                $fp = @fopen($to.$header['filename'], 'wb');
                if(!$fp){ return(-1); }
                $size = $header['size'];

                while($size != 0)
                {
                    $read_size   = ($size < 2048 ? $size : 2048);
                    $buffer      = gzread($gzp, $read_size);
                    $binary_data = pack('a'.$read_size, $buffer);
                    @fwrite($fp, $binary_data, $read_size);
                    $size       -= $read_size;
                }
                fclose($fp); gzclose($gzp);
                touch($to.$header['filename'], $header['mtime']);
                @unlink($to.$header['filename'].'.gz');
            }
        }
        return true;
    }

}

?>
