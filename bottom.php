</td></tr>
</table>
<div align=center><?php
include_once 'config.php';
$rand = rand(0,count($copyright)-1);
echo $copyright[$rand];

?>
</div>

</body>
</html>
