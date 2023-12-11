<?php

class Themes {

    public $Core;

    protected function processTheme($Core) {
        $this->Core = $Core;

        $_THEME = array();
        $_THEME['CORE'] = sprintf('%s%s',$this->Core['Path']['core'], Template::THEME_DIR . '/' . Template::TEMPLATE_NAME);
        $_THEME['OVERRIDE'] = sprintf('%s%s',$this->Core['Path']['override'], Template::THEME_DIR. '/'  . Template::TEMPLATE_NAME);
       
        switch($this->Core['Method']) {
            case 'css':
            case 'js':
            case 'font':
            case 'img':
                $_THEME['CORE'] .= sprintf('/%s',$this->Core['Method']);
                $_THEME['OVERRIDE'] .= sprintf('/%s',$this->Core['Method']);
            break;
            default: 
                $this->callErrorPage();
                return;
            break;
        }

        $this->Core['Theme'] = array();
        $this->Core['Theme']['Path'] = $_THEME;
        $this->Core['Theme']['Files'] = array();
        $this->Core['Theme']['Files'][$this->Core['Method']] = array();

        $this->listFolderFiles($_THEME['CORE']);
        $this->listFolderFiles($_THEME['OVERRIDE']);
        
        $_FOLDER_FILE = $_GET['param'];

        //tcheck if file isset
        if(file_exists(sprintf("%s/%s.%s",$this->Core['Theme']['Path']['CORE'],$this->Core['process'], $_FOLDER_FILE))) {

            if(is_dir(sprintf("%s/%s.%s",$this->Core['Theme']['Path']['CORE'],$this->Core['process'], $_FOLDER_FILE))) {
                $this->callErrorPage();
                return;
            }

            //if override isset load default override else core
            if(file_exists(sprintf("%s/%s.%s",$this->Core['Theme']['Path']['OVERRIDE'],$this->Core['process'],$_FOLDER_FILE))) {
                $_FILE = sprintf("%s/%s.%s",$this->Core['Theme']['Path']['OVERRIDE'],$this->Core['process'],$_FOLDER_FILE);
            }
            else{
                $_FILE = sprintf("%s/%s.%s",$this->Core['Theme']['Path']['CORE'],$this->Core['process'],$_FOLDER_FILE);
            }

            
            if(filesize($_FILE) < 0) {
                $this->callErrorPage();
                return;
            }

            header("Content-type: " . $this->mime_content_type($_FILE));
            header("Pragma-directive:max-age=2592000, public");
            header("Cache-directive:max-age=2592000, public");
            header("Cache-Control:max-age=2592000, private");
            header("Pragma:max-age=2592000, public");
            header("Expires: 2592000");
            

            $_R = PHP_EOL;	
            $_R .= "/********************************** ";	
            $_R .= "Module - ". $this->Core["Module"] . " ";	
            $_R .= strtr($_FILE,array(sprintf("%s/",$this->Core['Theme']['Path']['CORE']) => "")) ." ";	
            $_R .= "*/".PHP_EOL;
            switch($this->Core['Method']) {
                case 'css':
                case 'js':
                    $_R .= file_get_contents($_FILE) . PHP_EOL;
                    
                    if(count($this->Core["Theme"]["Files"][$this->Core['Method']]) > 0) {
                        
                        foreach($this->Core["Theme"]["Files"][$this->Core['Method']] as $_PATH_F) {
                            if(preg_match("/" .$this->Core['mode']."/i",$_PATH_F)) {
                                if(filesize($_PATH_F) > 0) {
                                    $_R .= PHP_EOL;	
                                    $_R .= "/********************************** ";	
                                    $_R .= "Module - ". $this->Core["Module"] . " ";	
                                    $_R .= strtr($_PATH_F,array(sprintf("%s/",$this->Core['Theme']['Path']['CORE']) => "")) ." ";	
                                    $_R .= "*/".PHP_EOL;	
                                    $_R .= file_get_contents($_PATH_F) . PHP_EOL;	
                                }
                            }
                        }
                    }
                    
                break;
                case 'font':
                case 'img':
                default: 
                    $_R .= file_get_contents($_FILE) . PHP_EOL;
                break;
            }
            echo $_R;

        }else{
            $this->callErrorPage();
        }
    }

