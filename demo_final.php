<?php
	require('pdo_mysql_class.php');//neededed to import the custom PDO_MySQL class into the program
	$db = new PDO_MySQL();//create a new instance of the class
	$db->connect("localhost","root","root","pokemon");//use the connect function
?>
<!doctype html>
<html>
	<head>
		<title>A PHP PDO demo</title>
		<link rel="stylesheet" type="text/css" href="markdown_styles.css"/>
		<link rel="stylesheet" type="text/css" href="styles.css" />
	</head>
	<body>
		<h1>A PDO for MySQL Demo</h1>
		<ul id="nav" class="trail">
			<li><a href="index.html">Introduction</a></li>
			<li><a href="demo_old_way.php">The mysqli_* old method</a></li>
			<li><a href="demo_new_way.php">The PHP PDO new method</a></li>
			<li><a href="demo_better_way.php">The PDO_MySQL custom PHP class</a></li>
			<li class="active"><a href="demo_final.php">Completed demo</a></li>
			<li><a href="demo_conclusion.html">Conclusion</a></li>
		</ul>
		<h2>Completed PHP PDO for MySQL Demo</h2>
		<p>
			This is the completed form of this demo using my custom PDO_MySQL class for connecting to a MySQL database and querying using prepared statements.
			<br>
			Also introduced are just a few of its handy utilities and functionality I have provided within the class.
			<br>
			Also note, there is some JavaScript used in this example to get the sounds to play when the pokemon sprites are clicked.
			<br>
			<em>Turn your sound up!</em>
		<form action="demo_final.php" method="get">
			<label for="pkmn_name">Pokemon Name: </label>
			<input id="pkmn_name" type="text" name="pkmn_name" value="<?php if(isset($_GET['pkmn_name'])) echo($_GET['pkmn_name']); ?>" />
			<input type="submit" name="" value="Search">
		</form>
		<audio id="cry" src='' ></audio>
		<h3>Matching Species:</h3>
		<table>
			<?php
				//check to make sure the database is connected before continuing and attempting to query it
				if($db->is_connected()){
					if(isset($_GET['pkmn_name'])){
						//get the user's input from the GET global array
						//need to include the wildcard characters as the input to the query
						$name = "%".$_GET['pkmn_name']."%";
						//define the query
						$query = "SELECT SPECIES_CRY_BLOB, SPECIES_IMG_BLOB, SPECIES_ID, SPECIES_NAME, TYPE1, TYPE2 FROM SPECIES WHERE SPECIES_NAME LIKE ?";
						//query the database using the query_prepared() function to have it automatically prepare the SQL statement
						//and pass it an array of the variables that match the ? in the statement in the same order as they appear
						$result = $db->query_prepared($query,[$name]);
						//print out to the user the number of matched results
						echo("<h4>".$db->num_rows($result)." results found</h4>");
						//use the has_rows() function to determine if the query successfully returned any results
						if($db->has_rows($result)){
							//use the field_names() function to get an array of the column names in the database that were returned by the associative array when queried
							$fields = $db->field_names($result);
							echo("<thead>");
							//write out the table header using the field names
							for($i=1;$i<sizeof($fields);$i++){
								$fieldname = $fields[$i];
								echo("<th>$fieldname</th>");
							}
							echo("</thead>");
							echo("<tbody>");
							//while there are still rows left in the result set, continue iterating and writing out the table rows with the data from the record
							while($row = $db->fetch_row($result)){
								echo("<tr>");
								echo( "<td><img class='species_img' src='data:image/png;base64,".base64_encode($row['SPECIES_IMG_BLOB'])."' data-wav='".base64_encode($row['SPECIES_CRY_BLOB'])."' alt='{$row['SPECIES_NAME']}' draggable='false'/></td>");
								$j = 0;
								foreach($row as $item){
									if($j > 1){
										$field = $item;
										if($field=="") $field = "(none)";
										echo("<td>$field</td>");
									}
									$j++;
								}//end foreach
								echo("</tr>");
							}//end while
							echo("</tbody>");
						}//end has_rows
					}//end isset
				}//end is_connected
			?>
		</table>
		<div id="markup">
		<pre><code class="language-php"><span class="hljs-preprocessor">&lt;?php</span>
    <span class="hljs-keyword">require</span>(<span class="hljs-string">'pdo_mysql_class.php'</span>);<span class="hljs-comment">//neededed to import the custom PDO_MySQL class into the program</span>
    <span class="hljs-variable">$db</span> = <span class="hljs-keyword">new</span> PDO_MySQL();<span class="hljs-comment">//create a new instance of the class.</span>
    <span class="hljs-variable">$db</span>-&gt;connect(<span class="hljs-string">"localhost"</span>,<span class="hljs-string">"username"</span>,<span class="hljs-string">"password"</span>,<span class="hljs-string">"pokemon"</span>);<span class="hljs-comment">//use the connect function</span>
