let btnAttention = document.getElementById("btnAttention");
let btnActivite = document.getElementById("btnActivite");
let btnRestauration = document.getElementById("btnRestauration");
let btnHebergement = document.getElementById("btnHebergement");
let selectTri = document.getElementById("tri");

let tabPrestations = [];

btnAttention.onclick = function() {
    if ($(':first', this).hasClass("actif")) {
        $(':first', this).removeClass("actif");
        tabPrestVariable =tabPrestations;
    } else {
        $(".actif").removeClass("actif");
        equalCategory('Attention')
        $(':first', this).addClass("actif");
    }
    tri(selectTri.value);
};

btnActivite.onclick = function() {
    if ($(':first', this).hasClass("actif")) {
        $(':first', this).removeClass("actif");
        tabPrestVariable =tabPrestations;
    } else {
        $(".actif").removeClass("actif");
        equalCategory('Activité')
        $(':first', this).addClass("actif");
    }
    tri(selectTri.value);
};

btnRestauration.onclick = function() {
    if ($(':first', this).hasClass("actif")) {
        $(':first', this).removeClass("actif");
        tabPrestVariable =tabPrestations;
    } else {
        $(".actif").removeClass("actif");
        equalCategory('Restauration')
        $(':first', this).addClass("actif");
    }
    tri(selectTri.value);
};

btnHebergement.onclick = function() {
    if ($(':first', this).hasClass("actif")) {
        $(':first', this).removeClass("actif");
        tabPrestVariable =tabPrestations;
    } else {
        $(".actif").removeClass("actif");
        equalCategory('Hébergement')
        $(':first', this).addClass("actif");
    }
    tri(selectTri.value);
};

$('.tabPrestations').each(function() {
    tabPrestations.push($(this));
});

let tabPrestVariable = tabPrestations;

function equalCategory(element) {
    tabPrestVariable = tabPrestations.filter(function(prest) {
        return prest.children().children()[2]['innerHTML'] === element;
    });
}

function renderPrestaCateg(tableau) {
    $('.grid_container_prestations').html('');
    for (let i = 0; i < tableau.length; i++) {
        $('.grid_container_prestations').append(tableau[i]);
    }
}

function tri(value){
    let tab = new Array();
    tabPrestVariable.forEach(function (t){
        tab.push(t);
    });
    if (value === "0"){
        renderPrestaCateg(tabPrestVariable);
    }
    else if (value === "1"){
        renderPrestaCateg(tab.sort(increasing));
    }
    else{
        renderPrestaCateg(tab.sort(decrease));
    }
}

function increasing(x, y) {
    return x.children()[3]['innerHTML'] - y.children()[3]['innerHTML'];
}

function decrease(x, y) {
    return y.children()[3]['innerHTML'] - x.children()[3]['innerHTML'];
}