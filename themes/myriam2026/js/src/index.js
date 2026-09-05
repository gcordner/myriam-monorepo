// Remove height: 100% injected by the Vimeo player on week pages.
if ( document.body.classList.contains( 'single-class_week' ) ) {
	function fixVimeoHeight() {
		[ document.documentElement, document.body ].forEach( function ( el ) {
			if ( el.style.height === '100%' || el.style.overflow === 'hidden' ) {
				el.style.height = '';
				el.style.overflow = '';
			}
		} );
	}

	fixVimeoHeight();

	// Vimeo sets these after the player loads, so watch for it.
	var observer = new MutationObserver( fixVimeoHeight );
	observer.observe( document.documentElement, {
		attributes: true,
		attributeFilter: [ 'style' ],
		subtree: false,
	} );
	observer.observe( document.body, {
		attributes: true,
		attributeFilter: [ 'style' ],
		subtree: false,
	} );
}

// Keep --site-header-height in sync with the real, rendered header
// height. The header is position:fixed and its height varies by
// breakpoint (.inside-header's padding changes at 768px), so the
// .entry-header clearance margin in _layouts.scss reads this instead of
// a hardcoded guess. The CSS fallback already matches today's height, so
// in the normal case this only confirms it rather than visibly changing
// anything — see notes/layout.md.
( function () {
	var header = document.querySelector( '.site-header' );
	if ( ! header ) {
		return;
	}

	function setHeaderHeightVar() {
		var height = header.getBoundingClientRect().height;
		document.documentElement.style.setProperty( '--site-header-height', height + 'px' );
	}

	setHeaderHeightVar();
	window.addEventListener( 'resize', setHeaderHeightVar );

	if ( document.fonts && document.fonts.ready ) {
		document.fonts.ready.then( setHeaderHeightVar );
	}
} )();
