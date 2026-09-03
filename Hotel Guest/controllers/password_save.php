<?php
require_once "../config/database.php"; require_login();
$stmt=$conn->prepare("SELECT password_hash FROM guests WHERE id=?"); $stmt->bind_param("i",$gid=guest_id()); $stmt->execute(); $u=$stmt->get_result()->fetch_assoc();
if($u && password_verify($_POST['current'],$u['password_hash']) && strlen($_POST['new'])>=6){
  $hash=password_hash($_POST['new'],PASSWORD_DEFAULT); $stmt=$conn->prepare("UPDATE guests SET password_hash=? WHERE id=?"); $stmt->bind_param("si",$hash,$gid); $stmt->execute();
}
header("Location:index.php?page=profile"); exit;
?>