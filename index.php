<html>
<!-- bid = sell price, ask = buy price-->
<head>
<title>Simple Stock Exchange</title>
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script src="jquery-cookie-master/jquery.cookie.js"></script>
<script type="text/javascript">
var selected_stock = {};
var portfolio = {};
	
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

function setSelectedStock(symbol){
	var temp = loadJSON(symbol);
	if(temp['status'] != 'error'){
		selected_stock = loadJSON(symbol);
		displaySelectedStock();
	} else {
		alert('Could not find a stock with the symbol \'' + symbol + '\'\.');
	}
}

function displaySelectedStock(){
	$("#ss_name").text(selected_stock['name']);
	$("#ss_symbol").text(selected_stock['symbol']);
	$('#ss_bid').text(selected_stock['bid']);
	$('#ss_ask').text(selected_stock['ask']);
}

var money;

function updateMoney(){
	$("#money_display").text(money.toString());
	$.cookie("stock_money", money);
	
}

function buyStock(){
	var quant = parseInt($("#buy_sell_quantity_input").val());
	if(quant <= 0) {
		alert("Please use above zero quantities.");
		return;
	}
	var cost = parseInt(selected_stock['ask']);
	cost = quant * cost;
	if(cost > money) {
		alert("costs too much");
	} else {
		money -= cost;
		updateMoney();
		addToPortfolio(quant);
	}
}
function sellStock(){
	var quant = parseInt($("#buy_sell_quantity_input").val());
	if(quant <= 0) {
		alert("Please use above zero quantities.");
		return;
	} else if(quant > portfolio[selected_stock['symbol']]) {
		alert('You only have ' + portfolio[selected_stock['symbol']] + ' of that stock. You cannot sell more than you own.');
		return;
	} else if(!(selected_stock['symbol'] in portfolio)){
		alert('You do not currently own any of that stock');
		return;
	}
	var cost = parseInt(selected_stock['bid']);
	cost = quant * cost;
	money += cost;
	updateMoney();
	removeFromPortfolio(quant);
}

function addToPortfolio(amount){
	var current_amount;
	if(selected_stock['symbol'] in portfolio) {	//Already own the company's stock
		portfolio[selected_stock['symbol']] += amount;
	} else {
		portfolio[selected_stock['symbol']] = amount;
	}
	
	refreshCurrentPortfolio();
	savePortfolio();
}

function removeFromPortfolio(amount){
	var current_amount;
	portfolio[selected_stock['symbol']] -= amount;
	if(portfolio[selected_stock['symbol']] <= 0) {	//If you no longer own any of the stock
		delete portfolio[selected_stock['symbol']];
	}
	
	refreshCurrentPortfolio();
	savePortfolio();
}
	
function refreshCurrentPortfolio(){
		var str = [];
		str.push("<tr><td>Company</td><td>Quantity</td><td>Price Paid</td></tr>");
			for(key in portfolio) {
				var stock = loadJSON(key);
				if(!stock['name']) {
					continue;
				}
				str.push('<tr class = removable>');
				str.push('<td>');
				str.push(stock['name']);
				str.push('</td>');
				str.push('<td>');
				str.push(portfolio[key].toString());
				str.push('</td>');
				str.push('<td>');
				str.push(parseInt(stock['ask']) * portfolio[key]);
				str.push('</td>');
				str.push('</tr>');
			}
	$("#stock_portfolio_table").html(str.join(""));
}

function savePortfolio(){
	var str = [];
	for( key in portfolio) {
		str.push(key.toString() + ',' + portfolio[key].toString() + '.');
	}
	$.cookie("stock_portfolio", str.join(""));
}

function loadPortfolio(){
	portfolio = {};
	var str = $.cookie("stock_portfolio");
	if(str) {
		var arr = str.split(".");
		var temp;
			console.log('');
			console.log('str: ' + str);
		for(stck in arr) {
			if(arr[stck] == ''){
				continue;
			}
			console.log('stck: ' + stck);
			temp = arr[stck].split(',');
			console.log('temp: ' + temp);
			portfolio[temp[0]] = parseInt(temp[1]);
		}
	}
}

function viewStockDetails(){
	var symbol = selected_stock['symbol'];
	if(symbol == null) {
		alert('Please search for a stock first.');
		return;
	}
    var form = document.createElement('form');
    form.setAttribute('method', 'POST');
    form.setAttribute('action', 'view.php');
    form.style.display = 'hidden';
    document.body.appendChild(form)
	var inp = document.createElement('input');
	inp.value = symbol;
	inp.name = 'symbol';
	inp.type = 'hidden'
	form.appendChild(inp);
	form.submit();
}

function loadPage(){
	loadPortfolio();
	refreshCurrentPortfolio();

	if(!(money = $.cookie("stock_money"))) {	//Set the cookie if it doesn't exist, if not, load from it
		$.cookie("stock_money", 100000);
		money = 100000;
	} else {
		money = parseInt(money);
		$("#money_display").text(money.toString());
		updateMoney();
	}
}

</script>
</head>
<body onload='loadPage()'>
<h1> Simple Stock Exchange</h1> <br />
<table border='2'>
<tr><td>Money Remaining</td></tr>
<tr><td><p id="money_display">100000</p></tr></td></table> <br />

Search Symbol: <input id="search_stock_input" type="text" name="Search Symbol" />
<button id="search_stock_button" >Search </button>
<button id='view_detail_button'>See Details</button>
<!--id="view_detail_button"-->
<table id="selected_stock_table" border="1">
<tr class='dnr'>
<td>Name</td>
<td>Symbol</td>
<td>Bid</td>
<td>Ask</td>
</tr>
<tr>
<td id="ss_name">NA</td>
<td id="ss_symbol">NA</td>
<td id="ss_bid">NA</td>
<td id="ss_ask">NA</td>
</tr>
</table>

Quantity: <input id="buy_sell_quantity_input" type="number" name="quantity_input">
<button id="buy_button">Buy</button>
<button id="sell_button">Sell</button>

<br /><br /><br />
<h3>Current Portfolio</h3>
<table id="stock_portfolio_table" border="1">
<tbody id="tbody1">
<tr class='dnr'>
<td>Company</td>
<td>Quantity</td>
<td>Price Paid</td>
</tr>
<tr>
<td>None</td>
<td>None</td>
<td>None</td>
</tr>
</tbody>
</table>
<br /><button id="reset_button">Reset</button>
</body>
<script>
$('#reset_button').click(function(){
	$.cookie('stock_money', 100000);
	$.cookie('stock_portfolio', null);
	portfolio = {};
	money = 100000;
	refreshCurrentPortfolio();
	updateMoney();
});
$('#search_stock_button').click(function(){
	setSelectedStock( $('#search_stock_input').val() );
});

$('#search_stock_input').keypress(function(e) {
	if(e.which == 13) {
	setSelectedStock( $('#search_stock_input').val() );
	}
});

$('#view_detail_button').click(function() {
	viewStockDetails();
});

$('#buy_button').click(function(){
	buyStock();
});
$('#sell_button').click(function(){
	sellStock();
});

</script>
</html>