<?php
	require('pdo_mysql_class.php');//neededed to import the custom PDO_MySQL class into the program
	//define our connection settings
	$host = "localhost";
	$dbuser = "root";
	$dbpass = "root";
	$dbname = "pokemon";
	$db = new PDO_MySQL();//create a new instance of the class
	$db->connect($host,$dbuser,$dbpass,$dbname);//use the connect function
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
			<li class="active"><a href="demo_better_way.php">The PDO_MySQL custom PHP class</a></li>
			<li><a href="demo_final.php">Completed demo</a></li>
			<li><a href="demo_conclusion.html">Conclusion</a></li>
		</ul>
		<h2>The PDO_MySQL custom PHP class</h2>
		<p>
			This demonstrates the syntax of my custom PDO_MySQL class and its streamlined PDO usage to prepare statements and query the database.
			<br>
			You will notice that this still works for any pokemon names.
			<br>
			This works because we have used the PDO interface in the backend which is set up to prepare all SQL statements and perform all the variable binding 
			<br>
			behind the scenes, allowing the developer to focusing more on creating their application rather than having to consciously perform all the steps themselves.
			<br>
			Again, try entering: <code>' OR 1=1;-- </code> (include a space at the end)<br><br>
			
			You will notice this <strong>still does not work</strong>, which is good!.
			<br>
			When you are satisfied with this example and understand this simple functionality of the class, take a look at the <span class="sql">pdo_mysql_class.php</span> file and read the commented usage for a more complete understanding of what it is set up for and capable of.
			<br>
			Continue on to see completed form of this demo.
			<br><a href="demo_final.php" class="btn-simple">See completed demo.</a>
			<br><br>
		<form action="demo_better_way.php" method="get">
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
				if($db->is_connected()){
					if(isset($_GET['pkmn_name'])){
						//get the user's input from the GET global array
						$name = $_GET['pkmn_name'];
						$name = "%".$name."%";//need to include the wildcard characters as input to the query
						//define the query
						$query = "SELECT SPECIES_NAME, TYPE1 FROM SPECIES WHERE SPECIES_NAME LIKE ?";
						//query the database using the query_prepared() function to have it automatically prepare the SQL statement
						//and pass it an array of the variables that match the ? in the statement in the same order as they appear
						$result = $db->query_prepared($query,array($name));
						//use the has_rows() function to determine if the query successfully returned any results
						if($db->has_rows($result)){
							 //while there are still rows left in the result set, continue iterating and writing out the table rows with the data from the record
							while($row = $db->fetch_row($result)){
								echo("<tr><td>".$row['SPECIES_NAME']."</td><td>".$row['TYPE1']."</td></tr>");
							}//end while
						}//end has_rows
					}//end isset
				}//end is_connected
			?>
		</table>
		<div id="markup">
		<pre><code class="language-php"><span class="hljs-preprocessor">&lt;?php</span>
    <span class="hljs-keyword">require</span>(<span class="hljs-string">'pdo_mysql_class.php'</span>);<span class="hljs-comment">//neededed to import the custom PDO_MySQL class into the program</span>
    <span class="hljs-comment">//define our connection settings</span>
    <span class="hljs-variable">$host</span> = <span class="hljs-string">"localhost"</span>;
    <span class="hljs-variable">$dbuser</span> = <span class="hljs-string">"username"</span>;
    <span class="hljs-variable">$dbpass</span> = <span class="hljs-string">"password"</span>;
    <span class="hljs-variable">$dbname</span> = <span class="hljs-string">"pokemon"</span>;
    <span class="hljs-variable">$db</span> = <span class="hljs-keyword">new</span> PDO_MySQL();<span class="hljs-comment">//create a new instance of the class</span>
    <span class="hljs-variable">$db</span>-&gt;connect(<span class="hljs-variable">$host</span>,<span class="hljs-variable">$dbuser</span>,<span class="hljs-variable">$dbpass</span>,<span class="hljs-variable">$dbname</span>);<span class="hljs-comment">//use the connect function</span>
<span class="hljs-preprocessor">?&gt;</span>
&lt;table&gt;
<span class="hljs-preprocessor">&lt;?php</span>
    <span class="hljs-comment">//check to see if the database is connected before continuing and attempting to query it</span>
    <span class="hljs-keyword">if</span>(<span class="hljs-variable">$db</span>-&gt;is_connected()){
        <span class="hljs-keyword">if</span>(<span class="hljs-keyword">isset</span>(<span class="hljs-variable">$_GET</span>[<span class="hljs-string">'pkmn_name'</span>])){
            <span class="hljs-comment">//get the user's input from the GET global array</span>
            <span class="hljs-variable">$name</span> = <span class="hljs-variable">$_GET</span>[<span class="hljs-string">'pkmn_name'</span>];
            <span class="hljs-variable">$name</span> = <span class="hljs-string">"%"</span>.<span class="hljs-variable">$name</span>.<span class="hljs-string">"%"</span>;<span class="hljs-comment">//need to include the wildcard characters as input to the query</span>
            <span class="hljs-comment">//define the query</span>
            <span class="hljs-variable">$query</span> = <span class="hljs-string">"SELECT SPECIES_NAME, TYPE1 FROM SPECIES WHERE SPECIES_NAME LIKE ?"</span>;
            <span class="hljs-comment">//query the database using the query_prepared() function to have it automatically prepare the SQL statement</span>
            <span class="hljs-comment">//and pass it an array of the variables that match the ? in the statement in the same order as they appear</span>
            <span class="hljs-variable">$result</span> = <span class="hljs-variable">$db</span>-&gt;query_prepared(<span class="hljs-variable">$query</span>,<span class="hljs-keyword">array</span>(<span class="hljs-variable">$name</span>));
            <span class="hljs-comment">//use the has_rows() function to determine if the query successfully returned any results</span>
            <span class="hljs-keyword">if</span>(<span class="hljs-variable">$db</span>-&gt;has_rows(<span class="hljs-variable">$result</span>)){
                 <span class="hljs-comment">//while there are still rows left in the result set, continue iterating and writing out the table rows with the data from the record</span>
                <span class="hljs-keyword">while</span>(<span class="hljs-variable">$row</span> = <span class="hljs-variable">$db</span>-&gt;fetch_row(<span class="hljs-variable">$result</span>)){
                    <span class="hljs-keyword">echo</span>(<span class="hljs-string">"&lt;tr&gt;&lt;td&gt;"</span>.<span class="hljs-variable">$row</span>[<span class="hljs-string">'SPECIES_NAME'</span>].<span class="hljs-string">"&lt;/td&gt;&lt;td&gt;"</span>.<span class="hljs-variable">$row</span>[<span class="hljs-string">'TYPE1'</span>].<span class="hljs-string">"&lt;/td&gt;&lt;/tr&gt;"</span>);
                }<span class="hljs-comment">//end while</span>
            }<span class="hljs-comment">//end has_rows</span>
        }<span class="hljs-comment">//end isset</span>
    }<span class="hljs-comment">//end is_connected</span>
<span class="hljs-preprocessor">?&gt;</span>
&lt;/table&gt;
</code></pre>
		</div>
	</body>
</html>