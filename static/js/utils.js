function getDate() {
  var d = new Date();
  var date = d.getFullYear() + "-" + ("0"+(d.getMonth()+1)).slice(-2) + "-" + ("0" + d.getDate()).slice(-2);
  return date;
}

function getRandomColor() {
  var letters = "0123456789ABCDEF";
  var color = '#';
  for (var i = 0; i < 6; i++ ) {
    color += letters[Math.floor(Math.random() * 16)];
  }
  return color;
}

function createArrayOfValue(value, count) {
  var values = [];
  for (var i = 0; i < count; i++) {
    values.push(value);
  }
  return values;
}

function sendRequest(options, successCallback) {
  $.ajax({
    url: "api.php",
    method: "POST",
    data: options,
    success: successCallback,
    error: function(data) {
      console.log(data);
    }
  });
}

function numberWithCommas(x) {
  return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}