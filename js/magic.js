// magic.js
$(document).ready(function() {

	// process the form
	$('form').submit(function(event) {
    var yourForm = $(this);
    var id = $(this).find('input[type=hidden]').val();
console.log('Submitting form'+id);
//		$this.find('.form-group').removeClass('has-error'); // remove the error class
//		$this.find('.help-block').remove(); // remove the error text

		// get the form data
		// there are many ways to get this data using jQuery (you can use the class or id also)
		var formData = {
			'name' 				: $(this).find('input[name=name]').val(),
            'ID'                : $(this).find('input[name=ID]').val(),
			'desc' 		    	: $(this).find('input[name=desc]').val(),
			'tags' 	            : $(this).find('input[name=tags]').val()
		};

		// process the form
		$.ajax({
			type 		: 'POST', // define the type of HTTP verb we want to use (POST for our form)
			url 		: 'process.php', // the url where we want to POST
			data 		: formData, // our data object
			dataType 	: 'json', // what type of data do we expect back from the server
			encode 		: true
		})
			// using the done promise callback
			.done(function(data) {

				// log data to the console so we can see
				//console.log(data); 

				// here we will handle errors and validation messages
				if (!data.success) {
					
					// handle errors for name ---------------
					if (data.errors.name) {
						$('#name-group').addClass('has-error'); // add the error class to show red input
						$('#name-group').append('<div class="help-block">' + data.errors.name + '</div>'); // add the actual error message under our input
					}

					// handle errors for email ---------------
					if (data.errors.email) {
						$('#email-group').addClass('has-error'); // add the error class to show red input
						$('#email-group').append('<div class="help-block">' + data.errors.email + '</div>'); // add the actual error message under our input
					}

					// handle errors for superhero alias ---------------
					if (data.errors.tags) {
						$('#tags-group').addClass('has-error'); // add the error class to show red input
						$('#tags-group').append('<div class="help-block">' + data.errors.tags + '</div>'); // add the actual error message under our input
					}
                    $('form[name=form'+data.ID+']').append('<div class="alert" style="background-color:red;">' + data.errors.sql + '</div>');
                    console.log('error');


				} else {

					// ALL GOOD! just show the success message!
                    $('form[name=form'+data.ID+'] .btn').first().hide();
					$('form[name=form'+data.ID+']').append('<div class="alert alert-success">' + data.message + '</div>');

					// usually after form submission, you'll want to redirect
					// window.location = '/thank-you'; // redirect a user to another page

				}
			})

			// using the fail promise callback
			.fail(function(data) {

				// show any errors
				// best to remove for production
				console.log(data);
			});

		// stop the form from submitting the normal way and refreshing the page
		event.preventDefault();
	});

});