<span class="hljs-preprocessor">?&gt;</span>
<span class="hljs-preprocessor">&lt;?php</span>
    <span class="hljs-comment">//check to make sure the database is connected before continuing and attempting to query it</span>
    <span class="hljs-keyword">if</span>(<span class="hljs-variable">$db</span>-&gt;is_connected()){
        <span class="hljs-keyword">if</span>(<span class="hljs-keyword">isset</span>(<span class="hljs-variable">$_GET</span>[<span class="hljs-string">'pkmn_name'</span>])){
            <span class="hljs-comment">//get the user's input from the GET global array</span>
            <span class="hljs-comment">//need to include the wildcard characters as the input to the query</span>
            <span class="hljs-variable">$name</span> = <span class="hljs-string">"%"</span>.<span class="hljs-variable">$_GET</span>[<span class="hljs-string">'pkmn_name'</span>].<span class="hljs-string">"%"</span>;
            <span class="hljs-comment">//define the query</span>
            <span class="hljs-variable">$query</span> = <span class="hljs-string">"SELECT SPECIES_CRY_BLOB, SPECIES_IMG_BLOB, SPECIES_ID, SPECIES_NAME, TYPE1, TYPE2 FROM SPECIES WHERE SPECIES_NAME LIKE ?"</span>;
            <span class="hljs-comment">//query the database using the query_prepared() function to have it automatically prepare the SQL statement</span>
            <span class="hljs-comment">//and pass it an array of the variables that match the ? in the statement in the same order as they appear</span>
            <span class="hljs-variable">$result</span> = <span class="hljs-variable">$db</span>-&gt;query_prepared(<span class="hljs-variable">$query</span>,[<span class="hljs-variable">$name</span>]);
            <span class="hljs-comment">//print out to the user the number of matched results</span>
            <span class="hljs-keyword">echo</span>(<span class="hljs-string">"&lt;h4&gt;"</span>.<span class="hljs-variable">$db</span>-&gt;num_rows(<span class="hljs-variable">$result</span>).<span class="hljs-string">" resutls found&lt;/h4&gt;"</span>);
            <span class="hljs-comment">//use the has_rows() function to determine if the query successfully returned any results</span>
            <span class="hljs-keyword">if</span>(<span class="hljs-variable">$db</span>-&gt;has_rows(<span class="hljs-variable">$result</span>)){
                <span class="hljs-comment">//use the field_names() function to get an array of the column names in the database that were returned by the associative array when queried</span>
                <span class="hljs-variable">$fields</span> = <span class="hljs-variable">$db</span>-&gt;field_names(<span class="hljs-variable">$result</span>);
                <span class="hljs-keyword">echo</span>(<span class="hljs-string">"&lt;thead&gt;"</span>);
                <span class="hljs-comment">//write out the table header using the field names</span>
                <span class="hljs-keyword">for</span>(<span class="hljs-variable">$i</span>=<span class="hljs-number">1</span>;<span class="hljs-variable">$i</span>&lt;sizeof(<span class="hljs-variable">$fields</span>);<span class="hljs-variable">$i</span>++){
                    <span class="hljs-variable">$fieldname</span> = <span class="hljs-variable">$fields</span>[<span class="hljs-variable">$i</span>];
                    <span class="hljs-keyword">echo</span>(<span class="hljs-string">"&lt;th&gt;$fieldname&lt;/th&gt;"</span>);
                }
                <span class="hljs-keyword">echo</span>(<span class="hljs-string">"&lt;/thead&gt;"</span>);
                <span class="hljs-keyword">echo</span>(<span class="hljs-string">"&lt;tbody&gt;"</span>);
                <span class="hljs-comment">//while there are still rows left in the result set, continue iterating and writing out the table rows with the data from the record</span>
                <span class="hljs-keyword">while</span>(<span class="hljs-variable">$row</span> = <span class="hljs-variable">$db</span>-&gt;fetch_row(<span class="hljs-variable">$result</span>)){
                    <span class="hljs-keyword">echo</span>(<span class="hljs-string">"&lt;tr&gt;"</span>);
                    <span class="hljs-keyword">echo</span>( <span class="hljs-string">"&lt;td&gt;&lt;img class='species_img' src='data:image/png;base64,"</span>.base64_encode(<span class="hljs-variable">$row</span>[<span class="hljs-string">'SPECIES_IMG_BLOB'</span>]).<span class="hljs-string">"' data-wav='"</span>.base64_encode(<span class="hljs-variable">$row</span>[<span class="hljs-string">'SPECIES_CRY_BLOB'</span>]).<span class="hljs-string">"' alt='{$row['SPECIES_NAME']}' draggable='false'/&gt;&lt;/td&gt;"</span>);
                    <span class="hljs-variable">$j</span> = <span class="hljs-number">0</span>;
                    <span class="hljs-keyword">foreach</span>(<span class="hljs-variable">$row</span> <span class="hljs-keyword">as</span> <span class="hljs-variable">$item</span>){
                        <span class="hljs-keyword">if</span>(<span class="hljs-variable">$j</span> &gt; <span class="hljs-number">1</span>){
                            <span class="hljs-variable">$field</span> = <span class="hljs-variable">$item</span>;
                            <span class="hljs-keyword">if</span>(<span class="hljs-variable">$field</span>==<span class="hljs-string">""</span>) <span class="hljs-variable">$field</span> = <span class="hljs-string">"(none)"</span>;
                            <span class="hljs-keyword">echo</span>(<span class="hljs-string">"&lt;td&gt;$field&lt;/td&gt;"</span>);
                        }
                        <span class="hljs-variable">$j</span>++;
                    }<span class="hljs-comment">//end foreach</span>
                    <span class="hljs-keyword">echo</span>(<span class="hljs-string">"&lt;/tr&gt;"</span>);
                }<span class="hljs-comment">//end while</span>
                <span class="hljs-keyword">echo</span>(<span class="hljs-string">"&lt;/tbody&gt;"</span>);
            }<span class="hljs-comment">//end has_rows</span>
        }<span class="hljs-comment">//end isset</span>
    }<span class="hljs-comment">//end is_connected</span>
