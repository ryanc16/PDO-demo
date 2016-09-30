<?php
	//define our connection settings
	$host = "localhost";
	$dbuser = "root";
	$dbpass = "root";
	$dbname = "pokemon";
	//create a new instance of the PDO class by stating we want the mysql: driver
	//also it needs our connection settings
	$db = new PDO('mysql:host='.$host.';dbname='.$dbname.';charset=utf8', $dbuser, $dbpass);
	//set the error mode to exception so they can be used with try/catch
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	//set the emulate prepares to false so it actually uses prepared statements
	$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
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
			<li class="active"><a href="demo_new_way.php">The PHP PDO new method</a></li>
			<li><a href="demo_better_way.php">The PDO_MySQL custom PHP class</a></li>
			<li><a href="demo_final.php">Completed demo</a></li>
			<li><a href="demo_conclusion.html">Conclusion</a></li>
		</ul>
		<h2>The PHP PDO new method</h2>
		<p>
			This demonstrates the syntax of the PDO interface for MySQL and its usage to query the database for pokemon that are similar to what the user types into the box.
			<br>
			You will notice that for pokemon names, such as Pikachu for example, will work just as well as a name like Farfetch'd. Now yype just a single quote and see what happens.
			<br>
			This works because we have prepared the SQL statement by telling the database to only treat this input as strictly input and not as additional SQL.
			<br>
			Again, try entering: <code>' OR 1=1;-- </code> (include a space at the end)<br><br>
			
			You will notice this <strong>does not work</strong>, which is good!. This is because it looks for pokemon names that look like exactly like that and there of course are none!
			<br>
			To see a better, <em>err rather more intuitive</em>, way to use the PDO interface, see my custom PDO_MySQL class which accesses the PDO class using similar methods, structure, and logic flow as in the older mysqli_* method, which many people are accustom to.
			<br>
			It also has some additional little useful utilities for more advanced web app setups.
			<br><a href="demo_better_way.php" class="btn-simple">See custom PDO_MySQL Example</a>
		</p>
		<form action="demo_new_way.php" method="get">
			<label for="pkmn_name">Pokemon Name: </label>
			<input id="pkmn_name" type="text" name="pkmn_name" value="<?php if(isset($_GET['pkmn_name'])) echo($_GET['pkmn_name']); ?>" />
			<input type="submit" name="" value="Search">
		</form>
		<strong>The complete query: </strong>
		<?php
			$query = "SELECT SPECIES_NAME, TYPE1 FROM SPECIES WHERE SPECIES_NAME LIKE ?";
			echo("<span class='sql'>".$query."</span>");
		?>
		<h3>Matching Species:</h3>
		<table>
			<?php
				//check to see if the database is connected before continuing and attempting to query it
				if($db){
					if(isset($_GET['pkmn_name'])){
						//get the user's input from the GET global array
						$name = $_GET['pkmn_name'];
						$name = "%".$name."%";//need to include the wildcard characters as input to the query
						//define the query
						$query = "SELECT SPECIES_NAME, TYPE1 FROM SPECIES WHERE SPECIES_NAME LIKE ?";
						//create a $stmt variable to hold our working statemnt and use the prepare() function to prepare the SQL
						$stmt = $db->prepare($query);
						//bind the $name variable to the first ? in the SQL statement. Tell it to treat it like a stirng
						$stmt->bindValue(1, $name, PDO::PARAM_STR);
						//execute the statement
						$stmt->execute();
						//check to see if the statement was successful
						if($stmt){
							//use rowCount() function to determine if the query successfully returned any results
							if($stmt->rowCount() > 0){
								//while there are still rows left in the result set, continue iterating and writing out the table rows with the data from the record
								while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
									echo("<tr><td>".$row['SPECIES_NAME']."</td><td>".$row['TYPE1']."</td></tr>");
								}//end while
							}//end rowCount
						}//end check for successful query
					}//end isset
				}//end check to see if connected
			?>
		</table>
		
		<div id="markup">
		<pre><code class="language-php"><span class="hljs-preprocessor">&lt;?php</span>
    <span class="hljs-comment">//define our connection settings</span>
    <span class="hljs-variable">$host</span> = <span class="hljs-string">"localhost"</span>;
    <span class="hljs-variable">$dbuser</span> = <span class="hljs-string">"username"</span>;
    <span class="hljs-variable">$dbpass</span> = <span class="hljs-string">"password"</span>;
    <span class="hljs-variable">$dbname</span> = <span class="hljs-string">"pokemon"</span>;
    <span class="hljs-comment">//create a new instance of the PDO class by stating we want the mysql: driver</span>
    <span class="hljs-comment">//also it needs our connection settings</span>
    <span class="hljs-variable">$db</span> = <span class="hljs-keyword">new</span> PDO(<span class="hljs-string">'mysql:host='</span>.<span class="hljs-variable">$host</span>.<span class="hljs-string">';dbname='</span>.<span class="hljs-variable">$dbname</span>.<span class="hljs-string">';charset=utf8'</span>, <span class="hljs-variable">$dbuser</span>, <span class="hljs-variable">$dbpass</span>);
    <span class="hljs-comment">//set the error mode to exception so they can be used with try/catch</span>
    <span class="hljs-variable">$db</span>-&gt;setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    <span class="hljs-comment">//set the emulate prepares to false so it actually uses prepared statements</span>
    <span class="hljs-variable">$db</span>-&gt;setAttribute(PDO::ATTR_EMULATE_PREPARES, <span class="hljs-keyword">false</span>);
