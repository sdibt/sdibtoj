<?session_start();?>
<title>Submit Code</title>
<?
if (!isset($_SESSION['user_id'])){
	echo "<a href=loginpage.php>Please Login First</a>";
	require_once("oj-footer.php");
	exit(0);
}
if (isset($_GET['id'])){
	$id=intval($_GET['id']);
	require_once("oj-header.php");
}else if (isset($_GET['cid'])&&isset($_GET['pid'])){
	require_once("contest-header.php");
	$cid=intval($_GET['cid']);$pid=intval($_GET['pid']);
}
else{
	echo "<h2>No Such Problem!</h2>";
	require_once("oj-footer.php");
	exit(0);
}
?>
<center>
<?php
if($OJ_EDITE_AREA){
?>
<script language="Javascript" type="text/javascript" src="edit_area/edit_area_full.js"></script>
<script language="Javascript" type="text/javascript">

editAreaLoader.init({
                id: "source"
                ,start_highlight: true
                ,allow_resize: "both"
                ,allow_toggle: true
                ,word_wrap: true
                ,language: "en"
                ,syntax: "cpp"
                        ,font_size: "10"
                ,syntax_selection_allow: "basic,c,cpp,java,pas"
                        ,toolbar: "search, go_to_line, fullscreen, |, undo, redo, |, select_font,syntax_selection,|, change_smooth_selection, highlight, reset_highlight, word_wrap, |, help"
        });
</script>
<?php }?>




<script src="include/checksource.js">

</script>
<form action="submit.php" method="post"  onsubmit="return checksource(document.getElementById('source').value);">
<?if (isset($id)){?>
Problem <font color=blue><b><?=$id?></b></font><br>
<input type='hidden' value='<?=$id?>' name="id">
<?
}else{
$PID="ABCDEFGHIJKLMNOPQRSTUVWXYZ";
if ($pid>25) $pid=25;
?>
Problem <font color=blue><b><?=$PID[$pid]?></b></font> of Contest <font color=blue><b><?=$cid?></b></font><br>
<input type='hidden' value='<?=$cid?>' name="cid">
<input type='hidden' value='<?=$pid?>' name="pid">
<?}?>
Language:
<select id="language" name="language">
<? 
  
if(isset($_GET['langmask']))
   $langmask=$_GET['langmask'];
else
  $langmask=$OJ_LANGMASK;


$lang=(~((int)$langmask))&15;
 $C_=($lang&1)>0;
 $CPP_=($lang&2)>0;
 $P_=($lang&4)>0;
 $J_=($lang&8)>0;
 if(isset($_COOKIE['lastlang'])) $lastlang=$_COOKIE['lastlang'];
 else $lastlang=1;
 
 if($C_) echo"	    <option value=0 ".( $lastlang==0?"selected":"").">C</option>";
 if($CPP_) echo"	<option value=1 ".( $lastlang==1?"selected":"").">C++</option>";
 if($P_) echo"		<option value=2 ".( $lastlang==2?"selected":"").">Pascal</option>";
 if($J_) echo"		<option value=3 ".( $lastlang==3?"selected":"").">Java</option>";
?>
</select>
<br>
<?php
$src="";

if(isset($_GET['sid'])){
          $sid=intval($_GET['sid']);
          $sql="SELECT * FROM `solution` WHERE `solution_id`=".$sid;
          $result=mysql_query($sql);
          $row=mysql_fetch_object($result);
          if ($row && $row->user_id==$_SESSION['user_id']) $ok=true;
          if (isset($_SESSION['source_browser'])) $ok=true;
          mysql_free_result($result);
          if ($ok==true){
             $sql="SELECT `source` FROM `source_code` WHERE `solution_id`='".$sid."'";
          $result=mysql_query($sql);
          $row=mysql_fetch_object($result);
          $src=$row->source;
          mysql_free_result($result);
         }
   }

 
 
 ?>
<textarea cols=80 rows=20 id="source" name="source"><?=$src?></textarea><br>





<input type=submit value="Submit">
<input type=reset value="Reset">
</form>
</center>
<?require_once("oj-footer.php")?>

