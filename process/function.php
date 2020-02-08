<?php
include_once "common.php";
// binary executable command
define("OMPI","/usr/local/bin/ompicc "); // 弃用，有依赖问题
define("SWEET",getcwd()."/../omptg-tools/sweet ");
define("WCTG",getcwd()."/../omptg-tools/OmpTG/wctg ");
define("PYTHON","PYTHONIOENCODING=utf-8 /usr/bin/python3 ");
define("PCFG_GEN",PYTHON.getcwd()."/../omptg-tools/OmpTG/src/Preprocessing/graph.py ");
define("EFG_GEN",PYTHON.getcwd()."/../omptg-tools/OmpTG/src/EFGenerate/graph.py ");
define("TRIM_P",PYTHON.getcwd()."/../omptg-tools/trimming/main.py ");
define("CLANG",getcwd()."/../omptg-tools/ALFBackend/clang-3.4 ");
define("LLC",getcwd()."/../omptg-tools/ALFBackend/llc ");
define("OPT",getcwd()."/../omptg-tools/ALFBackend/opt ");
define("LLVM_DIS",getcwd()."/../omptg-tools/ALFBackend/llvm-dis ");

// regex
define("REG_THRFUNC","/_thrFunc[\d]+_/");

// tip messages
define("CONTACT"," Please contact us by E-mail(me@kingtous.cn) for help.");
define("UNSPPORRT_CONTACT","Server Overloaded or File may contain unsupported code.".CONTACT);
define("NOT_ENOUGH_FILE_FOUND","The file doesn't contain all the dependencies we need to use.");
define("NO_FILE_FOUND","no output file found, perhaps some error occured during processing. ");
define("LONG_RES","If Server has no response(perhaps it costs plenty of time), you could back to menu to check your files.");
define("IOError","Server error when processing files uploaded, please try again later.");
define("FileError","File Provided to OmpTG is Invalid.");

function command_c_ll($file_path){
    $head_path = dirname($file_path).'/'.basename($file_path,'.c');
    return CLANG." -g -gcolumn-info -Wall -emit-llvm -S -o - $file_path | ".OPT."-instnamer | ".LLVM_DIS." -o $head_path.ll";
}

function command_ll_alf_and_map($file_path){
    $head_path = dirname($file_path).'/'.basename($file_path,'.ll');
    return LLC." $file_path -march=alf -o=$head_path.alf -alf-map-file=$head_path.map";
}

function command_gen_pcfg($dotPath,$wctPath,$relPath){
    return PCFG_GEN."-d $dotPath -w $wctPath -r $relPath";
}

function command_gen_efg($dotPath,$wctPath,$relPath,$parseFunction){
    return EFG_GEN."-d $dotPath -w $wctPath -r $relPath -p $parseFunction";
}

// 重命名 + 下载重命名后的文件
function rename_and_show_download_btn($src,$dest,$value=''){
    // step.1 rename src -> dest (src == null) means dont do step.1
    // step.2 show download button
    if ($dest == null){
        return false;
    }
    if ($src != null){
        if (!rename($src,$dest))
            return false;
    }

    echo "<p><form action='../down.php'>";
    echo "<input type='hidden' name='file' value='  "   . $dest .   " ' />";
    echo "<input type='submit' value='Download $value'/>";
    echo "</form></p>";
    return true;

}

function show_home_btn(){
    echo "<p><form action='../index.php'>";
    echo "<input type='submit' value='Back to Menu / Check Files'/>";
    echo "</form></p>";
    return true;
}




