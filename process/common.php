<?php
// 所写的一些通用函数，可以用在任何项目

function show_process_animation(){
    echo '<div class="spinner" id="process_animation">';
    echo '<div class="bounce1"></div>';
    echo '<div class="bounce2"></div>';
    echo '<div class="bounce3"></div>';
    echo '</div>';
}

function create_temp_folder_for_current_id(){
    
    $pPath = sys_get_temp_dir() . '/phptemp/' . session_id(); // 为当前session创建的处理文件夹，处理操作都在这个文件夹内完成
    $phptemp = sys_get_temp_dir() . '/phptemp/';
    if (file_exists($phptemp) || mkdir($phptemp)){
    	if (file_exists($pPath) || mkdir($pPath)) {
        	return true;
    	}
    	else return false;
    } else {
    	return false;
    }
}


// 获取文件列表
function getFileList($directory) {
    $files = array();
    if(is_dir($directory)) {
        if($dh = opendir($directory)) {
            while(($file = readdir($dh)) !== false) {
                if($file != '.' && $file != '..') {
                    $files[] = $file;
                }
            }
            closedir($dh);
        }
    }
    return $files;
}

// 递归删除相应目录
function delDir($directory){//自定义函数递归的函数整个目录
    if(file_exists($directory)){//判断目录是否存在，如果不存在rmdir()函数会出错
        if($dir_handle=@opendir($directory)){//打开目录返回目录资源，并判断是否成功
            while($filename=readdir($dir_handle)){//遍历目录，读出目录中的文件或文件夹
                if($filename!='.' && $filename!='..'){//一定要排除两个特殊的目录
                    $subFile=$directory."/".$filename;//将目录下的文件与当前目录相连
                    if(is_dir($subFile)){//如果是目录条件则成了
                        delDir($subFile);//递归调用自己删除子目录
                    }
                    if(is_file($subFile)){//如果是文件条件则成立
                        unlink($subFile);//直接删除这个文件
                    }
                }
            }
            closedir($dir_handle);//关闭目录资源
            rmdir($directory);//删除空目录
        }
    }
}
function showError($message){
    echo '<br>' . 'Error:' . $message . '</br>';
}

function getExt($path){
    return pathinfo($path,PATHINFO_EXTENSION);
}

function showMessage($sentence){
    echo str_repeat(" ",4096).'<p>'.$sentence.'</p>';
}


function my_exec($session_id,$command){
    $descriptorspec = array(
        0 => array("pipe", "r"),  // 标准输入，子进程从此管道中读取数据
        1 => array("pipe", "w"),  // 标准输出，子进程向此管道中写入数据
        2 => array("file", "/tmp/phptemp/$session_id/shell-output.txt", "a") // 标准错误，写入到一个文件
    );

    $cwd = '/tmp';
    $env = array('some_option' => 'aeiou');

    $process = proc_open($command, $descriptorspec, $pipes, $cwd, $env);
    if (is_resource($process)) {
        // $pipes 现在看起来是这样的：
        // 0 => 可以向子进程标准输入写入的句柄
        // 1 => 可以从子进程标准输出读取的句柄
        // 错误输出将被追加到文件 /tmp/error-output.txt

        fwrite($pipes[0], '<?php print_r($_ENV); ?>');
        fclose($pipes[0]);

        echo stream_get_contents($pipes[1]);
        fclose($pipes[1]);


        // 切记：在调用 proc_close 之前关闭所有的管道以避免死锁。
        $return_value = proc_close($process);

        if ($return_value == 0){
            // 可以显示OK
//            showMessage("command OK.");
        }
        else
            showMessage("command returned $return_value");
        return $return_value;
    }
}

function get_suffix($file){
    // aaa.zip -> zip
    return substr(strrchr($file, '.'), 1);
}


function check_suffix($suffix,$file){
    return get_suffix($file)==$suffix;
}

function set_options_in_form($id,$name,$value,$message){
    /*
     * $id : select id
     * $name : select name
     * $value : <option value="XXXXXXXXXXX">
     * $message : <option value="xxx">message</option>
     */
    try{
        echo "<select id=$id name=$name>";
        $length = count($value);
        for ($i=0;$i < $length ;$i++){
            echo "<option value=$value[$i]>$message[$i]</option>";
        }
        echo "</select>";
        return true;
    }catch (Exception $e){
        return false;
    }

}
