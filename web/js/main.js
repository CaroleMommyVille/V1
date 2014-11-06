var currentPage = "";
function getParameterByName(a) {
    a = a.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
    var c = new RegExp("[\\?&]" + a + "=([^&#]*)"), b = c.exec("?" + getCurrentParameters());
    return b == null ? "" : decodeURIComponent(b[1].replace(/\+/g, " "))
}
function getCurrentStatus() {
    $.ajax({url: "/ui/current.json"}).done(function (a) {
        isAuthenticated = a.auth
    })
}
function getCurrentPage() {
    var a = window.location.hash;
    if (a.charAt(0) == "#") {
        if (a == "#/") {
            currentPage = "login"
        } else {
            if (a.length == 0) {
                currentPage = "login"
            } else {
                currentPage = a.substring(1);
                query = currentPage.indexOf("?");
                if (query >= 0) {
                    currentPage = currentPage.substring(0, query)
                }
                if (currentPage.charAt(0) == "/") {
                    currentPage = currentPage.substring(1)
                }
                if (currentPage.charAt(currentPage.length - 1) == "/") {
                    currentPage = currentPage.substring(0, currentPage.length - 1)
                }
            }
        }
        return currentPage
    }
    currentPage = "login";
    return currentPage
}
function getCurrentParameters() {
    var a = window.location.hash;
    if (a.charAt(0) == "#") {
        query = a.indexOf("?");
        if (query > 0) {
            return a.substr(query + 1)
        }
    }
    return window.location.search.substring(1)
}
function generateMenu() {
    $.ajax({url: "/ui/menu.json", async: false}).done(function (b) {
        var a = b;
        $("nav#menu").empty();
        $.each(a, function (d, c) {
            if (window.location.hash === "#" + c) {
                is_active = " menu_active"
            } else {
                is_active = ""
            }
            if (d == "MV") {
                if ((is_active === "") && (window.location.hash === "")) {
                    is_active = " menu_active"
                }
                $("nav#menu").append("<li><a class='home" + is_active + "' href='#" + c + "' title='Accueil'>&nbsp;</a></li>")
            } else {
                d = d.replace(/^Mommy/gi, "Mommy<span>").replace(/$/gi, "</span>");
                $("nav#menu").append("<li><a class='" + is_active + "' href='#" + c + "'>" + d + "</a></li>")
            }
        })
    })
}
function generateNotification() {
    $.ajax({url: "/notification/"}).done(function (a) {
        a = a.replace("</ul>", "<li><a href='/deconnexion'>Déconnexion</a></li></ul>");
        $("#notification").stop().empty().html(a)
    })
}
function generatePageContent(a) {
    $("#loading").show();
    $.ajax({url: "/" + a + "/display.json"}).done(function (b) {
        var c = getCurrentParameters();
        $.each(b, function (d, e) {
            var g = e.frame;
            if (e.empty) {
                $(g).empty()
            }
            if (typeof e.title !== "undefined") {
                document.title = "MommyVille - " + e.title
            }
            if (typeof e.menu !== "undefined") {
                if (e.menu == "refresh") {
                    var f = getCurrentPage();
                    generateMenu()
                }
            }
            if (typeof e.notification !== "undefined") {
                if (e.notification == "refresh") {
                    generateNotification()
                }
            }
            $.ajax({url: e.html, data: c, async: false}).done(function (h) {
                $(g).append(h);
                $("#loading").hide()
            }).fail(function () {
                $.get("/error/404", function (h) {
                    $("#center").html(h);
                    $("#loading").hide()
                })
            })
        })
    })
}
function loadContent() {
    var a = getCurrentPage();
    if (a == false) {
        return
    }
    generateMenu();
    generatePageContent(a);
    if ((window.location.hash == "#/login/register/remember") || (window.location.hash == "#/login/register/reset") || (window.location.hash.indexOf("#/login/register/remember/") >= 0)) {
        window.location.hash = "#/"
    }
}
function initGeoComplete(b, a) {
    if ($(b).val().length) {
        address = $(b).val()
    } else {
        address = "Paris, France"
    }
    $(b).geocomplete({map: a, location: address, markerOptions: {draggable: false}})
}
function initMapStations(a) {
    $.getJSON("/map/stations.json", function (b) {
        var c = [];
        $.each(b, function (e, d) {
            c.push({id: d.id, text: d.name})
        });
        $(a).select2({multiple: true, minimumInputLength: 3, maximumSelectionSize: 3, tags: c, width: "element", createSearchChoice: function (d) {
                return null
            }, placeholder: "Choisissez jusqu'à 3 stations"})
    })
}
function validationDialog(b, a) {
    $("#dialog-message").attr("title", b);
    a = a.replace("ERROR: ", "<br/>");
    $("#dialog-message").html(a);
    $("#dialog-message").dialog({modal: true, minWidth: 300, maxWidth: 600, buttons: {Ok: function () {
                $(this).dialog("close")
            }}})
}
function createNewSelectItem(a, b) {
    if ($(b).filter(function () {
        return this.text.localeCompare(a) === 0
    }).length === 0) {
        return{id: a, text: a}
    }
}
window.onhashchange = function () {
    loadContent()
};
$(function () {
    if (window.location.pathname.indexOf("/login/change-password") < 0) {
        loadContent()
    }
    generatePageContent("feedback");
    $("a").click(function (c) {
        if ($(this).attr("href").charAt(0) == "#") {
            c.preventDefault();
            var b = $(this).attr("href");
            if (window.location.hash == b) {
                if (getCurrentParameters() == "") {
                    b += "?rnd=" + Math.random()
                } else {
                    b += "&rnd=" + Math.random()
                }
            }
            window.location.hash = b
        }
    });
    $.maxlength.setDefaults($.maxlength.regionalOptions.fr);
    var a = false;
    $("#footerSlideButton").click(function () {
        $("#questionning").slideToggle("slow");
        if (a === false) {
            $("#footerSlideContent").animate({height: "430px"});
            $("#footerSlideContent").toggleClass("footerSlideContainer-visible");
            $(this).css("backgroundPosition", "bottom left");
            a = true
        } else {
            $("#footerSlideContent").animate({height: "0px"});
            $("#footerSlideContent").toggleClass("footerSlideContainer-visible");
            $(this).css("backgroundPosition", "top left");
            a = false
        }
    });
    $(".fancybox").fancybox({titlePosition: "over", helpers: {thumbs: {width: 50, height: 50}, title: {type: "over"}}});
    fade($("#questionning"));
    $(document).on("select2-open", "select", function () {
        var b;
        $(".select2-results").each(function () {
            if ($(this).parent().css("display") != "none") {
                b = $(this)
            }
            if (b === undefined) {
                return
            }
            b.mCustomScrollbar({theme: "rounded-dark"})
        });
        $(".select2-with-searchbox").each(function () {
            if ($(this).parent().css("display") != "none") {
                b = $(this)
            }
            if (b === undefined) {
                return
            }
            b.mCustomScrollbar({theme: "rounded-dark"})
        });
        $(".select2-drop-multiple").each(function () {
            if ($(this).parent().css("display") != "none") {
                b = $(this)
            }
            if (b === undefined) {
                return
            }
            b.mCustomScrollbar({theme: "rounded-dark"})
        });
        $(".ui-widget-content").each(function () {
            if ($(this).parent().css("display") != "none") {
                b = $(this)
            }
            if (b === undefined) {
                return
            }
            b.mCustomScrollbar({theme: "rounded-dark"})
        })
    })
});
$(window).bind("scroll", function () {
    stretch_portal_content();
    $(window).resize(stretch_portal_content);
    if ($(window).scrollTop() > 40) {
        $("header").stop().animate({height: "40px"}, 300);
        $("#notification").stop().animate({marginTop: "-10px"}, 300);
        $("#menu").stop().animate({top: "40px"}, 300);
        $("#loading").stop().animate({height: "40px"}, 300);
        $("#summary").stop().animate({top: "170px"}, 300)
    } else {
        $("header").stop().animate({height: "130px"}, 300);
        $("#notification").stop().animate({marginTop: "80px"}, 300);
        $("#menu").stop().animate({top: "130px"}, 300);
        $("#loading").stop().animate({height: "130px"}, 300);
        $("#summary").stop().animate({top: "300px"}, 300)
    }
});
function stretch_portal_content() {
    if ($(window).height() > $("body").height()) {
        $("#center").height($(window).height() - ($("body").height() - $("#center").height()));
        $("#left").height($(window).height() - ($("body").height() - $("#left").height()));
        $("#right").height($(window).height() - ($("body").height() - $("#right").height()))
    }
}
function fade(a) {
    a.fadeIn(6000).delay(3000).fadeOut(6000, function () {
        var b = $(this).next("p");
        fade(b.length > 0 ? b : $(this).parent().children().first())
    })
}
;