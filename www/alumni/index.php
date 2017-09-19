<?
	$date_rx = "!^(\d{4})-(\d{2})$!";

	list($this_y, $this_m) = explode('-', date('Y-m'));
	$next_m = mktime(0,0,0,$this_m+1,1,$this_y);

	$rows = array();

	$lines = file('data.txt');
	array_shift($lines);

	foreach ($lines as $line){
		$fields = explode(',', $line);
		$pres = 0;
		if ($fields[2] == 'present'){
			$fields[2] = date('Y-m', $next_m);
			$pres = 1;
		}
		if (preg_match($date_rx, $fields[1]) && preg_match($date_rx, $fields[2])){

			$rows[] = array(
				'name'		=> $fields[0],
				'joined'	=> $fields[1],
				'left'		=> $fields[2],
				'still'		=> $pres,
			);
		}else if ($fields[0]){
			$odds[] = "$fields[0] : $fields[1] - $fields[2] $fields[3]";
		}
	}

	# build dates index
	$dates = array();
	$idx = 0;
	$m = 12;
	$y = 2003;
	list($last_y, $last_m) = explode('-', date('Y-m', $next_m));

	while (1){
		$dates[sprintf('%04d-%02d', $y, $m)] = $idx;
		if ($last_y == $y && $last_m == $m) break;
		$idx++;
		$m++;
		if ($m == 13){
			$y++;
			$m = 1;
		}
	}

	$max_idx = $idx;

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<title>Flickr Alumni</title>
<style>

body {
	font-family: Arial, helvetica, sans-serif;
}

.prepad {
	float: left;
	height: 16px;
	xbackground-color: pink;
}
.tenure {
	float: left;
	height: 16px;
	background-color: green;
	-moz-border-radius: 15px;
	border-radius: 15px;
}
.still {
	background-color: blue;
}

</style>
</head>
<body>

<table border="1">
<?
	foreach ($rows as $row){

		echo "<tr>\n";
		echo "<td>".str_replace(' ', '&nbsp;', HtmlSpecialChars($row['name']))."</td>\n";
		echo "<td width=\"100%\">";

		$start_idx = $dates[$row['joined']];
		$end_idx = $dates[$row['left']];

		$percent_start = 100 * ($start_idx / $max_idx);
		$percent_end = (100 * ($end_idx / $max_idx)) - $percent_start;

		$class = $row['still'] ? 'tenure still' : 'tenure';

		echo "<div class=\"prepad\" style=\"width: {$percent_start}%\"></div>";
		echo "<div class=\"$class\" style=\"width: {$percent_end}%\"></div>";

		echo "</td>\n";

		$months = 1 +($end_idx - $start_idx);
		echo "<td>$months</td>";

		echo "</tr>\n";
	}
?>
</table>

<p>Missing dates:<br />
<? foreach ($odds as $line) echo " * ".HtmlSpecialChars($line)."<br />"; ?>
</p>

</body>
</html>
