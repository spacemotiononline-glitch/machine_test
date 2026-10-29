/* Lifestyle Smiles – navigation toggle and two-step enquiry form. */
( function () {
	'use strict';

	document.documentElement.classList.add( 'js' );

	/* Mobile navigation */
	var toggle = document.querySelector( '.nav-toggle' );
	var panel = document.getElementById( 'primary-nav' );

	if ( toggle && panel ) {
		var setOpen = function ( open ) {
			toggle.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
			panel.classList.toggle( 'is-open', open );
		};

		toggle.addEventListener( 'click', function () {
			setOpen( toggle.getAttribute( 'aria-expanded' ) !== 'true' );
		} );

		panel.addEventListener( 'click', function ( e ) {
			if ( e.target.closest( 'a' ) ) {
				setOpen( false );
			}
		} );

		document.addEventListener( 'keydown', function ( e ) {
			if ( e.key === 'Escape' && toggle.getAttribute( 'aria-expanded' ) === 'true' ) {
				setOpen( false );
				toggle.focus();
			}
		} );
	}

	/* Two-step form */
	var form = document.getElementById( 'suitability-form' );
	if ( ! form ) {
		return;
	}

	var steps = form.querySelectorAll( '.form-step' );
	var cards = document.querySelectorAll( '[data-step-card]' );
	var actions = form.querySelector( '.form-actions' );
	var next = form.querySelector( '[data-form-next]' );
	var back = form.querySelector( '[data-form-back]' );
	var errorBox = document.getElementById( 'form-error' );
	var current = 0;

	function show( index ) {
		current = index;
		steps.forEach( function ( step, i ) {
			step.classList.toggle( 'is-current', i === index );
		} );
		cards.forEach( function ( card, i ) {
			card.classList.toggle( 'is-active', i === index );
		} );
		actions.classList.toggle( 'is-last', index === steps.length - 1 );
		back.hidden = index === 0;
		clearError();
	}

	function clearError() {
		errorBox.hidden = true;
		errorBox.textContent = '';
		form.querySelectorAll( '.is-invalid' ).forEach( function ( el ) {
			el.classList.remove( 'is-invalid' );
			el.removeAttribute( 'aria-invalid' );
		} );
	}

	function fail( message, field ) {
		errorBox.textContent = message;
		errorBox.hidden = false;
		if ( field ) {
			field.classList.add( 'is-invalid' );
			field.setAttribute( 'aria-invalid', 'true' );
			field.focus();
		}
		return false;
	}

	function validateStep( index ) {
		clearError();
		var scope = steps[ index ];
		var fields = scope.querySelectorAll( 'input[required]' );
		var radiosChecked = false;

		for ( var i = 0; i < fields.length; i++ ) {
			var f = fields[ i ];

			if ( f.type === 'radio' ) {
				radiosChecked = scope.querySelector( 'input[name="' + f.name + '"]:checked' );
				if ( ! radiosChecked ) {
					return fail( 'Please select a gender option.', f );
				}
				continue;
			}

			if ( f.type === 'file' ) {
				if ( ! f.files.length ) {
					return fail( 'Please upload all four smile photos.', f );
				}
				if ( f.files[ 0 ].size > 5 * 1024 * 1024 ) {
					return fail( 'Each photo must be 5 MB or smaller.', f );
				}
				if ( ! /^image\/(jpeg|png|webp)$/.test( f.files[ 0 ].type ) ) {
					return fail( 'Photos must be JPG, PNG or WebP images.', f );
				}
				continue;
			}

			if ( ! f.value.trim() ) {
				return fail( 'Please complete all required fields.', f );
			}
			if ( f.type === 'email' && ! f.checkValidity() ) {
				return fail( 'Please enter a valid email address.', f );
			}
			if ( f.type === 'tel' && ! /^\d{6,12}$/.test( f.value.replace( /[\s\-()]/g, '' ) ) ) {
				return fail( 'Please enter a valid phone number.', f );
			}
			if ( f.type === 'date' && ! f.checkValidity() ) {
				return fail( 'Please enter a valid date of birth.', f );
			}
		}
		return true;
	}

	next.addEventListener( 'click', function () {
		if ( validateStep( current ) ) {
			show( current + 1 );
			var first = steps[ current ].querySelector( 'input' );
			if ( first ) {
				first.focus();
			}
		}
	} );

	back.addEventListener( 'click', function () {
		show( current - 1 );
	} );

	form.addEventListener( 'submit', function ( e ) {
		for ( var i = 0; i < steps.length; i++ ) {
			if ( ! validateStep( i ) ) {
				e.preventDefault();
				show( i );
				validateStep( i );
				return;
			}
		}
	} );

	show( 0 );
}() );
