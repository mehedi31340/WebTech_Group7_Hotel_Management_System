<?php
require_once "../config/database.php"; require_login();
$name=trim($_POST['full_name']??''); $phone=trim($_POST['phone']??''); $nat=trim($_POST['nationality']??''); $idno=trim($_POST['id_number']??'');
$picture=null;
if(!empty($_FILES['profile_picture']['name']) && $_FILES['profile_picture']['error']===UPLOAD_ERR_OK){
  $allowed=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
  $mime=mime_content_type($_FILES['profile_picture']['tmp_name']);
  if(isset($allowed[$mime]) && $_FILES['profile_picture']['size'] <= 2*1024*1024){
    $dir=__DIR__.'/uploads'; if(!is_dir($dir)) mkdir($dir,0777,true);
    $file='guest_'.guest_id().'_'.time().'.'.$allowed[$mime];
    move_uploaded_file($_FILES['profile_picture']['tmp_name'],$dir.'/'.$file);
    $picture='uploads/'.$file;
  }
}
if($picture){
  $stmt=$conn->prepare("UPDATE guests SET full_name=?,phone=?,nationality=?,id_number=?,profile_picture=? WHERE id=?");
  $stmt->bind_param("sssssi",$name,$phone,$nat,$idno,$picture,$gid=guest_id());
}else{
  $stmt=$conn->prepare("UPDATE guests SET full_name=?,phone=?,nationality=?,id_number=? WHERE id=?");
  $stmt->bind_param("ssssi",$name,$phone,$nat,$idno,$gid=guest_id());
}
$stmt->execute(); $_SESSION['guest_name']=$name; header("Location:index.php?page=profile"); exit;
?>