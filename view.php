<html>	
<?php
	session_start();
	$symbol = $_POST["symbol"];
?>
<head>
<title>Stock Viewer</title>
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script type="text/javascript">

<?php echo 'var sym = \''; ?><?php echo $symbol ?><?php echo '\';' ?>

function loadJSON(symbol){
	var stock = {};
	/*$.getJSON("http://data.benzinga.com/stock/" + symbol.toString(), function(data) {
		for(name in data){
		//alert(name + ": " + data[name]);
			stock[name] = data[name];
		}
		document.write("1");
		for(a in stock){
			document.write(a + ": " + stock[a] + "<br />");
			}
			document.write("<br /> <br />");
		stock = data;
		document.write("2");
		for(a in stock){
			document.write(a + ": " + stock[a] + "<br />");
		}
	});*/
	$.ajax({	//Makes sure its synchonous so that it WAITS UNTIL IT HAS A VALUE IN STOCK BEFORE IT RETURNS
  url: "http://data.benzinga.com/stock/" + symbol.toString(), 
  dataType: 'json',  
  async: false,  
  success: function(data) {
		for(name in data){
		//alert(name + ": " + data[name]);
			stock[name] = data[name];
		}
		stock = data;
	}});
	return stock;
}

function loadPage(){
	var stock = loadJSON(sym);
	var str = [];
		for(key in stock){
			str.push('<tr>');
			str.push('<td>');
			str.push(key);
			str.push('</td>');
			str.push('<td>');
			str.push(stock[key]);
			str.push('</td>');
			str.push('</tr>');
		}
	$("#stock_detail_table").html(str.join(""));
}
function goBack(){
	history.back();
}
</script>

<body onload='loadPage()'>
<table id='stock_detail_table' border='1'>
</table>
<button onclick="goBack()">Return to previous page</button>
</body>
</html>