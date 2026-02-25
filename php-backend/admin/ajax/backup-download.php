<?php
/**
 * Backup & Download Handler — Super Admin Only
 * Supports: database, files, full backup downloads
 */
require_once __DIR__.'/../../includes/auth.php';
requireAdmin();
if(!isSuperAdmin()){http_response_code(403);die('Forbidden');}

// CSRF check via GET token
$token=$_GET['token']??'';
if(!$token||!hash_equals($_SESSION['csrf_token']??'',$token)){http_response_code(403);die('Invalid CSRF token');}

$type=$_GET['type']??'';
$date=date('Y-m-d');
$db=getDB();

/**
 * Generate SQL dump of all tables
 */
function generateSqlDump($db){
    $sql="-- JNV School Database Backup\n";
    $sql.="-- Generated: ".date('Y-m-d H:i:s')."\n";
    $sql.="-- Server: ".DB_HOST." | Database: ".DB_NAME."\n";
    $sql.="SET NAMES utf8mb4;\nSET FOREIGN_KEY_CHECKS=0;\n\n";

    $tables=$db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    foreach($tables as $table){
        // DROP + CREATE
        $sql.="-- Table: $table\n";
        $sql.="DROP TABLE IF EXISTS `$table`;\n";
        $create=$db->query("SHOW CREATE TABLE `$table`")->fetch();
        $sql.=$create['Create Table'].";\n\n";

        // INSERT data
        $rows=$db->query("SELECT * FROM `$table`")->fetchAll(PDO::FETCH_ASSOC);
        if(!empty($rows)){
            $cols=array_keys($rows[0]);
            $colList='`'.implode('`,`',$cols).'`';
            // Batch inserts in chunks of 100
            $chunks=array_chunk($rows,100);
            foreach($chunks as $chunk){
                $sql.="INSERT INTO `$table` ($colList) VALUES\n";
                $vals=[];
                foreach($chunk as $row){
                    $escaped=array_map(function($v) use ($db){
                        if($v===null) return 'NULL';
                        return $db->quote($v);
                    },array_values($row));
                    $vals[]='('.implode(',',$escaped).')';
                }
                $sql.=implode(",\n",$vals).";\n";
            }
            $sql.="\n";
        }
    }
    $sql.="SET FOREIGN_KEY_CHECKS=1;\n";
    return $sql;
}

/**
 * Recursively add directory to ZipArchive
 */
function addDirToZip($zip,$dir,$base){
    $files=new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir,RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::LEAVES_ONLY
    );
    foreach($files as $file){
        if(!$file->isFile()) continue;
        $filePath=$file->getRealPath();
        $relativePath=$base.'/'.substr($filePath,strlen($dir)+1);
        $zip->addFile($filePath,$relativePath);
    }
}

try{
    if($type==='database'){
        // Database SQL dump
        $sql=generateSqlDump($db);
        $filename="jnvschool_db_backup_{$date}.sql";
        auditLog('backup_database','system',null,'Database backup downloaded');
        header('Content-Type: application/sql');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        header('Content-Length: '.strlen($sql));
        echo $sql;
        exit;
    }
    elseif($type==='files'){
        // Uploads folder ZIP
        $uploadsDir=realpath(__DIR__.'/../../uploads');
        if(!$uploadsDir||!is_dir($uploadsDir)){die('Uploads directory not found.');}

        $tmpFile=tempnam(sys_get_temp_dir(),'backup_');
        $zip=new ZipArchive();
        if($zip->open($tmpFile,ZipArchive::CREATE|ZipArchive::OVERWRITE)!==true){die('Cannot create ZIP.');}
        addDirToZip($zip,$uploadsDir,'uploads');
        $zip->close();

        $filename="jnvschool_files_backup_{$date}.zip";
        auditLog('backup_files','system',null,'Files backup downloaded');
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        header('Content-Length: '.filesize($tmpFile));
        readfile($tmpFile);
        unlink($tmpFile);
        exit;
    }
    elseif($type==='full'){
        // Full backup: SQL + uploads in one ZIP
        $uploadsDir=realpath(__DIR__.'/../../uploads');
        if(!$uploadsDir||!is_dir($uploadsDir)){die('Uploads directory not found.');}

        $tmpFile=tempnam(sys_get_temp_dir(),'backup_full_');
        $zip=new ZipArchive();
        if($zip->open($tmpFile,ZipArchive::CREATE|ZipArchive::OVERWRITE)!==true){die('Cannot create ZIP.');}

        // Add SQL dump
        $sql=generateSqlDump($db);
        $zip->addFromString("jnvschool_db_backup_{$date}.sql",$sql);

        // Add uploads
        addDirToZip($zip,$uploadsDir,'uploads');
        $zip->close();

        $filename="jnvschool_full_backup_{$date}.zip";
        auditLog('backup_full','system',null,'Full backup downloaded');
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        header('Content-Length: '.filesize($tmpFile));
        readfile($tmpFile);
        unlink($tmpFile);
        exit;
    }
    else{
        http_response_code(400);
        die('Invalid backup type. Use: database, files, or full');
    }
}catch(Exception $e){
    http_response_code(500);
    die('Backup failed: '.$e->getMessage());
}
