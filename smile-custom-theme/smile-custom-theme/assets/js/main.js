/* Lifestyle Smiles – navigation toggle, story dots and multi-step enquiry form. */
( function () {
	'use strict';

	/* The "js" class is added inline in <head> (functions.php) so the layout never flashes. */

	/* ---------- Mobile navigation ---------- */
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

	/* ---------- Patient story dots ---------- */
	var track = document.querySelector( '.stories__track' );
	var dots = document.querySelectorAll( '[data-story-dot]' );

	if ( track && dots.length ) {
		var cards = track.querySelectorAll( '.story-card' );
		var ticking = false;

		var currentStory = function () {
			var best = 0;
			var bestDist = Infinity;
			cards.forEach( function ( card, i ) {
				var dist = Math.abs( card.offsetLeft - track.offsetLeft - track.scrollLeft );
				if ( dist < bestDist ) {
					bestDist = dist;
					best = i;
				}
			} );
			return best;
		};

		var syncDots = function () {
			var active = currentStory();
			dots.forEach( function ( dot, i ) {
				dot.setAttribute( 'aria-current', i === active ? 'true' : 'false' );
			} );
			ticking = false;
		};

		track.addEventListener( 'scroll', function () {
			if ( ! ticking ) {
				ticking = true;
				window.requestAnimationFrame( syncDots );
			}
		}, { passive: true } );

		dots.forEach( function ( dot, i ) {
			dot.addEventListener( 'click', function () {
				var reduce = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
				track.scrollTo( {
					left: cards[ i ].offsetLeft - track.offsetLeft,
					behavior: reduce ? 'auto' : 'smooth'
				} );
			} );
		} );
	}

	/* ---------- Multi-step form ---------- */
	var form = document.getElementById( 'suitability-form' );
	if ( ! form ) {
		return;
	}

	var panels = form.querySelectorAll( '.form-panel' );
	var stepCards = document.querySelectorAll( '[data-step-card]' );
	var actions = form.querySelector( '.form-actions' );
	var next = form.querySelector( '[data-form-next]' );
	var back = form.querySelector( '[data-form-back]' );
	var submit = form.querySelector( '[data-form-submit]' );
	var errorBox = document.getElementById( 'form-error' );
	var current = 0;
	var MAX_BYTES = 5 * 1024 * 1024;

	function isPhotoPanel( p ) {
		return p.getAttribute( 'data-panel' ) === 'photo';
	}

	function photoChosen( p ) {
		var input = p.querySelector( 'input[type="file"]' );
		return !! ( input && input.files.length );
	}

	function syncButtons() {
		var p = panels[ current ];
		var blocked = isPhotoPanel( p ) && ! photoChosen( p );
		next.disabled = blocked;
		submit.disabled = blocked;
	}

	function clearError() {
		errorBox.hidden = true;
		errorBox.textContent = '';
		form.querySelectorAll( '.is-invalid' ).forEach( function ( el ) {
			el.classList.remove( 'is-invalid' );
			el.removeAttribute( 'aria-invalid' );
		} );
	}

	function show( index ) {
		current = index;
		var stepNo = panels[ index ].getAttribute( 'data-step' );

		panels.forEach( function ( p, i ) {
			p.classList.toggle( 'is-current', i === index );
		} );
		stepCards.forEach( function ( card ) {
			card.classList.toggle( 'is-active', card.getAttribute( 'data-step-card' ) === stepNo );
		} );
		actions.classList.toggle( 'is-last', index === panels.length - 1 );
		back.hidden = index === 0;
		clearError();
		syncButtons();
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

	function checkFile( input ) {
		var file = input.files[ 0 ];
		if ( ! file ) {
			return fail( 'Please upload this photo to continue.', input );
		}
		if ( file.size > MAX_BYTES ) {
			return fail( 'Each photo must be 5 MB or smaller.', input );
		}
		if ( ! /^image\/(jpeg|png|webp)$/.test( file.type ) ) {
			return fail( 'Photos must be JPG, PNG or WebP images.', input );
		}
		return true;
	}

	function validatePanel( p ) {
		clearError();
		var kind = p.getAttribute( 'data-panel' );

		if ( kind === 'photo' ) {
			return checkFile( p.querySelector( 'input[type="file"]' ) );
		}

		if ( kind === 'concerns' ) {
			if ( ! p.querySelector( 'input[name="concerns[]"]:checked' ) ) {
				return fail( 'Please select at least one concern.', p.querySelector( 'input[name="concerns[]"]' ) );
			}
			var urgency = p.querySelector( '#urgency' );
			if ( ! urgency.value ) {
				return fail( 'Please select your urgency.', urgency );
			}
			return true;
		}

		var fields = p.querySelectorAll( 'input[required]' );
		for ( var i = 0; i < fields.length; i++ ) {
			var f = fields[ i ];

			if ( f.type === 'radio' ) {
				if ( ! p.querySelector( 'input[name="' + f.name + '"]:checked' ) ) {
					return fail( 'Please select a gender option.', f );
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

	/* Show the chosen file name in each upload tile and enable Next. */
	panels.forEach( function ( p ) {
		var input = p.querySelector( 'input[type="file"]' );
		if ( ! input ) {
			return;
		}
		input.addEventListener( 'change', function () {
			var tile = p.querySelector( '.upload' );
			var label = p.querySelector( '.upload__file' );
			clearError();

			if ( input.files.length && ! checkFile( input ) ) {
				input.value = '';
				tile.classList.remove( 'has-file' );
				label.textContent = '';
			} else if ( input.files.length ) {
				tile.classList.add( 'has-file' );
				label.textContent = input.files[ 0 ].name;
			} else {
				tile.classList.remove( 'has-file' );
				label.textContent = '';
			}
			syncButtons();
		} );
	} );

	next.addEventListener( 'click', function () {
		if ( validatePanel( panels[ current ] ) ) {
			show( current + 1 );
			var first = panels[ current ].querySelector( 'input, select' );
			if ( first && first.type !== 'file' ) {
				first.focus();
			}
		}
	} );

	back.addEventListener( 'click', function () {
		show( current - 1 );
	} );

	form.addEventListener( 'submit', function ( e ) {
		for ( var i = 0; i < panels.length; i++ ) {
			if ( ! validatePanel( panels[ i ] ) ) {
				e.preventDefault();
				show( i );
				validatePanel( panels[ i ] );
				return;
			}
		}
	} );

	show( 0 );
}() );
