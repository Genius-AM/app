function setroute(route) {
    document.location.href = document.location.href + "/" + route.value;
}

function countprice() {
    let men = document.getElementById('men').value;
    let women = document.getElementById('women').value;
    let kids = document.getElementById('kids').value;

    let price_men = document.getElementById('price_men').value;
    let price_women = document.getElementById('price_women').value;
    let price_kids = document.getElementById('price_kids').value;

    let prepayment = document.getElementById('prepayment').value;

    document.getElementById('total').innerText = men * price_men + women * price_women + kids * price_kids;
    document.getElementById('summ').value = men * price_men + women * price_women + kids * price_kids - prepayment;

    setTimeout(countprice, 100);
}

function noprepayment() {
    document.getElementById('prepayment').value = 0;
}