<span class="hljs-preprocessor">?&gt;</span>
&lt;/table&gt;
</code></pre>
<pre><code class="language-html"><span class="hljs-tag">&lt;<span class="hljs-title">script</span> <span class="hljs-attribute">type</span>=<span class="hljs-value">"text/javascript"</span>&gt;</span><span class="javascript">
<span class="hljs-keyword">var</span> sprites = <span class="hljs-built_in">document</span>.getElementsByClassName(<span class="hljs-string">'species_img'</span>);
<span class="hljs-keyword">var</span> audioSrc = <span class="hljs-built_in">document</span>.getElementById(<span class="hljs-string">'#cry'</span>);
<span class="hljs-keyword">for</span>(<span class="hljs-keyword">var</span> i=<span class="hljs-number">0</span>;i&lt;sprites.length;i++){
    sprites[i].addEventListener(<span class="hljs-string">'click'</span>,<span class="hljs-function"><span class="hljs-keyword">function</span>(<span class="hljs-params">e</span>)</span>{
        <span class="hljs-keyword">var</span> data = e.target.getAttribute(<span class="hljs-string">'data-wav'</span>);
        cry.setAttribute(<span class="hljs-string">'src'</span>,<span class="hljs-string">'data:audio/wav;base64,'</span>+data);
        cry.play();
    });
}
</span><span class="hljs-tag">&lt;/<span class="hljs-title">script</span>&gt;</span>
</code></pre>
		</div>
		<script type="text/javascript">
			var sprites = document.getElementsByClassName('species_img');
			var audioSrc = document.getElementById('#cry');
			for(var i=0;i<sprites.length;i++){
				sprites[i].addEventListener('click',function(e){
					var data = e.target.getAttribute('data-wav');
					cry.setAttribute('src','data:audio/wav;base64,'+data);
					cry.play();
				});
			}
		</script>
	</body>
</html>