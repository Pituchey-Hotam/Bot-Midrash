function messageAlert(e, s, a = !1) {
    var l = '<div class="alert alert-' + s + ' alert-dismissible">' + e +
        '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></div>';
    a ? $("#message").html(l) : ($("#message").children().length >= 3 &&
        $("#message").children().first().remove(), $("#message").append(l));
}