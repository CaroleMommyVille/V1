var currentPage = '';

function getParameterByName(name) {
    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
        results = regex.exec('?'+getCurrentParameters());
    return results == null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}

function getCurrentStatus() {
	$.ajax({
		url: '/ui/current.json',
	})
	.done(function (data) {
		isAuthenticated = data.auth;
	});
}

function getCurrentPage() {
	var hash = window.location.hash;
	if (hash.charAt(0) == '#') {
		if (hash == '#/') currentPage = 'login';
//		if (hash.charAt(1) != '/') return false;
		else if (hash.length == 0) currentPage = 'login';
		else {
			currentPage = hash.substring(1);
			query = currentPage.indexOf('?');
			if (query >= 0)
				currentPage = currentPage.substring(0, query);
			if (currentPage.charAt(0) == '/')
				currentPage = currentPage.substring(1);
			if (currentPage.charAt(currentPage.length-1) == '/')
				currentPage = currentPage.substring(0,currentPage.length-1);		
		}

		return currentPage;
	}
	currentPage = 'login';
	return currentPage;
}

function getCurrentParameters() {
	var hash = window.location.hash;
	if (hash.charAt(0) == '#') {
		query = hash.indexOf('?');
		if (query > 0) return hash.substr(query+1);
	}
	return window.location.search.substring(1);
}

function generateMenu() {
    $.ajax({
    	'url': '/ui/menu.json',
    	'async': false,
    })
    .done(function (data) {
        var currentMenu = data;
		$("nav#menu").empty();
	    $.each(currentMenu, function(name, url) {
	    	if (window.location.hash === '#'+url)
	    		is_active = ' menu_active';
	    	else
	    		is_active = '';
	    	if (name == 'MV') {
	    		if ((is_active === '') && (window.location.hash === '')) is_active = ' menu_active';
	    		$("nav#menu").append("<li><a class='home"+is_active+"' href='#"+url+"' title='Accueil'>&nbsp;</a></li>");
	    	} else {
	    		name = name.replace(/^Mommy/gi, 'Mommy<span>').replace(/$/gi, '</span>');
	    		$("nav#menu").append("<li><a class='"+is_active+"' href='#"+url+"'>"+name+"</a></li>");
	    	}
	    });
    });
}

function generateNotification() {
    $.ajax({
    	'url': '/notification/',
    })
    .done(function (data) {
    	data = data.replace('</ul>', "<li><a href='/deconnexion'>Déconnexion</a></li></ul>");
    	$("#notification").stop().empty().html(data);
		//$("#notification > ul").append("<li><a href='/deconnexion'>Déconnexion</a></li>");
    });
}

function generatePageContent(currentPage) {
	$("#loading").show();
	/*
	var cp = currentPage.replace(/\//g, '');
	if (cp.indexOf('profilvoir') >= 0)
		display = 'profil/voir';
	else
		display = currentPage;
	*/
	$.ajax({
		url: '/'+currentPage+'/display.json',
	})
	.done(function (data) {
		var params = getCurrentParameters();
		$.each(data, function (idx, element) {
			var targetFrame = element.frame;
			if (element.empty) {
				$(targetFrame).empty();
			}
			if (typeof element.title !== 'undefined') {
				document.title = 'MommyVille - '+element.title;
			}
			if (typeof element.menu !== 'undefined') {
				if (element.menu == 'refresh') {
					var currentPage = getCurrentPage();
					generateMenu();
				}
			}
			if (typeof element.notification !== 'undefined') {
				if (element.notification == 'refresh') {
					generateNotification();
				}
			}
			$.ajax({
				url: element.html,
				data: params,
				async: false,
			})
			.done(function (content) {
				$(targetFrame).append(content);
				$("#loading").hide();
			})
			.fail(function () {
				$.get('/error/404', function (data) {
					$('#center').html(data);
					$("#loading").hide();
				});
			});
		});
	});
}

function loadContent() {
//	getCurrentStatus();
	var currentPage = getCurrentPage();
	if (currentPage == false) return;
	generateMenu();
	generatePageContent(currentPage);
	if ((window.location.hash == '#/login/register/remember') || (window.location.hash == '#/login/register/reset') || (window.location.hash.indexOf("#/login/register/remember/") >= 0)) {
		window.location.hash = '#/';
	}
}

function initGeoComplete(field, map) {
	if ($(field).val().length)
		address = $(field).val();
	else
		address = "Paris, France";
	$(field).geocomplete({
		map: map,
		location: address,
		markerOptions: {
			draggable: false,
		},
	});
}

function initMapStations(elt) {
	$.getJSON('/map/stations.json', function(data) {
		var stations = [];
		$.each(data, function(id, station) {			
			stations.push({id: station.id, text: station.name });
		});

		$(elt).select2({
			multiple: true,
			minimumInputLength: 3,
			maximumSelectionSize: 3,
			tags: stations,
			width: 'element',
			createSearchChoice: function(param) { return null },
			placeholder: "Choisissez jusqu'à 3 stations"
		});
	});
}

