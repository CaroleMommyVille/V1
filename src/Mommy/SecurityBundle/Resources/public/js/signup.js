function manageRegistrationError(data) {
	if (typeof data.code != 'undefined') {
		switch (data.code) {
			case 'ERR_THANKS':
				$("tfoot").html();
				var msg = '<p><span class="ui-icon ui-icon-circle-check" style="float:left; margin:0 7px 50px 0;"></span>' + data.data + '</p>';
				validationDialog(data.title, msg);
				break;
			case 'ERR_OK':
				$("input[type=submit]").removeAttr("disabled");
				break;
			default:
				$.get('/error/'+data.code+'.json', function(error) { 	
					var msg = '<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 50px 0;"></span>' + data.msg;
					if (data.code == 'ERR_MUST_ANSWER') {
						msg += ' : ' + data.data;
					}
					msg += '</p>';
					validationDialog(data.title, msg);
				});
				break;
		}
	}
	$("input[type=submit]").removeAttr("disabled");
	$("#loading").hide();
}

function manageRegistrationForm() {
	$(".birthday").datepicker({
		showOn: 'both',
		changeMonth: true,
		changeYear: true,
		showOtherMonths: true,
		yearRange: '-61:-14',
		defaultDate: '-18y',
		showButtonPanel: true,
		dateFormat: 'dd-mm-yy',
		buttonImage: "/img/datepicker.gif",
		buttonImageOnly: true,
		autoClose: false,
		onChangeMonthYear: function(year, month, datePicker) {
		    // Prevent forbidden dates, like 2/31/2012:
		    var $t = $(this);
		    var day = $t.data('preferred-day') || new Date().getDate();
		    var newDate = new Date(month + '/' + day + '/' + year);
		    while (day > 28) {
		      if (newDate.getDate() == day && newDate.getMonth() + 1 == month && newDate.getFullYear() == year) {
		        break;
		      } else {
		        day -= 1;
		        newDate = new Date(month + '/' + day + '/' + year);
		      }
		    }   
		    $t.datepicker('setDate', newDate);
		  },  
		    
		  beforeShow: function(dateText, datePicker) {
		    // Record the starting date so that
		    // if the user changes months from Jan->Feb->Mar
		    // and the original date was 1/31,
		    // then we go 1/31->2/28->3/31.
		    $(this).data('preferred-day', ($(this).datepicker('getDate') || new Date()).getDate());
//		  }
//		beforeShow: function(dateText, datePicker) {
   			$('#ui-datepicker-div')[ $(dateText).is('[data-calendar="false"]') ? 'addClass' : 'removeClass' ]('hide-calendar');
  		},
   	});
	$(".birthday-child").datepicker({
		showOn: 'both',
		changeMonth: true,
		changeYear: true,
		showOtherMonths: true,
		yearRange: '-61:+1',
		defaultDate: '-3y',
		showButtonPanel: true,
		dateFormat: 'dd-mm-yy',
		buttonImage: "/img/datepicker.gif",
		buttonImageOnly: true,
		autoClose: false,
		onChangeMonthYear: function(year, month, datePicker) {
		    // Prevent forbidden dates, like 2/31/2012:
		    var $t = $(this);
		    var day = $t.data('preferred-day') || new Date().getDate();
		    var newDate = new Date(month + '/' + day + '/' + year);
		    while (day > 28) {
		      if (newDate.getDate() == day && newDate.getMonth() + 1 == month && newDate.getFullYear() == year) {
		        break;
		      } else {
		        day -= 1;
		        newDate = new Date(month + '/' + day + '/' + year);
		      }
		    }   
		    $t.datepicker('setDate', newDate);
		  },  
		    
		  beforeShow: function(dateText, datePicker) {
		    // Record the starting date so that
		    // if the user changes months from Jan->Feb->Mar
		    // and the original date was 1/31,
		    // then we go 1/31->2/28->3/31.
		    $(this).data('preferred-day', ($(this).datepicker('getDate') || new Date()).getDate());
//  }
//  		beforeShow: function(dateText, datePicker) {
   			$('#ui-datepicker-div')[ $(dateText).is('[data-calendar="false"]') ? 'addClass' : 'removeClass' ]('hide-calendar');
  		},
		onSelect: function(dateStr, inst) {
			var date = $(this).datepicker('getDate');
			var today = new Date();
			if (date.getFullYear() + 3 > today.getFullYear()) {
				$('.baby').show();
				$('.young').hide();
			} else {
				$('.baby').hide();
				$('.young').show();
			}
		}
   	});
	$(".date-delivery").datepicker({
		showOn: 'both',
		changeMonth: true,
		changeYear: true,
		showOtherMonths: true,
		minDate: '0',
		maxDate: '+10m',
		defaultDate: '0',
		showButtonPanel: true,
		dateFormat: 'dd-mm-yy',
		buttonImage: "/img/datepicker.gif",
		buttonImageOnly: true,
		autoClose: false,
		onChangeMonthYear: function(year, month, datePicker) {
		    // Prevent forbidden dates, like 2/31/2012:
		    var $t = $(this);
		    var day = $t.data('preferred-day') || new Date().getDate();
		    var newDate = new Date(month + '/' + day + '/' + year);
		    while (day > 28) {
		      if (newDate.getDate() == day && newDate.getMonth() + 1 == month && newDate.getFullYear() == year) {
		        break;
		      } else {
		        day -= 1;
		        newDate = new Date(month + '/' + day + '/' + year);
		      }
		    }   
		    $t.datepicker('setDate', newDate);
		  },  
		    
		  beforeShow: function(dateText, datePicker) {
		    // Record the starting date so that
		    // if the user changes months from Jan->Feb->Mar
		    // and the original date was 1/31,
		    // then we go 1/31->2/28->3/31.
		    $(this).data('preferred-day', ($(this).datepicker('getDate') || new Date()).getDate());
//		  }
//  		beforeShow: function(dateText, datePicker) {
   			$('#ui-datepicker-div')[ $(dateText).is('[data-calendar="false"]') ? 'addClass' : 'removeClass' ]('hide-calendar');
  		},
		onSelect: function(dateStr, inst) {
			var date = $(this).datepicker('getDate');
			var diff = 43 - $.datepicker.iso8601Week(date) + $.datepicker.iso8601Week(new Date());
			var str = date.getFullYear() + '-' + (date.getMonth()+1) + '-' + date.getDate();
			if ($("#enceinte_amenorrhee").length > 0) {
				$("#enceinte_amenorrhee").select2().select2("val", diff);
			}
			if ($("#pma_amenorrhee").length > 0) {
				$("#pma_amenorrhee").select2().select2("val", diff);
			}
			date.setDate(date.getDate() + 4);
			var str = date.getFullYear() + '-' + (date.getMonth()+1) + '-' + date.getDate();
			$('.date-delivery-to').val(str);
		}
	});
	$('.date-without-day').datepicker( {
		showOn: 'both',
        changeMonth: true,
        changeYear: true,
        showButtonPanel: true,
        dateFormat: 'mm-yy',
		maxDate: '0',
		buttonImage: "/img/datepicker.gif",
		buttonImageOnly: true,
		autoClose: false,
		onChangeMonthYear: function(year, month, datePicker) {
		    // Prevent forbidden dates, like 2/31/2012:
		    var $t = $(this);
		    var day = $t.data('preferred-day') || new Date().getDate();
		    var newDate = new Date(month + '/' + day + '/' + year);
		    while (day > 28) {
		      if (newDate.getDate() == day && newDate.getMonth() + 1 == month && newDate.getFullYear() == year) {
		        break;
		      } else {
		        day -= 1;
		        newDate = new Date(month + '/' + day + '/' + year);
		      }
		    }   
		    $t.datepicker('setDate', newDate);
		  },  
		    
		  beforeShow: function(dateText, datePicker) {
		    // Record the starting date so that
		    // if the user changes months from Jan->Feb->Mar
		    // and the original date was 1/31,
		    // then we go 1/31->2/28->3/31.
		    $(this).data('preferred-day', ($(this).datepicker('getDate') || new Date()).getDate());
//		  }
//  		beforeShow: function(dateText, datePicker) {
   			$('#ui-datepicker-div')[ $(el).is('[data-calendar="false"]') ? 'addClass' : 'removeClass' ]('hide-calendar');
  		},
        onClose: function(dateText, inst) { 
            var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
            var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
            $(this).datepicker('setDate', new Date(year, month, 1));
        }
    });
	$('.date-without-day-mamange').datepicker( {
		showOn: 'both',
        changeMonth: true,
        changeYear: true,
        showButtonPanel: true,
        dateFormat: 'mm-yy',
		maxDate: '0',
		minDate: '-8y',
		buttonImage: "/img/datepicker.gif",
		buttonImageOnly: true,
		autoClose: false,
		onChangeMonthYear: function(year, month, datePicker) {
		    // Prevent forbidden dates, like 2/31/2012:
		    var $t = $(this);
		    var day = $t.data('preferred-day') || new Date().getDate();
		    var newDate = new Date(month + '/' + day + '/' + year);
		    while (day > 28) {
		      if (newDate.getDate() == day && newDate.getMonth() + 1 == month && newDate.getFullYear() == year) {
		        break;
		      } else {
		        day -= 1;
		        newDate = new Date(month + '/' + day + '/' + year);
		      }
		    }   
		    $t.datepicker('setDate', newDate);
		  },  
		    
		  beforeShow: function(dateText, datePicker) {
		    // Record the starting date so that
		    // if the user changes months from Jan->Feb->Mar
		    // and the original date was 1/31,
		    // then we go 1/31->2/28->3/31.
		    $(this).data('preferred-day', ($(this).datepicker('getDate') || new Date()).getDate());
//		  }
//  		beforeShow: function(dateText, datePicker) {
   			$('#ui-datepicker-div')[ $(el).is('[data-calendar="false"]') ? 'addClass' : 'removeClass' ]('hide-calendar');
  		},
        onClose: function(dateText, inst) { 
            var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
            var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
            $(this).datepicker('setDate', new Date(year, month, 1));
        }
    });
	$('#registration').submit(function (event) {
		event.preventDefault();
		$("input[type=submit]").attr("disabled", "disabled");
		$("#loading").show();
		$.ajax({
			url: '/login/register',
			type: 'POST',
			data: $(this).serialize(),
		})
		.done(function (data) {
			$("#center").empty().append(data);
			$("#loading").hide();
		})
		.fail(function (xhr, status, error) {
console.log(xhr.responseText);
			manageRegistrationError(error);
		});
	});
}

$(function() {
	$.datepicker._selectDateOverload = $.datepicker._selectDate;
	$.datepicker._selectDate = function(id, dateStr) {
	    var target = $(id);
	    var inst = this._getInst(target[0]);
	    inst.inline = true;
	    $.datepicker._selectDateOverload(id, dateStr);
	    inst.inline = false;
	    this._updateDatepicker(inst);
	}
	
	manageRegistrationForm();
	$('form#login').submit(function (event) {
		$("#loading").show();
		event.preventDefault();
		$.ajax({
			url: $('form#login').attr('action'),
			type: $('form#login').attr('method'),
			data: $('form#login').serialize(),
			statusCode: {
				403: function() {
					$("#loading").hide();
					validationDialog('Erreur lors de la connexion', 'Votre identifiant ou votre mot de passe semble invalide');
				},
			},
		})
		.done(function (data) {
			$("#loading").hide();
			if (data.success) {
				loadContent();
			} else {
				validationDialog('Erreur lors de la connexion', 'Votre identifiant ou votre mot de passe semble invalide');
			}
		});
	})
});