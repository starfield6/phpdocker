<?php
echo <<< _END
	<html>
		<head>
			<title>PHP Development with Docker Part 2</title>
			<link rel="stylesheet" href="styles.css"/>
		</head>
		<body>
			<form method="post" action="scrape.php">
				<h3> Welcome to the Starfield for the second time! </h3>
				<p class="title">Chose a link to scrape:</p>
				<select name="link">
					<option value="https://en.wikipedia.org/wiki/Object-oriented_programming">Object oriented programming</option>
					<option value="https://fr.wikipedia.org/wiki/Consortium">Consortium</option>
					<option value="https://en.wikipedia.org/wiki/Culinary_arts">Culinary arts</option>
				</input>
				<input type="submit" value="Scrape">
			</form>
		</body>
	</html>
_END;

?>

