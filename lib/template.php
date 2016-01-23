<?php
/**
 * Template
 *
 * @author Chen Jun <594553417@qq.com>
 * @copyright  Copyright (c) 2015.
*/
class core_template{

    /** 版本 **/
    const VESSION = '1.0';

    /** 模版文件路径 **/
    public $template_dir = '';

    /** 编译文件路径 **/
    public $compile_dir = '';

    /** 左标识符 **/
    public $left_delim = '{#';

    /** 右标识符 **/
    public $right_delim = '#}';

    /** 模版变量 **/
    public $vars = array();

    /** 编译文件标识 **/
    public $mark = 'template';

    /** 解析标签 **/
    private $parseTags = array(
        'php',
        '\/php',
        'include',
        'foreachelse',
        'foreach',
        '\/foreach',
        'if',
        'elseif',
        'else',
        '\/if',
    );

    //tudo: 未实现
    public $regFunctions = array();


    /**
     * 获取模版原始文件路径
     *
     * @param string $tpl
     * @return string
     */
    protected function getTplPath($tpl = ''){
        return $this->template_dir.DIRECTORY_SEPARATOR.$tpl;
    }


    /**
     * 获取模版编译文件路径
     *
     * @param string $tpl
     * @return string
     */
    protected function getCompilePath($tpl = ''){
        $compileFile = str_replace(array('/','.'),'_',$tpl).'_'.$this->mark.'_complie.php';
        return $this->compile_dir.DIRECTORY_SEPARATOR.$compileFile;
    }


    /**
     * 编译模版文件
     *
     * @param string $tpl
     * @return void
     */
    private function compile($tpl = ''){
        $tplFile = $this->getTplPath($tpl);
        $compileFile = $this->getCompilePath($tpl);
        $tplContent = core::instance('core_file')->read( $tplFile );

        $this->parseVar($tplContent);
        $this->parseTag($tplContent);

        $md5 = md5_file($tplFile);
        $expireTime = time();
        $tplHeader = "<?php \n/* \nCore Template Compile \nCreated on: ".date('Y-m-d H:i:s',$expireTime)." \n*/ \n\$this->checkCompile('$tpl','$md5','$expireTime');\n?>\n";
        $tplContent = $tplHeader.$tplContent;
        core::instance('core_file')->write( $compileFile, $tplContent );
    }


    /**
     * 检测模版是否被编译
     *
     * @param string $tpl
     * @param string $md5 模版文件md5
     * @param string $expireTime
     * @return void
     */
    private function checkCompile($tpl = '',$md5 = ''){
        if(md5_file($this->getTplPath($tpl)) != $md5) $this->compile($tpl);
    }


    /**
     * 注册模版函数
     * 
     * @param string $function 
     * @return void
     */
    private function regFunction($tplFunction = '',$function = array()){
        $this->regFunctions[$tplFunction] = $function;
    }

    /**
     * 模板变量赋值
     * 
     * @param string $var 变量名
     * @param string $val 变量值
     * @return void
     */
    public function assign($var = '',$val = ''){
        if(is_array($var)){
            foreach($var as $k=>$v) $this->vars[$k] = $v;
        }else{
            $this->vars[$var] = $val;
        }
    }

    /**
     * 模板渲染
     * 
     * @param string $tpl 
     * @return string
     */
    public function fetch($tpl = ''){
        extract($this->vars, EXTR_OVERWRITE);
        $compileFile = $this->getCompilePath($tpl);
        if(!file_exists($compileFile)) $this->compile($tpl);
        ob_start();
        ob_implicit_flush(0);
        include $compileFile;
        $fetch = ob_get_clean();
        return $fetch;
    }

    /**
     * 模板渲染输出
     * 
     * @param string $tpl 
     * @return echo string
     */
    public function display($tpl = ''){
        echo $this->fetch($tpl);
    }

    /**
     * 解析编译文件变量
     * 
     * @param string $compile 
     * @return string
     */
    private function parseVar(&$compile = ''){
        preg_match_all("/{$this->left_delim}([$].+?){$this->right_delim}/",$compile,$vars);
        foreach($vars[0] as $k=>$v){
            $isModify = strstr($v, '|');
            if( $isModify === false ){//常规变量   
                $compile = str_replace($v,'<?php echo '.$vars[1][$k].';?>',$compile);
            }else{//带修饰器
                $parseVarModifyParams = array();
                $varModifyArray = explode('|',$vars[1][$k]);
                $varOri = array_shift($varModifyArray);
                $varModify = join('|',$varModifyArray);
                foreach( $varModifyArray as $vmk => $vmv){
                    $isModifyParams = strpos($vmv, ':');
                    if( $isModifyParams === false ){
                        $parseVarModifyParams[] = array('modify'=>$vmv);
                    }else{
                        $modify = substr($vmv,0,$isModifyParams);
                        $modifyParams = substr($vmv,$isModifyParams+1);
                        $modifyParams = explode(':',$modifyParams);
                        $parseVarModifyParams[] = array('modify'=>$modify,'params'=>$modifyParams);
                    }
                }
                $compile = str_replace($v,'<?php $this->varModify('.$varOri.','.$this->varExport($parseVarModifyParams).');?>',$compile);
                unset($varModifyArray);
            }
        }
        return $compile; 
    }