function validationDialog(title, msg) {
	$('#dialog-message').attr('title', title);
	msg = msg.replace('ERROR: ', '<br/>');
	$('#dialog-message').html(msg); 
	$('#dialog-message').dialog({
		modal: true,
		minWidth: 300,
		maxWidth: 600,
		buttons: {
			Ok: function() {
				$( this ).dialog( "close" );
			}
		}
	});
}

function createNewSelectItem(term,data) {
    if ($(data).filter (function() {return this.text.localeCompare(term)===0;}).length===0) {
		return { id:term, text:term };
	} 
}

window.onhashchange = function(){
	loadContent();
}

$(function() {
	if (window.location.pathname.indexOf('/login/change-password') < 0)
		loadContent();
	generatePageContent('feedback');
	$('a').click(function (event){
		if ($(this).attr("href").charAt(0) == '#') {
	    	event.preventDefault();
	    	var href = $(this).attr("href");
	    	if (window.location.hash == href) {
	    		if (getCurrentParameters() == '')
    				href += '?rnd='+Math.random();
    			else
    				href += '&rnd='+Math.random();
	    	}
	    	window.location.hash = href;
    	}
	}); 
	$.maxlength.setDefaults($.maxlength.regionalOptions['fr']);

	var open = false;
	$('#footerSlideButton').click(function () {
		$('#questionning').slideToggle('slow');
		if(open === false) {
			$('#footerSlideContent').animate({ height: '430px' });
			$('#footerSlideContent').toggleClass('footerSlideContainer-visible');
			$(this).css('backgroundPosition', 'bottom left');
			open = true;
		} else {
			$('#footerSlideContent').animate({ height: '0px' });
			$('#footerSlideContent').toggleClass('footerSlideContainer-visible');
			$(this).css('backgroundPosition', 'top left');
			open = false;
		}
	});		

	$(".fancybox").fancybox({
		titlePosition  : 'over',
		helpers:  {
			thumbs: {
				width	: 50,
				height	: 50
			},
			title: {
				type: 'over'
			}
		}
	});

	fade($('#questionning'));

    $(document).on("select2-open", "select", function () {
        var el;
        $('.select2-results').each(function () {
            if ($(this).parent().css("display") != 'none') el = $(this);
            if (el === undefined) return;
            el.mCustomScrollbar({
				theme: "rounded-dark",
			});
        });
        $('.select2-with-searchbox').each(function () {
            if ($(this).parent().css("display") != 'none') el = $(this);
            if (el === undefined) return;
            el.mCustomScrollbar({
				theme: "rounded-dark",
			});
        });
        $('.select2-drop-multiple').each(function () {
            if ($(this).parent().css("display") != 'none') el = $(this);
            if (el === undefined) return;
            el.mCustomScrollbar({
				theme: "rounded-dark",
			});
        });
        $('.ui-widget-content').each(function () {
            if ($(this).parent().css("display") != 'none') el = $(this);
            if (el === undefined) return;
            el.mCustomScrollbar({
				theme: "rounded-dark",
			});
        });
	});
});

$(window).bind('scroll', function() {
	stretch_portal_content();
	$(window).resize( stretch_portal_content );
	if ($(window).scrollTop() > 40) {
		$('header').stop().animate({height: '40px'}, 300); //.css({'background-image': 'url(/img/logos/mv-texte-150x40.png)'});
		$('#notification').stop().animate({marginTop: '-10px'}, 300);
		$('#menu').stop().animate({top: '40px'}, 300);
		$('#loading').stop().animate({height: '40px'}, 300);
		$('#summary').stop().animate({top: '170px'}, 300);
	} else {
		$('header').stop().animate({height: '130px'}, 300); //.css({'background-image': 'url(/img/logos/mv-logo-248x130.png)'})
		$('#notification').stop().animate({marginTop: '80px'}, 300);
		$('#menu').stop().animate({top: '130px'}, 300);
		$('#loading').stop().animate({height: '130px'}, 300);
		$('#summary').stop().animate({top: '300px'}, 300);
	}
});

function stretch_portal_content() {
	if( $(window).height() > $('body').height() ) {
		$('#center').height($(window).height() - ( $('body').height() - $('#center').height()));
		$('#left').height($(window).height() - ( $('body').height() - $('#left').height()));
		$('#right').height($(window).height() - ( $('body').height() - $('#right').height()));
	}
}

function fade($ele) {
    $ele.fadeIn(6000).delay(3000).fadeOut(6000, function() {
        var $next = $(this).next('p');
        fade($next.length > 0 ? $next : $(this).parent().children().first());
   });
};