<?
require_once("oj-header.php");
require_once("./include/db_info.inc.php");
?>
<script src="include/sortTable.js"></script>
<?
$sql="SELECT max(`problem_id`) as upid FROM `problem`";
$page_cnt=100;
$result=mysql_query($sql);
echo mysql_error();
$row=mysql_fetch_object($result);
$cnt=intval($row->upid)-1000;
$cnt=$cnt/$page_cnt;

if (isset($_GET['page'])){
	$page=intval($_GET['page']);
}else $page="1";
$pstart=1000+$page_cnt*intval($page)-$page_cnt;
$pend=$pstart+$page_cnt;

$sub_arr=Array();
// submit
if (isset($_SESSION['user_id'])){
$sql="SELECT `problem_id` FROM `solution` WHERE `user_id`='".$_SESSION['user_id']."'".
	" AND `problem_id`>='$pstart'".
	" AND `problem_id`<'$pend'".
	" group by `problem_id`";
$result=@mysql_query($sql) or die(mysql_error());
while ($row=mysql_fetch_array($result))
	$sub_arr[$row[0]]=true;
}
$acc_arr=Array();
// ac
if (isset($_SESSION['user_id'])){
$sql="SELECT `problem_id` FROM `solution` WHERE `user_id`='".$_SESSION['user_id']."'".
	" AND `problem_id`>='$pstart'".
	" AND `problem_id`<'$pend'".
	" AND `result`=4".
	" group by `problem_id`";
$result=@mysql_query($sql) or die(mysql_error());
while ($row=mysql_fetch_array($result))
	$acc_arr[$row[0]]=true;
}

if (!isset($_SESSION['administrator'])){
	
           $sql0="SELECT `problem_id`,`title`,`source`,`submit`,`accepted`,`in_date` FROM `problem` ".
           "WHERE `defunct`='N' AND `problem_id` NOT IN(
		SELECT `problem_id` FROM `contest_problem` WHERE `contest_id` IN (
			SELECT `contest_id` FROM `contest` WHERE `end_time`>NOW() or private=1
		)
	) AND";
	$sql=$sql0."  `problem_id`>='".strval($pstart)."' AND `problem_id`<'".strval($pend)."' ";
}
else{
	
        $sql0="SELECT `problem_id`,`title`,`source`,`submit`,`accepted`,`in_date` FROM `problem` WHERE ";
       $sql=$sql0." `problem_id`>='".strval($pstart)."' AND `problem_id`<'".strval($pend)."' ";
}
if(isset($_GET['search'])){
    $search=trim(mysql_real_escape_string($_GET['search']));
    if($search!='')
     {
        $sql=$sql0." ( title like '%$search%'";
        $sql=$sql." or source like '%$search%')";
        //$sql=$sql." or problem_id='$search')";
	if (!isset($_SESSION['administrator'])){
        	$sql=$sql." and defunct='N'";
         }
    }
}
$sql=$sql." ORDER BY `problem_id`";
?>
<title>Problem Set</title>

<?
$result=mysql_query($sql) or die(mysql_error());
echo "<h3 align='center'>";
for ($i=1;$i<=$cnt+1;$i++){
	if ($i>1) echo '&nbsp;';
	if ($i==$page) echo "<span class=red>$i</span>";
	else echo "<a href='problemset.php?page=".$i."'>".$i."</a>";
}
$cnt=0;
echo "</h3>";
echo "<center><table id=problemset width=90%>";
//echo "<thead><tr align=center class='evenrow'><td width=5><td width=100% colspan=5><form>Title、Source or ID<input type=text name=search><input type=submit value=GO ></form> </tr>";
echo "<thead><tr align='center' class='evenrow'><td width='5'></td>";
echo "<td width='90%' colspan='2'><form>Title or Source<input type='text' name='search'><input type='submit' value='$MSG_SEARCH' ></form></td>";

echo "<td width='10%' colspan='2'><form action=problem.php>Problem ID<input type='text' name='id' size=5><input type='submit' value='GO' ></form></td>";
echo "</tr><tr align=center class='toprow'>";



if(isset($_GET['search']))
    echo "<tr align=center class='toprow'><td width=3%><td width=10%>Problem ID<td width=40%>Title<td width=7%>AC<td width=40%>Source</tr>";

else
   echo "<tr align=center class='toprow'><td width=3%><td width=10%>Problem ID<td width=55%>Title<td width=14%>Ratio(AC/Submit)<td width=7%>Difficulty<td width=26%>Date</tr>";
$cnt=0;
while ($row=mysql_fetch_object($result)){
        if ($cnt) echo "<tr class='oddrow'>";
        else echo "<tr class='evenrow'>";
        echo "<td>";
        if (isset($sub_arr[$row->problem_id])){
                if (isset($acc_arr[$row->problem_id])) echo "<font color=green>Y</font>";
                else echo "<font color=red>N</font>";
        }
        echo "<td align=center>".$row->problem_id;
        echo "<td align=left><a href='problem.php?id=".$row->problem_id."'>".$row->title."</a>";
       // echo "<td align=left>".$row->source;
       if(isset($_GET['search'])){
         echo "<td align=center><a href='status.php?problem_id=".$row->problem_id."&jresult=4'>".$row->accepted."</a>";
         echo "<td align=center>".$row->source;
       }
       else
       {
       echo "<td align=center>";
            if ($row->submit==0)
              echo sprintf("%.00lf%%",$row->submit);
            else
             echo  sprintf ( "%.00lf%%", 100 *( $row->accepted / $row->submit) );
          echo  "(<a href='status.php?problem_id=".$row->problem_id."&jresult=4'>"
                .$row->accepted."</a>/<a href='status.php?problem_id=".$row->problem_id."'>".$row->submit."</a>)";
        echo "<td align=center>";
            if($row->submit==0)
             echo sprintf("%.00lf%%",100*(1-$row->submit));
            else
                echo sprintf ( "%.00lf%%", 100 *( 1-$row->accepted / $row->submit) );
        echo "<td align=center>";
        echo substr($row->in_date,0,10);
        }
        echo "</tr>";
        $cnt=1-$cnt;
}

mysql_free_result($result);



echo "</tbody>";

echo "</table></center>";

?>

<?
$sql="SELECT max(`problem_id`) as upid FROM `problem`";
$page_cnt=100;
$result=mysql_query($sql);
echo mysql_error();
$row=mysql_fetch_object($result);
$cnt=intval($row->upid)-1000;
$cnt=$cnt/$page_cnt;
echo "<h3 align='center'>";
for ($i=1;$i<=$cnt+1;$i++){
	if ($i>1) echo '&nbsp;';
	if ($i==$page) echo "<span class=red>$i</span>";
	else echo "<a href='problemset.php?page=".$i."'>".$i."</a>";
}
echo "</h3>";

?>


<?require_once("oj-footer.php")?>
