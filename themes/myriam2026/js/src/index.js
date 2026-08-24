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
