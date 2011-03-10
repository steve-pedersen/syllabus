<?php

/**
 * Compiler class for compiling CSS and JS files
 */
class Compiler {

    
    /**
     * Wrapper method for compiling the CSS string.
     * @return string HTML string of the CSS <link> tag(s)
     */
    public function compileCss($css_array) {
        return self::compile('css', $css_array);
    }

    
    /**
     * Wrapper method for compiling the JS string.
     * @return string HTML string of the JS <script> tag(s)
     */
    public function compileJs($js_array) {
        return self::compile('js', $js_array);
    }
    
    
    /**
     * Do the actual compiling. Only local files will be added to the compiled file.
     * Non-local files should be included manually in the template.
     * @param string $type The type of compile to run
     * @param array $path_array The array of paths for the files that should be compiled
     */
    private function compile($type, $path_array) {
        switch($type) {
            case 'css':
                $do_compile = CSS_COMPILE;
                $dir = CSS_COMPILE_DIR;
                $file = CSS_COMPILE_FILE;
                $html = '<link type="text/css" href="%s" rel="stylesheet" />';
                break;
            
            case 'js':
                $do_compile = JS_COMPILE;
                $dir = JS_COMPILE_DIR;
                $file = JS_COMPILE_FILE;
                $html = '<script src="%s"></script>';
                break;
            
            default: break;
        }
        
        $compiled_html = "\n" . sprintf($html, $dir . $file);
        $uncompiled_html = '';
        
        foreach($path_array as $k => $v) {
            $filename = preg_replace('!\s!', '', $v);
            $path_array[$k] = $filename;
            if(empty($filename)) {
                unset($path_array[$k]);
            } else {
                $uncompiled_html .= "\n" . sprintf($html, $filename);
            }
        }
        
        if($do_compile) {
            $valid_compile = true;
            
            if(!file_exists(SERVER_ROOT . $dir . $file)) {
                $compiled_string = '';
                foreach($path_array as $k => $v) {
                    if($f = file_get_contents($v)) {
                        $compiled_string .= $f;
                    } else {
                        $valid_compile = false;
                    }
                }
                
                if($valid_compile) {
                    if(!file_put_contents(SERVER_ROOT . $dir . $file, $compiled_string)) {
                        $valid_compile = false;
                    }
                }
            }
        }
        
        $return = (isset($valid_compile) && $valid_compile ==  true)
            ? $compiled_html
            : $uncompiled_html;
            
        return $return;
    }
    
}