function randomColor($number) 
{
    var colors = [];
 	var jump = Math.floor(300/$number);
    for (var i = 0; i < 300; i+= jump) 
    {
    	colors.push('hsl(' + i + ', 100%, 60%)');
    }
    return colors;
}
function trim(str) 
{ 
	var start = 0; 
	var end = str.length; 
	while (start < str.length && str.charAt(start) == ' ') start++; 
	while (end > 0 && str.charAt(end-1) == ' ') end--; 
	return str.substr(start, end-start); 
}

function getStepChart(data)
{
	var max = '';
	for (var i =0; i< data.length; i++)
	{
		for (var j =0; j< data[i].length; j++)
		{
			if (typeof data[i][j] == 'number' && trim(data[i][j].toString()) > max)
			{
				max = data[i][j];
			}
		}
	}
	var step = parseInt(max/10);
	return step;
}

/**
 * Show a loading indicator
 */
function showLoading(loading)
{
	if (!loading) loading = '';
	$("body").mLoading({
	   text: loading + "...",
	});		
}

/**
 * Hide loading indicator
 */
function hideLoading()
{
	$("body").mLoading('hide');
}

/**
 * convert color code form RBG to hex
 *
 * @param  varchar  rgb code color type RGB
 * @return	string  code color type hex
 */
function rgb2hex(rgb) {
 	rgb = rgb.match(/^rgba?[\s+]?\([\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?/i);
 	return (rgb && rgb.length === 4) ? "#" +
	  	("0" + parseInt(rgb[1],10).toString(16)).slice(-2) +
	  	("0" + parseInt(rgb[2],10).toString(16)).slice(-2) +
	  	("0" + parseInt(rgb[3],10).toString(16)).slice(-2) : '';
}

function getRandomColor() {
    var letters = '0123456789ABCDEF';
    var color = '#';
    for (var i = 0; i < 6; i++ ) {
        color += letters[Math.floor(Math.random() * 16)];
    }
    return color;
}
