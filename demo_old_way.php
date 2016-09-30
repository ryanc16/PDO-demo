<?php
	//define our connection settings
	$host = "localhost";
	$dbuser = "root";
	$dbpass = "root";
	$dbname = "pokemon";
	//create a variable to hold the reference to our database connection
	$db = mysqli_connect($host,$dbuser,$dbpass,$dbname);
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
			<li class="active"><a href="demo_old_way.php">The mysqli_* old method</a></li>
			<li><a href="demo_new_way.php">The PHP PDO new method</a></li>
			<li><a href="demo_better_way.php">The PDO_MySQL custom PHP class</a></li>
			<li><a href="demo_conclusion.html">Conclusion</a></li>
		</ul>
		<h2>The mysqli_* old method</h2>
		<p>
			This demonstrates the syntax of mysqli_* functions and their usage to query a database. In this example, we'll be searching for pokemon that are similar to what the user types into the box.
			<br>
			You will notice that simple pokemon names, such as Pikachu for example, will work fine. However, say you want to find Farfetch'd. Can you search for his entire name?
			<br>
			This breaks because the single quote creates an incomplete SQL query string.
			<br>
			Also, try entering: <code>' OR 1=1;-- </code>  (include a space at the end)<br><br>
			
			You will notice this returns everything in the table of pokemon, because 1=1 always is true. Likewise, if you would put any other valid SQL there, it would also run. <strong>Be careful!</strong>
			<br>
			Entering something like <code>' OR 1=1; DROP * FROM species;-- </code> would query just fine and <strong>would delete all your data</strong>.
			<br>
			To fix this we will use the PHP PDO class and use prepared statements.
			<br>
			<a href="demo_new_way.php" class="btn-simple">New PDO way Example</a>
		</p>
		<form action="demo_old_way.php" method="get">
			<label for="pkmn_name">Pokemon Name: </label>
			<input id="pkmn_name" type="text" name="pkmn_name" value="<?php if(isset($_GET['pkmn_name'])) echo($_GET['pkmn_name']); ?>" />
			<input type="submit" name="" value="Search">
		</form>
		<strong>The complete query: </strong>
		<?php
			$name = isset($_GET['pkmn_name'])?$_GET['pkmn_name']:"";
			$query = "SELECT SPECIES_NAME, TYPE1 FROM SPECIES WHERE SPECIES_NAME LIKE '%".$name."%' ";
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
						//define the query
						$query = "SELECT SPECIES_NAME, TYPE1 FROM SPECIES WHERE SPECIES_NAME LIKE '%".$name."%'";
						//query the database using the mysqli_query() function
						$result = mysqli_query($db,$query);
						//check to see if the query was successful
						if($result){
							//use mysqli_num_rows() function to determine if the query returned any rows
							if(mysqli_num_rows($result) > 0){
								//while there are still rows left in the result set, continue iterating and writing out the table rows with the data from the record
								while($row = mysqli_fetch_assoc($result)){
									echo("<tr><td>".$row['SPECIES_NAME']."</td><td>".$row['TYPE1']."</td></tr>");
								}//end while
							}//end mysqli_num_rows
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
    <span class="hljs-comment">//create a variable to hold the reference to our database connection</span>
    <span class="hljs-variable">$db</span> = mysqli_connect(<span class="hljs-variable">$host</span>,<span class="hljs-variable">$dbuser</span>,<span class="hljs-variable">$dbpass</span>,<span class="hljs-variable">$dbname</span>);
<span class="hljs-preprocessor">?&gt;</span>
&lt;table&gt;
<span class="hljs-preprocessor">&lt;?php</span>
    <span class="hljs-comment">//check to see if the database is connected before continuing and attempting to query it</span>
    <span class="hljs-keyword">if</span>(<span class="hljs-variable">$db</span>){
        <span class="hljs-keyword">if</span>(<span class="hljs-keyword">isset</span>(<span class="hljs-variable">$_GET</span>[<span class="hljs-string">'pkmn_name'</span>])){
            <span class="hljs-comment">//get the user's input from the GET global array</span>
            <span class="hljs-variable">$name</span> = <span class="hljs-variable">$_GET</span>[<span class="hljs-string">'pkmn_name'</span>];
            <span class="hljs-comment">//define the query</span>
            <span class="hljs-variable">$query</span> = <span class="hljs-string">"SELECT SPECIES_NAME, TYPE1 FROM SPECIES WHERE SPECIES_NAME LIKE '%"</span>.<span class="hljs-variable">$name</span>.<span class="hljs-string">"%'"</span>;
            <span class="hljs-comment">//query the database using the mysqli_query() function</span>
            <span class="hljs-variable">$result</span> = mysqli_query(<span class="hljs-variable">$db</span>,<span class="hljs-variable">$query</span>);
            <span class="hljs-comment">//check to see if the query was successful</span>
            <span class="hljs-keyword">if</span>(<span class="hljs-variable">$result</span>){
                <span class="hljs-comment">//use mysqli_num_rows() function to determine if the query returned any rows</span>
                <span class="hljs-keyword">if</span>(mysqli_num_rows(<span class="hljs-variable">$result</span>) &gt; <span class="hljs-number">0</span>){
                    <span class="hljs-comment">//while there are still rows left in the result set, continue iterating and writing out the table rows with the data from the record</span>
                    <span class="hljs-keyword">while</span>(<span class="hljs-variable">$row</span> = mysqli_fetch_assoc(<span class="hljs-variable">$result</span>)){
                        <span class="hljs-keyword">echo</span>(<span class="hljs-string">"&lt;tr&gt;&lt;td&gt;"</span>.<span class="hljs-variable">$row</span>[<span class="hljs-string">'SPECIES_NAME'</span>].<span class="hljs-string">"&lt;/td&gt;&lt;td&gt;"</span>.<span class="hljs-variable">$row</span>[<span class="hljs-string">'TYPE1'</span>].<span class="hljs-string">"&lt;/td&gt;&lt;/tr&gt;"</span>);
                    }<span class="hljs-comment">//end while</span>
                }<span class="hljs-comment">//end mysqli_num_rows</span>
            }<span class="hljs-comment">//end check for successful query</span>
        }<span class="hljs-comment">//end isset</span>
    }<span class="hljs-comment">//end check to see if connected</span>
<span class="hljs-preprocessor">?&gt;</span>
&lt;/table&gt;
</code></pre>
		</div>
	</body>
</html>