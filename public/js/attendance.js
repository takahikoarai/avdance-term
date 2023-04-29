function changeDayPrev() {
  const getToday = document.getElementsByName('date');
  const date = getToday[1].value;
  const year = Number(date.substr(0, 4));
  const month = Number(date.substr(5, 2));
  const day = Number(date.substr(8, 2));
  let today = new Date(year, month - 1, day);
  today.setDate(today.getDate() - 1);
  const formatToday = formatDate(today);
  getToday[1].value = formatToday;
}

function changeDayNext() {
  const getToday = document.getElementsByName('date');
  const date = getToday[1].value;
  const year = Number(date.substr(0, 4));
  const month = Number(date.substr(5, 2));
  const day = Number(date.substr(8, 2));
  let today = new Date(year, month - 1, day);
  today.setDate(today.getDate() + 1);
  const formatToday = formatDate(today);
  getToday[2].value = formatToday;
}

function formatDate(dt) {
  const y = dt.getFullYear();
  const m = ('00' + (dt.getMonth() + 1)).slice(-2);
  const d = ('00' + dt.getDate()).slice(-2);
  return (y + '-' + m + '-' + d);
}

const prev = document.getElementById('prev');
prev.onclick = changeDayPrev;

const next = document.getElementById('next');
next.onclick = changeDayNext;