<span class="hljs-preprocessor">?&gt;</span>
&lt;table&gt;
<span class="hljs-preprocessor">&lt;?php</span>
    <span class="hljs-comment">//check to see if the database is connected before continuing and attempting to query it</span>
    <span class="hljs-keyword">if</span>(<span class="hljs-variable">$db</span>){
        <span class="hljs-keyword">if</span>(<span class="hljs-keyword">isset</span>(<span class="hljs-variable">$_GET</span>[<span class="hljs-string">'pkmn_name'</span>])){
            <span class="hljs-comment">//get the user's input from the GET global array</span>
            <span class="hljs-variable">$name</span> = <span class="hljs-variable">$_GET</span>[<span class="hljs-string">'pkmn_name'</span>];
            <span class="hljs-variable">$name</span> = <span class="hljs-string">"%"</span>.<span class="hljs-variable">$name</span>.<span class="hljs-string">"%"</span>;<span class="hljs-comment">//need to include the wildcard characters as input to the query</span>
            <span class="hljs-comment">//define the query</span>
            <span class="hljs-variable">$query</span> = <span class="hljs-string">"SELECT SPECIES_NAME, TYPE1 FROM SPECIES WHERE SPECIES_NAME LIKE ?"</span>;
            <span class="hljs-comment">//create a $stmt variable to hold our working statemnt and use the prepare() function to prepare the SQL</span>
            <span class="hljs-variable">$stmt</span> = <span class="hljs-variable">$db</span>-&gt;prepare(<span class="hljs-variable">$query</span>);
            <span class="hljs-comment">//bind the $name variable to the first ? in the SQL statement. Tell it to treat it like a stirng</span>
            <span class="hljs-variable">$stmt</span>-&gt;bindValue(<span class="hljs-number">1</span>, <span class="hljs-variable">$name</span>, PDO::PARAM_STR);
            <span class="hljs-comment">//execute the statement</span>
            <span class="hljs-variable">$stmt</span>-&gt;execute();
            <span class="hljs-comment">//check to see if the statement was successful</span>
            <span class="hljs-keyword">if</span>(<span class="hljs-variable">$stmt</span>){
                <span class="hljs-comment">//use rowCount() function to determine if the query successfully returned any results</span>
                <span class="hljs-keyword">if</span>(<span class="hljs-variable">$stmt</span>-&gt;rowCount() &gt; <span class="hljs-number">0</span>){
                    <span class="hljs-comment">//while there are still rows left in the result set, continue iterating and writing out the table rows with the data from the record</span>
                    <span class="hljs-keyword">while</span>(<span class="hljs-variable">$row</span> = <span class="hljs-variable">$stmt</span>-&gt;fetch(PDO::FETCH_ASSOC)){
                        <span class="hljs-keyword">echo</span>(<span class="hljs-string">"&lt;tr&gt;&lt;td&gt;"</span>.<span class="hljs-variable">$row</span>[<span class="hljs-string">'SPECIES_NAME'</span>].<span class="hljs-string">"&lt;/td&gt;&lt;td&gt;"</span>.<span class="hljs-variable">$row</span>[<span class="hljs-string">'TYPE1'</span>].<span class="hljs-string">"&lt;/td&gt;&lt;/tr&gt;"</span>);
                    }<span class="hljs-comment">//end while</span>
                }<span class="hljs-comment">//end rowCount</span>
            }<span class="hljs-comment">//end check for successful query</span>
        }<span class="hljs-comment">//end isset</span>
    }<span class="hljs-comment">//end check to see if connected</span>
<span class="hljs-preprocessor">?&gt;</span>
&lt;/table&gt;
</code></pre>
		</div>
	</body>
</html>