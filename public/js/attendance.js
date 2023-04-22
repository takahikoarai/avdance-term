function changeDayPrev() {
  let getToday = document.getElementsByName('date');
  let date = getToday[1].value;
  let year = Number(date.substr(0, 4));
  let month = Number(date.substr(5, 2));
  let day = Number(date.substr(8, 2));
  let today = new Date(year, month - 1, day);
  today.setDate(today.getDate() - 1);
  let formatToday = formatDate(today);
  getToday[1].value = formatToday;
}

function changeDayNext() {
  let getToday = document.getElementsByName('date');
  let date = getToday[1].value;
  let year = Number(date.substr(0, 4));
  let month = Number(date.substr(5, 2));
  let day = Number(date.substr(8, 2));
  let today = new Date(year, month - 1, day);
  today.setDate(today.getDate() + 1);
  let formatToday = formatDate(today);
  getToday[2].value = formatToday;
}

function formatDate(dt) {
  var y = dt.getFullYear();
  var m = ('00' + (dt.getMonth() + 1)).slice(-2);
  var d = ('00' + dt.getDate()).slice(-2);
  return (y + '-' + m + '-' + d);
}

let prev = document.getElementById('prev');
prev.onclick = changeDayPrev;

let next = document.getElementById('next');
next.onclick = changeDayNext;