    private function mime_content_type($filename) {

        $mime_types = array(

            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',

            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',

            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',

            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',

            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',

            // ms office
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',

            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        );

        $ext = strtolower(array_pop(explode('.',$filename)));
        if (array_key_exists($ext, $mime_types)) {
            return $mime_types[$ext];
        }
        elseif (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME);
            $mimetype = finfo_file($finfo, $filename);
            finfo_close($finfo);
            return $mimetype;
        }
        else {
            return 'application/octet-stream';
        }
    }

    private function listFolderFiles($dir) {
        $ffs = @scandir($dir);
        
        //folder doesn't exist !
        if(!$ffs) {
            return;
        }		

        unset($ffs[array_search('.', $ffs, true)]);
        unset($ffs[array_search('..', $ffs, true)]);

        //prevent empty ordered elements
        if (count($ffs) < 1)
            return;

        foreach($ffs as $ff) {
            if(is_dir($dir.'/'.$ff)) {
                $this->listFolderFiles($dir.'/'.$ff);
            }else{
                // if(preg_match("/{$this->Core['process']}/i",$ff)) {
                    // $this->Core['Theme']['Files'][$this->Core['Method']][] = $dir. '/'. $ff;
                // }else{
                    if(!preg_match("/back/i",$ff) && !preg_match("/front/i",$ff)) {
                        $this->Core['Theme']['Files'][$this->Core['Method']][] = $dir. '/'. $ff;
                    }
                // }
            }
        }
    }

    static function compile($data) {
        $data = preg_replace('/\t/','', $data);
        $data = preg_replace('/\n/','', $data);
        $data = preg_replace('/\r/','', $data);
        $data = preg_replace("/\s\s+/",'', $data);
        $data = preg_replace('/\v/','', $data);
        $data = preg_replace('/\f/','', $data);
        $data = rtrim($data,"\t.");
        $data = rtrim($data,"\n.");
        $data = rtrim($data,"\r.");
        $data = rtrim($data,"\v.");
        $data = rtrim($data,"\f.");
        $data = rtrim($data, "\x00..\x1F");
        // $data = utf8_encode($data);
        // $data = mb_convert_encoding($data, 'UTF-8', 'ASCII');
        return self::minify_output($data);
    }
    
    static function minify_output($input) {
        
        if(trim($input) === "") return $input;
        // Remove extra white-space(s) between HTML attribute(s)
        $input = preg_replace_callback('#<([^\/\s<>!]+)(?:\s+([^<>]*?)\s*|\s*)(\/?)>#s', function($matches) {
            return '<' . $matches[1] . preg_replace('#([^\s=]+)(\=([\'"]?)(.*?)\3)?(\s+|$)#s', ' $1$2', $matches[2]) . $matches[3] . '>';
        }, str_replace("\r", "", $input));

        $input = preg_replace(
        array('/\s*(\w)\s*{\s*/','/\s*(\S*:)(\s*)([^;]*)(\s|\n)*;(\n|\s)*/','/\n/','/\s*}\s*/'), 
        array('$1{ ','$1$3;',"",'} '),
        $input
        );

        // css
        $input = preg_replace(array(
                // Remove comment(s)
                '#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')|\/\*(?!\!)(?>.*?\*\/)|^\s*|\s*$#s',
                // Remove unused white-space(s)
                '#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\'|\/\*(?>.*?\*\/))|\s*+;\s*+(})\s*+|\s*+([*$~^|]?+=|[{};,>~]|\s(?![0-9\.])|!important\b)\s*+|([[(:])\s++|\s++([])])|\s++(:)\s*+(?!(?>[^{}"\']++|"(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')*+{)|^\s++|\s++\z|(\s)\s+#si',
                // Replace `0(cm|em|ex|in|mm|pc|pt|px|vh|vw|%)` with `0`
                '#(?<=[\s:])(0)(cm|em|ex|in|mm|pc|pt|px|vh|vw|%)#si',
                // Replace `:0 0 0 0` with `:0`
                '#:(0\s+0|0\s+0\s+0\s+0)(?=[;\}]|\!important)#i',
                // Replace `background-position:0` with `background-position:0 0`
                '#(background-position):0(?=[;\}])#si',
                // Replace `0.6` with `.6`, but only when preceded by `:`, `,`, `-` or a white-space
                '#(?<=[\s:,\-])0+\.(\d+)#s',
                // Minify string value
                '#(\/\*(?>.*?\*\/))|(?<!content\:)([\'"])([a-z_][a-z0-9\-_]*?)\2(?=[\s\{\}\];,])#si',
                '#(\/\*(?>.*?\*\/))|(\burl\()([\'"])([^\s]+?)\3(\))#si',
                // Minify HEX color code
                '#(?<=[\s:,\-]\#)([a-f0-6]+)\1([a-f0-6]+)\2([a-f0-6]+)\3#i',
                // Replace `(border|outline):none` with `(border|outline):0`
                '#(?<=[\{;])(border|outline):none(?=[;\}\!])#',
                // Remove empty selector(s)
                '#(\/\*(?>.*?\*\/))|(^|[\{\}])(?:[^\s\{\}]+)\{\}#s'
            ),
            array(
                '$1',
                '$1$2$3$4$5$6$7',
                '$1',
                ':0',
                '$1:0 0',
                '.$1',
                '$1$3',
                '$1$2$4$5',
                '$1$2$3',
                '$1:0',
                '$1$2'
            ),
        $input);

        return preg_replace(
            array(
                // t = text
                // o = tag open
                // c = tag close
                // Keep important white-space(s) after self-closing HTML tag(s)
                '#<(img|input)(>| .*?>)#s',
                // Remove a line break and two or more white-space(s) between tag(s)
                '#(<!--.*?-->)|(>)(?:\n*|\s{2,})(<)|^\s*|\s*$#s',
                '#(<!--.*?-->)|(?<!\>)\s+(<\/.*?>)|(<[^\/]*?>)\s+(?!\<)#s', // t+c || o+t
                '#(<!--.*?-->)|(<[^\/]*?>)\s+(<[^\/]*?>)|(<\/.*?>)\s+(<\/.*?>)#s', // o+o || c+c
                '#(<!--.*?-->)|(<\/.*?>)\s+(\s)(?!\<)|(?<!\>)\s+(\s)(<[^\/]*?\/?>)|(<[^\/]*?\/?>)\s+(\s)(?!\<)#s', // c+t || t+o || o+t -- separated by long white-space(s)
                '#(<!--.*?-->)|(<[^\/]*?>)\s+(<\/.*?>)#s', // empty tag
                '#<(img|input)(>| .*?>)<\/\1>#s', // reset previous fix
                '#(&nbsp;)&nbsp;(?![<\s])#', // clean up ...
                '#(?<=\>)(&nbsp;)(?=\<)#', // --ibid
                // Remove HTML comment(s) except IE comment(s)
                '#\s*<!--(?!\[if\s).*?-->\s*|(?<!\>)\n+(?=\<[^!])#s'
            ),
            array(
                '<$1$2</$1>',
                '$1$2$3',
                '$1$2$3',
                '$1$2$3$4$5',
                '$1$2$3$4$5$6$7',
                '$1$2$3',
                '<$1$2',
                '$1 ',
                '$1',
                ""
            ),
        $input);
    }
}