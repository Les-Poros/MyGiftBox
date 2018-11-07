let btnAttention = document.getElementById("btnAttention");
let btnActivite = document.getElementById("btnActivite");
let btnRestauration = document.getElementById("btnRestauration");
let btnHebergement = document.getElementById("btnHebergement");

let tabPrestations = [];

btnAttention.onclick = function() {
    if ($(':first', this).hasClass("actif")) {
        $(':first', this).removeClass("actif");
        renderPrestaCateg(tabPrestations);
    } else {
        $(".actif").removeClass("actif");
        renderPrestaCateg(equalCategory('Attention'));
        $(':first', this).addClass("actif");
    }
};

btnActivite.onclick = function() {
    if ($(':first', this).hasClass("actif")) {
        $(':first', this).removeClass("actif");
        renderPrestaCateg(tabPrestations);
    } else {
        $(".actif").removeClass("actif");
        renderPrestaCateg(equalCategory('Activité'));
        $(':first', this).addClass("actif");
    }
};

btnRestauration.onclick = function() {
    if ($(':first', this).hasClass("actif")) {
        $(':first', this).removeClass("actif");
        renderPrestaCateg(tabPrestations);
    } else {
        $(".actif").removeClass("actif");
        renderPrestaCateg(equalCategory('Restauration'));
        $(':first', this).addClass("actif");
    }
};

btnHebergement.onclick = function() {
    if ($(':first', this).hasClass("actif")) {
        $(':first', this).removeClass("actif");
        renderPrestaCateg(tabPrestations);
    } else {
        $(".actif").removeClass("actif");
        renderPrestaCateg(equalCategory('Hébergement'));
        $(':first', this).addClass("actif");
    }
};

$('.tabPrestations').each(function() {
    tabPrestations.push($(this));
});

function equalCategory(element) {
    return tabPrestations.filter(function(prest) {
        return prest.children().children()[2]['innerHTML'] === element;
    });
}

function renderPrestaCateg(tableau) {
    $('.grid_container_prestations').html('');
    for (let i = 0; i < tableau.length; i++) {
        $('.grid_container_prestations').append(tableau[i]);
    }
}