    private function varModify($var = '',$params = array()){
        $echo = $var;
        foreach($params as $v){
            $mp = $v['params'];
            switch($v['modify']){
                case 'default':
                    $echo = $echo == '' ? $mp[0] : $echo;
                break;
                case 'truncate':
                    if ($mp[0] == 0){
                        $echo = '';
                    }else{
                        $pa = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf]"."[\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]"."|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7]"."[\x80-\xbf][\x80-\xbf][\x80-\xbf]/";
                        preg_match_all($pa,$echo,$tString);
                        $echo = join('',array_slice($tString[0],0,$mp[0]));
                        if (count($tString[0])>$mp[0]) $echo .= $mp[1];
                    }
                break;
                case 'date':
                    $echo = date($mp[0],$echo);
                break;
                case 'strip_tags':
                    $echo = strip_tags($echo,$mp[0]);
                break;
            }
        }
        echo $echo;
        unset($echo);
    }


    private function varExport($var,$mk = ''){
        if (is_array($var)){
            $code = 'array(';
            foreach ($var as $key => $value) {
               $code .= "'$key'=>".$this->varExport($value,$key).',';
            }
            $code = chop($code, ','); //remove unnecessary coma
            $code .= ')';
                return $code;
        }else{
            if($mk === 'modify'){
                return "'".$var."'";
            }else{
                return $var;
            }
        }
    }

    /**
     * 解析编译文件全局标签
     * 
     * @param string $compile 
     * @return string
     */
    private function parseTag(&$compile = ''){
        foreach($this->parseTags as $tag){
            $regxp = "/{$this->left_delim}{$tag}(.*?){$this->right_delim}/ies";
            $compile = preg_replace($regxp,"\$this->parseTagAll('$tag','\\1')",$compile);
        }
    }

    /**
     * 解析编译文件标签代码
     * 
     * @param string $compile 
     * @return string
     */
    private function parseTagAll($tag = '',$attribute = ''){
        $parseStr = '';
        $attribute = preg_replace('/(\s+)/',' ',trim($attribute,' '));
        if( $attribute ) $atTmp = explode(' ',$attribute);
        else $atTmp = array();
        $attributeArray = array();
        foreach($atTmp as $v){
            $atrTmp = explode('=',$v);
            $attributeArray[$atrTmp[0]] = $atrTmp[1];
        }
        unset($atTmp,$atrTmp);

        switch ($tag) {
            case 'php':
                $parseStr = '<?php ';
            break;
            case '\/php':
                $parseStr = ' ?>';
            break;
            case 'include':
                $includeFile = '';
                $assignStr = '';
                if( isset($attributeArray['file']) && $attributeArray['file'] != '' ){
                    $includeFile = $attributeArray['file'];
                    $includeFile = trim($includeFile,'\'');
                    $includeFile = trim($includeFile,'"');
                    unset($attributeArray['file']);
                }else{
                    throw new core_exception("template error: include file is none.");
                }
                $parseStr = "<?php ";
                if(is_array($attributeArray) && count($attributeArray)>0){
                    $assignStr .= 'array(';
                    foreach($attributeArray as $k=>$v){
                        $parseStr .= "\$this->assign('{$k}',{$v});";
                    }
                }
                
                $parseStr .= "\$this->display('{$includeFile}') ; ?>";
            break;
            case 'foreach':
                $parseStr = '<?php if(is_array('.$attributeArray['from'].') && count('.$attributeArray['from'].')>0){foreach('.$attributeArray['from'].' as ';
                if( isset($attributeArray['key']) && $attributeArray['key']!= '' ){
                    $parseStr .= '$'.$attributeArray['key'].' => ';
                }
                if( isset($attributeArray['item']) && $attributeArray['item']!= '' ){
                    $parseStr .= '$'.$attributeArray['item'].'){ ?>';
                }else{
                    throw new core_exception("template error: foreach value is none.");
                }
            break;
            case '\/foreach':
                $parseStr = '<?php }} ?>';
            break;
            case 'foreachelse':
                $parseStr = '<?php }}else{{ ?>';
            break;
            case 'if':
                $parseStr = '<?php if('.$attribute.'){ ?>';
            break;
            case 'elseif':
                $parseStr = '<?php }elseif('.$attribute.'){?>';
            break;
            case 'else':
                $parseStr = '<?php }else{ ?>';
            break;
            case '\/if':
                $parseStr = '<?php } ?>';
            break;
        }

        return $parseStr;
    }

}