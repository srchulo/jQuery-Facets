<html>
	<head>
		<script src="https://raw.github.com/srchulo/jQuery-Facets/master/jquery.min.js"></script>
		<script src="https://raw.github.com/srchulo/jQuery-Facets/master/jquery.deserialize.js"></script>
		<script src="https://raw.github.com/srchulo/jQuery-Facets/master/jquery.facets.js"></script>
		<?php
			if($_GET['q']) {
		?>
		<script type="text/javascript">
			$(document).ready(function(){ 
				$("form#facets").facets({
					'ajaxURL' : 'index_ajax.php',
					'preAJAX' : function () { 
						var zip = $("#zip").val();
						if(zip != "" && !/^\d{5}$/.test(zip)) { 
							alert("Must be a valid zip code!");
							return false;	
						}

						return true;
					}
				});
			});
		</script>
		<?php
			}
		?>
	</head>
	<body>
		<form action="index.php" method="GET">
			<input type="text" name="q" id="q" value="<?php echo $_GET["q"] ?>" />
			<input type="submit" value="search" />
		</form>

	<?php
		if($_GET['q'] != "") {
	?>
		<form id="facets">
			<h1>Color</h1>
			<label for="blue"><input type="checkbox" name="color[]" id="blue" value="blue" />blue</label><br />
			<label for="red"><input type="checkbox" name="color[]" id="red" value="red" />red</label>

			<h1>Condition</h1>
			<select name="condition">
				<option value=""></option>
				<option value="used">Used</option>
				<option value="good">Good</option>
				<option value="new">New</option>
				<option value="awesome">Awesome</option>
			</select>

			<h1>Zipcode</h1>
			<input type="text" value="" name="zip" id="zip" maxlength="5" />
		</form>

		<div id="searchCont">

		</div>
	<?php
		}
	?>
	</body>
</html>
